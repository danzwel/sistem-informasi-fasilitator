<?php

namespace App\Http\Controllers;

use App\Imports\PelatihanImport;
use App\Models\ActivityLog;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
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

        $batch = ImportBatch::create([
            'user_id' => $request->user()?->id,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'preview',
            'total_rows' => $import->ringkasan['baris_diproses'] + $import->ringkasan['baris_dilewati'],
            'success_rows' => $import->ringkasan['baris_diproses'],
            'failed_rows' => $import->ringkasan['baris_dilewati'],
        ]);

        foreach ($import->ringkasan['valid'] as $item) {
            $batch->rows()->create([
                'row_number' => $item['baris'],
                'status' => 'preview_valid',
                'data' => $item,
            ]);
        }

        foreach ($import->ringkasan['bermasalah'] as $item) {
            $batch->rows()->create([
                'row_number' => $item['baris'],
                'status' => 'preview_error',
                'data' => $item,
                'error_message' => implode(', ', $item['field_kosong']),
            ]);
        }

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'import_preview',
            'description' => 'Preview import dibuat.',
            'metadata' => ['import_batch_id' => $batch->id],
        ]);

        return view('import.preview', [
            'ringkasan' => $import->ringkasan,
            'path' => $path,
            'importBatch' => $batch,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'import_batch_id' => 'required|integer|exists:import_batches,id',
            'confirm_import' => 'accepted',
        ]);

        $batch = ImportBatch::findOrFail($request->integer('import_batch_id'));

        if ($batch->status !== 'preview' || $batch->file_path !== $request->path || !Storage::disk('local')->exists($request->path)) {
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
        try {
            DB::transaction(function () use ($batch, $import, $fullPath) {
                $batch->update(['status' => 'processing', 'started_at' => now()]);
                Excel::import($import, $fullPath);

                foreach ($import->ringkasan['valid'] as $item) {
                    $batch->rows()->updateOrCreate(
                        ['row_number' => $item['baris']],
                        ['status' => 'imported', 'data' => $item, 'error_message' => null]
                    );
                }

                foreach ($import->ringkasan['bermasalah'] as $item) {
                    $batch->rows()->updateOrCreate(
                        ['row_number' => $item['baris']],
                        [
                            'status' => 'failed',
                            'data' => $item,
                            'error_message' => implode(', ', $item['field_kosong']),
                        ]
                    );
                }

                foreach ($import->ringkasan['dikecualikan_detail'] as $item) {
                    $batch->rows()->updateOrCreate(
                        ['row_number' => $item['baris']],
                        ['status' => 'skipped', 'data' => $item, 'error_message' => null]
                    );
                }

                $batch->update([
                    'status' => $import->ringkasan['baris_dilewati'] ? 'partial' : 'completed',
                    'success_rows' => $import->ringkasan['pelatihan_ditambahkan'],
                    'failed_rows' => $import->ringkasan['baris_dilewati'],
                    'completed_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            $batch->update([
                'status' => 'failed',
                'completed_at' => now(),
            ]);

            ActivityLog::create([
                'user_id' => $request->user()?->id,
                'action' => 'import_failed',
                'description' => 'Import pelatihan gagal dan transaksi dibatalkan.',
                'metadata' => ['import_batch_id' => $batch->id],
            ]);

            report($exception);

            return redirect()->route('import.create')
                ->with('error', 'Import gagal. Tidak ada data yang disimpan.');
        }

        Storage::disk('local')->delete($request->path);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'import',
            'description' => 'Import pelatihan selesai.',
            'metadata' => [
                'import_batch_id' => $batch->id,
                'success_rows' => $import->ringkasan['pelatihan_ditambahkan'],
                'failed_rows' => $import->ringkasan['baris_dilewati'],
            ],
        ]);

        return redirect()->route('fasilitator.index')
            ->with('success', 'Import selesai. ' . $import->ringkasan['fasilitator_baru'] . ' fasilitator baru, ' . $import->ringkasan['pelatihan_ditambahkan'] . ' riwayat pelatihan ditambahkan, ' . $import->ringkasan['baris_dikecualikan'] . ' baris dikecualikan.');
    }
}
