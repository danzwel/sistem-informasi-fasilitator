<?php

namespace App\Http\Controllers;

use App\Models\Fasilitator;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;

class RiwayatPendidikanController extends Controller
{
    public function create(Fasilitator $fasilitator)
    {
        return view('riwayat-pendidikan.create', compact('fasilitator'));
    }

    public function store(Request $request, Fasilitator $fasilitator)
    {
        $validated = $request->validate([
            'jenjang' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'tahun_mulai' => 'nullable|digits:4|integer',
            'tahun_selesai' => 'nullable|digits:4|integer',
        ]);

        $fasilitator->riwayatPendidikan()->create($validated);

        return redirect()->route('fasilitator.show', $fasilitator)
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function edit(RiwayatPendidikan $riwayatPendidikan)
    {
        return view('riwayat-pendidikan.edit', compact('riwayatPendidikan'));
    }

    public function update(Request $request, RiwayatPendidikan $riwayatPendidikan)
    {
        $validated = $request->validate([
            'jenjang' => 'required|string|max:255',
            'institusi' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'tahun_mulai' => 'nullable|digits:4|integer',
            'tahun_selesai' => 'nullable|digits:4|integer',
        ]);

        $riwayatPendidikan->update($validated);

        return redirect()->route('fasilitator.show', $riwayatPendidikan->fasilitator_id)
            ->with('success', 'Riwayat pendidikan berhasil diperbarui.');
    }

    public function destroy(RiwayatPendidikan $riwayatPendidikan)
    {
        $fasilitatorId = $riwayatPendidikan->fasilitator_id;

        $riwayatPendidikan->delete();

        return redirect()->route('fasilitator.show', $fasilitatorId)
            ->with('success', 'Riwayat pendidikan berhasil dihapus.');
    }
}