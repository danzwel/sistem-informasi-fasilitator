<?php

namespace App\Http\Controllers;

use App\Models\Fasilitator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class FasilitatorController extends Controller
{
    public function index()
    {
        $fasilitators = Fasilitator::latest()->paginate(10);
        return view('fasilitator.index', compact('fasilitators'));
    }

    public function create()
    {
        return view('fasilitator.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'gelar' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:25',
            'pangkat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'alamat_kantor' => 'nullable|string',
            'alamat_rumah' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('fasilitator/foto', 'public');
        }

        if ($request->hasFile('ttd')) {
            $validated['ttd'] = $request->file('ttd')->store('fasilitator/ttd', 'public');
        }

        Fasilitator::create($validated);

        return redirect()->route('fasilitator.index')
            ->with('success', 'Data fasilitator berhasil ditambahkan.');
    }

    public function show(Fasilitator $fasilitator)
    {
        $riwayatPelatihan = $fasilitator->riwayatPelatihan()->latest('tanggal')->get();
        $riwayatPendidikan = $fasilitator->riwayatPendidikan()->latest('tahun_selesai')->get();

        $materiDiajarkan = $riwayatPelatihan->where('kategori', 'materi_diajarkan');
        $pelatihanTerkait = $riwayatPelatihan->where('kategori', 'pelatihan_terkait');
        $pengalamanMengajar = $riwayatPelatihan->where('kategori', 'pengalaman_mengajar');

        return view('fasilitator.show', compact(
            'fasilitator',
            'riwayatPendidikan',
            'materiDiajarkan',
            'pelatihanTerkait',
            'pengalamanMengajar'
        ));
    }

    public function edit(Fasilitator $fasilitator)
    {
        return view('fasilitator.edit', compact('fasilitator'));
    }

    public function update(Request $request, Fasilitator $fasilitator)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'gelar' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:25',
            'pangkat' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:255',
            'alamat_kantor' => 'nullable|string',
            'alamat_rumah' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($fasilitator->foto) {
                Storage::disk('public')->delete($fasilitator->foto);
            }
            $validated['foto'] = $request->file('foto')->store('fasilitator/foto', 'public');
        }

        if ($request->hasFile('ttd')) {
            if ($fasilitator->ttd) {
                Storage::disk('public')->delete($fasilitator->ttd);
            }
            $validated['ttd'] = $request->file('ttd')->store('fasilitator/ttd', 'public');
        }

        $fasilitator->update($validated);

        return redirect()->route('fasilitator.index')
            ->with('success', 'Data fasilitator berhasil diperbarui.');
    }

    public function destroy(Fasilitator $fasilitator)
    {
        $fasilitator->delete();

        return redirect()->route('fasilitator.index')
            ->with('success', 'Data fasilitator berhasil dihapus.');
    }

    private function dataCv(Fasilitator $fasilitator, bool $forPdf = false)
    {
        $riwayatPelatihan = $fasilitator->riwayatPelatihan()->latest('tanggal')->get();
        $riwayatPendidikan = $fasilitator->riwayatPendidikan()->orderBy('tahun_mulai')->get();

        return [
            'fasilitator' => $fasilitator,
            'riwayatPendidikan' => $riwayatPendidikan,
            'materiDiajarkan' => $riwayatPelatihan->where('kategori', 'materi_diajarkan'),
            'pelatihanTerkait' => $riwayatPelatihan->where('kategori', 'pelatihan_terkait'),
            'pengalamanMengajar' => $riwayatPelatihan->where('kategori', 'pengalaman_mengajar'),
            'forPdf' => $forPdf,
        ];
    }

    public function cv(Fasilitator $fasilitator)
    {
        return view('fasilitator.cv', $this->dataCv($fasilitator, false));
    }

    public function cvPdf(Fasilitator $fasilitator)
    {
        $pdf = Pdf::loadView('fasilitator.cv', $this->dataCv($fasilitator, true))
            ->setPaper('a4', 'portrait');

        $namaFile = 'CV - ' . $fasilitator->nama . '.pdf';

        return $pdf->download($namaFile);
    }
}
