<?php

namespace App\Http\Controllers;

use App\Imports\PelatihanImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function create()
    {
        return view('import.create');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $path = $request->file('file')->store('temp/imports', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $import = new PelatihanImport(true);
        Excel::import($import, $fullPath);

        return view('import.preview', [
            'ringkasan' => $import->ringkasan,
            'path' => $path,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        if (!Storage::disk('local')->exists($request->path)) {
            return redirect()->route('import.create')
                ->with('error', 'File sudah tidak ada, silakan upload ulang.');
        }

        $fullPath = Storage::disk('local')->path($request->path);

        $namaOverride = $request->input('nama_fasilitator', []);
        $pelatihanOverride = $request->input('nama_pelatihan', []);
        $materiOverride = $request->input('materi', []);
        $sertakan = $request->input('sertakan', []);

        $dikecualikan = collect($sertakan)
            ->filter(fn($nilai) => $nilai == '0')
            ->keys()
            ->map(fn($k) => (int) $k)
            ->all();

        $import = new PelatihanImport(false, $namaOverride, $pelatihanOverride, $materiOverride, $dikecualikan);
        Excel::import($import, $fullPath);

        Storage::disk('local')->delete($request->path);

        return redirect()->route('fasilitator.index')
            ->with('success', 'Import selesai. ' . $import->ringkasan['fasilitator_baru'] . ' fasilitator baru, ' . $import->ringkasan['pelatihan_ditambahkan'] . ' riwayat pelatihan ditambahkan, ' . $import->ringkasan['baris_dikecualikan'] . ' baris dikecualikan.');
    }
}