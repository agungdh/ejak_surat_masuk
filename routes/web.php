<?php

use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiklatController;
use App\Http\Controllers\LcController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PpmController;
use App\Http\Controllers\SeminarController;
use App\Http\Controllers\WebinarController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-admin', [DashboardAdminController::class, 'index']);
    Route::post('/dashboard-admin/datatable', [DashboardAdminController::class, 'datatable']);

    Route::get('/profil', [DashboardController::class, 'profil']);
    Route::post('/profil', [DashboardController::class, 'profilData']);
    Route::put('/profil', [DashboardController::class, 'profilUpdate']);

    Route::post('/pegawai/datatable', [PegawaiController::class, 'datatable']);
    Route::resource('/pegawai', PegawaiController::class);

    Route::get('/diklat/{diklat}/berkas', [DiklatController::class, 'berkas']);
    Route::post('/diklat/datatable', [DiklatController::class, 'datatable']);
    Route::resource('/diklat', DiklatController::class);

    Route::post('/ppm/datatable', [PpmController::class, 'datatable']);
    Route::resource('/ppm', PpmController::class);

    Route::get('/seminar/{seminar}/berkas', [SeminarController::class, 'berkas']);
    Route::post('/seminar/datatable', [SeminarController::class, 'datatable']);
    Route::resource('/seminar', SeminarController::class);

    Route::get('/webinar/{webinar}/berkas', [WebinarController::class, 'berkas']);
    Route::post('/webinar/datatable', [WebinarController::class, 'datatable']);
    Route::resource('/webinar', WebinarController::class);

    Route::get('/lc/{lc}/berkas', [LcController::class, 'berkas']);
    Route::post('/lc/datatable', [LcController::class, 'datatable']);
    Route::resource('/lc', LcController::class);
});

require __DIR__.'/auth.php';
