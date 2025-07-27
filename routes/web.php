<?php

use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputSuratMasukController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SuratMasukController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.input-surat-masuk');
Route::post('/', InputSuratMasukController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index']);
    Route::post('/dashboard-admin/datatable', [DashboardAdminController::class, 'datatable']);

    Route::get('/profil', [DashboardController::class, 'profil']);
    Route::post('/profil', [DashboardController::class, 'profilData']);
    Route::put('/profil', [DashboardController::class, 'profilUpdate']);

    Route::post('/pegawai/datatable', [PegawaiController::class, 'datatable']);
    Route::resource('/pegawai', PegawaiController::class);

    Route::get('/surat-masuk/{surat_masuk}/berkas', [SuratMasukController::class, 'berkas']);
    Route::post('/surat-masuk/datatable', [SuratMasukController::class, 'datatable']);
    Route::resource('/surat-masuk', SuratMasukController::class);
});

require __DIR__.'/auth.php';
