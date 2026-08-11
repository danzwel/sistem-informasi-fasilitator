<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitatorController;
use App\Http\Controllers\RiwayatPelatihanController;
use App\Http\Controllers\RiwayatPendidikanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('fasilitator', FasilitatorController::class);

Route::resource('fasilitator.riwayat-pelatihan', RiwayatPelatihanController::class)
    ->shallow()
    ->except(['index']);

Route::resource('fasilitator.riwayat-pendidikan', RiwayatPendidikanController::class)
    ->shallow()
    ->except(['index', 'show']);

Route::get('fasilitator/{fasilitator}/cv', [FasilitatorController::class, 'cv'])->name('fasilitator.cv');
Route::get('fasilitator/{fasilitator}/cv/pdf', [FasilitatorController::class, 'cvPdf'])->name('fasilitator.cv.pdf');

Route::get('import', [ImportController::class, 'create'])->name('import.create');
Route::post('import/preview', [ImportController::class, 'preview'])->name('import.preview');
Route::post('import/store', [ImportController::class, 'store'])->name('import.store');