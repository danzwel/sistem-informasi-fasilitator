<?php

namespace App\Imports;

use App\Models\Fasilitator;
use App\Models\RiwayatPelatihan;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PelatihanImport implements ToCollection
{
    public array $ringkasan = [
        'baris_diproses' => 0,
        'fasilitator_baru' => 0,
        'fasilitator_lama' => 0,
        'pelatihan_ditambahkan' => 0,
        'baris_dilewati' => 0,
        'baris_dikecualikan' => 0,
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
        $dariOverride = trim($override[$nomorBaris] ?? '');
        if ($dariOverride !== '') {
            return $dariOverride;
        }
        return $asli ? trim($asli) : '';
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

            $tanggal = $this->parseTanggal($tahun, $bulan);

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
                    ->where('keterangan', $materiFinal)
                    ->exists();

                if (!$sudahAda) {
                    RiwayatPelatihan::create([
                        'fasilitator_id' => $fasilitator->id,
                        'kategori' => 'materi_diajarkan',
                        'nama_kegiatan' => $namaPelatihanFinal,
                        'tanggal' => $tanggal,
                        'keterangan' => $materiFinal ?: null,
                    ]);
                    $this->ringkasan['pelatihan_ditambahkan']++;
                }
            } else {
                $this->ringkasan['pelatihan_ditambahkan']++;
            }
        }
    }

    private function parseTanggal($tahun, $bulan)
    {
        if (empty($tahun) || empty($bulan)) {
            return null;
        }

        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        $bulanAngka = $bulanMap[strtolower(trim($bulan))] ?? 1;

        try {
            return Carbon::createFromDate((int) $tahun, $bulanAngka, 1)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}