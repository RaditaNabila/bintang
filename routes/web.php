<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelolaAkunController;
use App\Http\Controllers\DataSiswaController;
use App\Http\Controllers\PoinPrestasiController;
use App\Http\Controllers\PoinPelanggaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ArsipAlumniController;
use App\Http\Controllers\KenaikanKelasController;
use App\Http\Controllers\RelasiWaliSiswaController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\GuruLaporanController;


// =========================
// AUTH & LANDING
// =========================

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');


// =========================
// LUPA / GANTI SANDI
// =========================

Route::get('/lupa-sandi', function () {
    return view('sandi');
})->name('lupa.sandi');

Route::post('/lupa-sandi', [AuthController::class, 'updatePassword'])
    ->name('lupa.sandi.process');


// =========================
// PILIH PERAN
// =========================

Route::get('/pilih-peran', function () {
    return view('pilih-peran');
})->name('pilih-peran');


// =========================
// ROUTE ADMIN / KEPALA SEKOLAH
// =========================
// URL tetap menggunakan /guru
// karena folder resources/views/guru/
// digunakan sebagai area Admin.

Route::prefix('guru')
    ->name('guru.')
    ->middleware('auth')
    ->group(function () {

        // =========================
        // DASHBOARD ADMIN
        // =========================

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        // =========================
        // KELOLA AKUN
        // =========================

        Route::get('/kelola-akun', [KelolaAkunController::class, 'index'])
            ->name('kelola');

        Route::post('/kelola-akun', [KelolaAkunController::class, 'store'])
            ->name('kelola.store');

        Route::put('/kelola-akun/{id}', [KelolaAkunController::class, 'update'])
            ->name('kelola.update');

        Route::delete('/kelola-akun/{id}', [KelolaAkunController::class, 'destroy'])
            ->name('kelola.destroy');


        // =========================
        // DATA SISWA
        // =========================

        Route::get('/data-siswa', [DataSiswaController::class, 'index'])
            ->name('data-siswa');

        Route::post('/data-siswa', [DataSiswaController::class, 'store'])
            ->name('data-siswa.store');

        Route::put('/data-siswa/{id}', [DataSiswaController::class, 'update'])
            ->name('data-siswa.update');

        Route::post('/data-siswa/{id}/arsip', [DataSiswaController::class, 'destroy'])
            ->name('data-siswa.arsip');

        Route::post('/data-siswa/reset-poin', [DataSiswaController::class, 'resetPoin'])
            ->name('data-siswa.reset-poin');

        Route::post('/data-siswa/kelas', [DataSiswaController::class, 'storeKelas'])
            ->name('data-siswa.kelas.store');

        Route::delete('/data-siswa/kelas/{id}', [DataSiswaController::class, 'destroyKelas'])
            ->name('data-siswa.kelas.destroy');


        // =========================
        // RELASI WALI - SISWA
        // =========================

        Route::get('/relasi-wali-siswa', [RelasiWaliSiswaController::class, 'index'])
            ->name('relasi-wali-siswa.index');

        Route::post('/relasi-wali-siswa', [RelasiWaliSiswaController::class, 'store'])
            ->name('relasi-wali-siswa.store');

        Route::delete('/relasi-wali-siswa/{id}', [RelasiWaliSiswaController::class, 'destroy'])
            ->name('relasi-wali-siswa.destroy');


        // =========================
        // NAIK KELAS
        // =========================

        Route::get('/naik-kelas', [KenaikanKelasController::class, 'index'])
            ->name('naik-kelas');

        Route::post('/naik-kelas/proses', [KenaikanKelasController::class, 'prosesOtomatis'])
            ->name('naik-kelas.proses');

        Route::post('/naik-kelas/pindahkan', [KenaikanKelasController::class, 'pindahkan'])
            ->name('naik-kelas.pindahkan');


        // =========================
        // POIN PRESTASI
        // =========================

        Route::get('/poin-prestasi', [PoinPrestasiController::class, 'index'])
            ->name('poin-prestasi');

        Route::post('/poin-prestasi', [PoinPrestasiController::class, 'store'])
            ->name('poin-prestasi.store');

        Route::put('/poin-prestasi/{id}', [PoinPrestasiController::class, 'update'])
            ->name('poin-prestasi.update');

        Route::delete('/poin-prestasi/{id}', [PoinPrestasiController::class, 'destroy'])
            ->name('poin-prestasi.destroy');


        // =========================
        // KATEGORI PRESTASI
        // =========================

        Route::post('/poin-prestasi/kategori', [PoinPrestasiController::class, 'storeKategori'])
            ->name('poin-prestasi.kategori.store');

        Route::delete('/poin-prestasi/kategori/{id}', [PoinPrestasiController::class, 'destroyKategori'])
            ->name('poin-prestasi.kategori.destroy');


        // =========================
        // PELANGGARAN
        // =========================

        Route::get('/pelanggaran', [PoinPelanggaranController::class, 'index'])
            ->name('pelanggaran');

        Route::post('/pelanggaran', [PoinPelanggaranController::class, 'store'])
            ->name('pelanggaran.store');

        Route::delete('/pelanggaran/{id}', [PoinPelanggaranController::class, 'destroy'])
            ->name('pelanggaran.destroy');

        Route::post('/pelanggaran/kategori', [PoinPelanggaranController::class, 'storeKategori'])
            ->name('pelanggaran.storeKategori');

        Route::delete('/pelanggaran/kategori/{id}', [PoinPelanggaranController::class, 'destroyKategori'])
            ->name('pelanggaran.destroyKategori');

        Route::post('/pelanggaran/jenis', [PoinPelanggaranController::class, 'storeJenisPelanggaran'])
            ->name('pelanggaran.storeJenis');

        Route::delete('/pelanggaran/jenis/{id}', [PoinPelanggaranController::class, 'destroyJenisPelanggaran'])
            ->name('pelanggaran.destroyJenis');


        // =========================
        // LAPORAN
        // =========================

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan');

        Route::get('/laporan/export', [GuruLaporanController::class, 'exportExcel'])
            ->name('laporan.export');


        // =========================
        // ARSIP
        // =========================

        Route::get('/arsip', [ArsipAlumniController::class, 'index'])
            ->name('arsip');


        // =========================
        // INFORMASI
        // =========================

        Route::get('/informasi', function () {
            return view('guru.informasi');
        })->name('informasi');

    });


// =========================
// ROUTE PENGAJAR / GURU
// =========================
// Guru asli menggunakan folder:
// resources/views/pengajar/

Route::prefix('pengajar')
    ->name('pengajar.')
    ->middleware('auth')
    ->group(function () {

        // =========================
        // DASHBOARD
        // =========================

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        // =========================
        // DATA SISWA
        // =========================

        Route::get('/data-siswa', [DataSiswaController::class, 'index'])
            ->name('data-siswa');


        // =========================
        // POIN PRESTASI
        // =========================
        // Pengajar hanya bisa melihat
        // dan menambahkan poin.

        Route::get('/poin-prestasi', [PoinPrestasiController::class, 'index'])
            ->name('poin-prestasi');

        Route::post('/poin-prestasi', [PoinPrestasiController::class, 'store'])
            ->name('poin-prestasi.store');


        // =========================
        // PELANGGARAN
        // =========================
        // Pengajar hanya bisa melihat
        // dan menambahkan catatan pelanggaran.
        // Tidak ada route hapus/kategori/jenis.

        Route::get('/pelanggaran', [PoinPelanggaranController::class, 'index'])
            ->name('pelanggaran');

        Route::post('/pelanggaran', [PoinPelanggaranController::class, 'store'])
            ->name('pelanggaran.store');


        // =========================
        // LAPORAN
        // =========================

        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan');


        // =========================
        // INFORMASI
        // =========================

       Route::get('/info', function () {
            return view('pengajar.info');
        })->name('info');

    });


// =========================
// ROUTE WALI / ORANG TUA
// =========================

Route::get('/wali', [OrangTuaController::class, 'index'])
    ->name('wali')
    ->middleware('auth');


// =========================
// LOGOUT
// =========================

Route::post('/logout', function () {

    Auth::logout();

    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('welcome');

})->name('logout');