<?php

namespace App\Imports;

use App\Models\Fasilitator;
use App\Models\RiwayatPelatihan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class PelatihanImport implements ToCollection
{
    public array $ringkasan = [
        'baris_diproses' => 0,
        'fasilitator_baru' => 0,
        'fasilitator_lama' => 0,
        'pelatihan_ditambahkan' => 0,
        'baris_dilewati' => 0,
        'baris_dikecualikan' => 0,
        'dikecualikan_detail' => [],
        'valid' => [],
        'bermasalah' => [],
    ];

    protected bool $simulasiSaja;
    protected array $overrideNama;
    protected array $overridePelatihan;
    protected array $overrideMateri;
    protected array $dikecualikan;

    public function __construct(
        bool $simulasiSaja = true,
        array $overrideNama = [],
        array $overridePelatihan = [],
        array $overrideMateri = [],
        array $dikecualikan = []
    ) {
        $this->simulasiSaja = $simulasiSaja;
        $this->overrideNama = $overrideNama;
        $this->overridePelatihan = $overridePelatihan;
        $this->overrideMateri = $overrideMateri;
        $this->dikecualikan = $dikecualikan;
    }

    private function pilihNilai(array $override, $nomorBaris, $asli)
    {
        $dariOverride = $this->normalisasiTeks($override[$nomorBaris] ?? '');
        if ($dariOverride !== '') {
            return $dariOverride;
        }
        return $this->normalisasiTeks($asli);
    }

    private function normalisasiTeks($nilai): string
    {
        return preg_replace('/\s+/', ' ', trim((string) $nilai)) ?? '';
    }

    public function collection(Collection $rows)
    {
        $tahun = null;
        $bulan = null;
        $namaPelatihan = null;

        $dataRows = $rows->slice(2);

        foreach ($dataRows as $index => $row) {
            $nomorBaris = $index + 3;

            $colTahun = $row[1] ?? null;
            $colBulan = $row[2] ?? null;
            $colNamaPelatihan = $row[3] ?? null;
            $colMateri = $row[4] ?? null;
            $colNamaFasilitator = $row[5] ?? null;

            if (!empty($colTahun)) $tahun = $colTahun;
            if (!empty($colBulan)) $bulan = $colBulan;
            if (!empty($colNamaPelatihan)) $namaPelatihan = $colNamaPelatihan;

            $namaFasilitatorFinal = $this->pilihNilai($this->overrideNama, $nomorBaris, $colNamaFasilitator);
            $namaPelatihanFinal = $this->pilihNilai($this->overridePelatihan, $nomorBaris, $namaPelatihan);
            $materiFinal = $this->pilihNilai($this->overrideMateri, $nomorBaris, $colMateri);

            // Baris kosong total dari awal (belum ada data sama sekali, belum diedit admin juga), lewati diam-diam
            if ($namaFasilitatorFinal === '' && $materiFinal === '' && $namaPelatihanFinal === '' && !isset($this->overrideNama[$nomorBaris])) {
                continue;
            }

            // Admin sengaja uncheck / hapus baris ini
            if (in_array($nomorBaris, $this->dikecualikan)) {
                $this->ringkasan['baris_dikecualikan']++;
                $this->ringkasan['dikecualikan_detail'][] = [
                    'baris' => $nomorBaris,
                    'nama_fasilitator' => $namaFasilitatorFinal,
                    'nama_pelatihan' => $namaPelatihanFinal,
                    'materi' => $materiFinal,
                ];
                continue;
            }

            $fieldKosong = [];
            if ($namaFasilitatorFinal === '') $fieldKosong[] = 'nama_fasilitator';
            if ($namaPelatihanFinal === '') $fieldKosong[] = 'nama_pelatihan';

            if (!empty($fieldKosong)) {
                $this->ringkasan['baris_dilewati']++;
                $this->ringkasan['bermasalah'][] = [
                    'baris' => $nomorBaris,
                    'field_kosong' => $fieldKosong,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'nama_pelatihan' => $namaPelatihanFinal,
                    'materi' => $materiFinal,
                    'nama_fasilitator' => $namaFasilitatorFinal,
                ];
                continue;
            }

            $this->ringkasan['baris_diproses']++;

            $fasilitator = Fasilitator::whereRaw('LOWER(nama) = ?', [strtolower($namaFasilitatorFinal)])->first();
            $statusFasilitator = 'baru';

            if (!$fasilitator) {
                if (!$this->simulasiSaja) {
                    $fasilitator = Fasilitator::create([
                        'nama' => $namaFasilitatorFinal,
                        'status' => 'aktif',
                    ]);
                }
                $this->ringkasan['fasilitator_baru']++;
            } else {
                $statusFasilitator = 'lama';
                $this->ringkasan['fasilitator_lama']++;
            }

            $keterangan = $this->buatKeterangan($materiFinal, $bulan, $tahun);

            $this->ringkasan['valid'][] = [
                'baris' => $nomorBaris,
                'nama_fasilitator' => $namaFasilitatorFinal,
                'status_fasilitator' => $statusFasilitator,
                'nama_pelatihan' => $namaPelatihanFinal,
                'materi' => $materiFinal,
                'tahun' => $tahun,
                'bulan' => $bulan,
            ];

            if (!$this->simulasiSaja && $fasilitator) {
                $sudahAda = RiwayatPelatihan::where('fasilitator_id', $fasilitator->id)
                    ->where('nama_kegiatan', $namaPelatihanFinal)
                    ->where('keterangan', $keterangan)
                    ->exists();

                if (!$sudahAda) {
                    RiwayatPelatihan::create([
                        'fasilitator_id' => $fasilitator->id,
                        'kategori' => 'materi_diajarkan',
                        'nama_kegiatan' => $namaPelatihanFinal,
                        'tanggal' => null,
                        'keterangan' => $keterangan,
                    ]);
                    $this->ringkasan['pelatihan_ditambahkan']++;
                }
            } else {
                $this->ringkasan['pelatihan_ditambahkan']++;
            }
        }
    }

    private function buatKeterangan($materi, $bulan, $tahun): ?string
    {
        $bagian = [];

        if ($materi !== '') {
            $bagian[] = 'Materi: ' . $materi;
        }

        $periode = trim(implode(' ', array_filter([$bulan, $tahun], fn ($nilai) => !empty($nilai))));
        if ($periode !== '') {
            $bagian[] = 'Periode: ' . $periode;
        }

        return $bagian ? implode("\n", $bagian) : null;
    }
}
