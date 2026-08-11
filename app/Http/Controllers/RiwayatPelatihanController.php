<?php

namespace App\Http\Controllers;

use App\Models\Fasilitator;
use App\Models\RiwayatPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiwayatPelatihanController extends Controller
{
    public function create(Fasilitator $fasilitator, Request $request)
    {
        $kategori = $request->query('kategori', 'materi_diajarkan');

        return view('riwayat-pelatihan.create', compact('fasilitator', 'kategori'));
    }

    public function store(Request $request, Fasilitator $fasilitator)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:materi_diajarkan,pelatihan_terkait,pengalaman_mengajar',
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('sertifikat')) {
            $validated['sertifikat'] = $request->file('sertifikat')->store('riwayat-pelatihan/sertifikat', 'public');
        }

        $fasilitator->riwayatPelatihan()->create($validated);

        return redirect()->route('fasilitator.show', $fasilitator)
            ->with('success', 'Riwayat pelatihan berhasil ditambahkan.');
    }

    public function edit(RiwayatPelatihan $riwayatPelatihan)
    {
        return view('riwayat-pelatihan.edit', compact('riwayatPelatihan'));
    }

    public function update(Request $request, RiwayatPelatihan $riwayatPelatihan)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:materi_diajarkan,pelatihan_terkait,pengalaman_mengajar',
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('sertifikat')) {
            if ($riwayatPelatihan->sertifikat) {
                Storage::disk('public')->delete($riwayatPelatihan->sertifikat);
            }
            $validated['sertifikat'] = $request->file('sertifikat')->store('riwayat-pelatihan/sertifikat', 'public');
        }

        $riwayatPelatihan->update($validated);

        return redirect()->route('fasilitator.show', $riwayatPelatihan->fasilitator_id)
            ->with('success', 'Riwayat pelatihan berhasil diperbarui.');
    }

    public function destroy(RiwayatPelatihan $riwayatPelatihan)
    {
        $fasilitatorId = $riwayatPelatihan->fasilitator_id;

        if ($riwayatPelatihan->sertifikat) {
            Storage::disk('public')->delete($riwayatPelatihan->sertifikat);
        }

        $riwayatPelatihan->delete();

        return redirect()->route('fasilitator.show', $fasilitatorId)
            ->with('success', 'Riwayat pelatihan berhasil dihapus.');
    }
}