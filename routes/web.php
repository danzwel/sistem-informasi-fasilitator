<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitatorController;
use App\Http\Controllers\RiwayatPelatihanController;
use App\Http\Controllers\RiwayatPendidikanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\MateriController;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

$viewer = ['auth', 'role:admin,operator,viewer'];
$operator = ['auth', 'role:admin,operator'];

Route::get('/', [DashboardController::class, 'index'])->middleware($viewer)->name('dashboard');

Route::resource('fasilitator', FasilitatorController::class)
    ->middleware($viewer)
    ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], $operator);

Route::resource('fasilitator.riwayat-pelatihan', RiwayatPelatihanController::class)
    ->shallow()
    ->except(['index'])
    ->middleware($operator);

Route::resource('fasilitator.riwayat-pendidikan', RiwayatPendidikanController::class)
    ->shallow()
    ->except(['index', 'show'])
    ->middleware($operator);

Route::get('fasilitator/{fasilitator}/cv', [FasilitatorController::class, 'cv'])->middleware($viewer)->name('fasilitator.cv');
Route::get('fasilitator/{fasilitator}/cv/pdf', [FasilitatorController::class, 'cvPdf'])->middleware($viewer)->name('fasilitator.cv.pdf');

Route::get('import', [ImportController::class, 'create'])->middleware($operator)->name('import.create');
Route::post('import/preview', [ImportController::class, 'preview'])->middleware($operator)->name('import.preview');
Route::post('import/store', [ImportController::class, 'store'])->middleware($operator)->name('import.store');

Route::get('kegiatan', [KegiatanController::class, 'index'])->middleware($viewer)->name('kegiatan.index');
Route::post('kegiatan', [KegiatanController::class, 'store'])->middleware($operator)->name('kegiatan.store');
Route::get('pengajuan', [PengajuanController::class, 'index'])->middleware($viewer)->name('pengajuan.index');
Route::post('pengajuan', [PengajuanController::class, 'store'])->middleware($operator)->name('pengajuan.store');
Route::patch('pengajuan/{pengajuan}/review', [PengajuanController::class, 'review'])->middleware('auth', 'role:admin')->name('pengajuan.review');
Route::post('rating', [RatingController::class, 'store'])->middleware($operator)->name('rating.store');
Route::get('materi', [MateriController::class, 'index'])->middleware($viewer)->name('materi.index');
Route::post('materi', [MateriController::class, 'store'])->middleware($operator)->name('materi.store');
