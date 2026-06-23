<?php

use App\Http\Controllers\Siswa\DashboardController;
use App\Http\Controllers\Siswa\JadwalController;
use App\Http\Controllers\Siswa\MateriController;
use App\Http\Controllers\Siswa\NilaiController;
use App\Http\Controllers\Siswa\PengumumanController;
use App\Http\Controllers\Siswa\TugasController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Redirect dashboard berdasarkan role
Route::get('dashboard', function () {
    return redirect(auth()->user()->getHomeRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ===================== AREA SISWA =====================
Route::middleware(['auth', 'verified', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Materi
        Route::get('materi', [MateriController::class, 'index'])->name('materi.index');
        Route::get('materi/{materi}', [MateriController::class, 'show'])->name('materi.show');
        Route::get('materi/{materi}/lampiran', [MateriController::class, 'downloadLampiran'])->name('materi.lampiran');

        // Tugas & Pengumpulan
        Route::get('tugas', [TugasController::class, 'index'])->name('tugas.index');
        Route::get('tugas/{tugas}', [TugasController::class, 'show'])->name('tugas.show');
        Route::post('tugas/{tugas}/submit', [TugasController::class, 'submit'])->name('tugas.submit');
        Route::get('tugas/{tugas}/lampiran', [TugasController::class, 'downloadLampiran'])->name('tugas.lampiran');
        Route::get('tugas/{tugas}/jawaban', [TugasController::class, 'downloadJawaban'])->name('tugas.jawaban');

        // Jadwal
        Route::get('jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

        // Pengumuman
        Route::get('pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');

        // Nilai
        Route::get('nilai', [NilaiController::class, 'index'])->name('nilai.index');
    });

// ===================== UNDUH BERKAS (admin & guru) =====================
Route::get('admin/pengumpulan/{pengumpulan}/unduh', \App\Http\Controllers\Admin\PengumpulanDownloadController::class)
    ->middleware(['auth', 'verified', 'role:admin,guru'])
    ->name('admin.pengumpulan.unduh');

require __DIR__.'/auth.php';
