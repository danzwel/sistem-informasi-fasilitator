<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FasilitatorController;
use App\Http\Controllers\RiwayatPelatihanController;
use App\Http\Controllers\RiwayatPendidikanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AuthController;

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
