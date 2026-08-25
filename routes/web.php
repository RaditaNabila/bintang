<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/pilih-peran', function () {
    return view('pilih-peran');
})->name('pilih-peran');


// =========================
// ROUTE GURU
// =========================
Route::prefix('guru')->name('guru.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('dashboard');

    // Kelola Akun
    Route::get('/kelola-akun', function () {
        return view('guru.kelola');
    })->name('kelola');

    // Data Siswa
    Route::get('/data-siswa', function () {
        return view('guru.data-siswa');
    })->name('data-siswa');

    // Relasi Wali-Siswa
    Route::get('/relasi', function () {
        return view('guru.relasi');
    })->name('relasi');

    // Naik Kelas
    Route::get('/naik-kelas', function () {
        return view('guru.naik-kelas');
    })->name('naik-kelas');

    // Poin Prestasi
    Route::get('/poin-prestasi', function () {
        return view('guru.poin-prestasi');
    })->name('poin-prestasi');

    Route::get('/pelanggaran', function () {
        return view('guru.pelanggaran');
    })->name('pelanggaran');

    Route::get('/laporan', function () {
        return view('guru.laporan');
    })->name('laporan');

    Route::get('/guru/arsip', function () {
        return view('guru.arsip');
    })->name('arsip');

    Route::get('/guru/informasi', function () {
        return view('guru.informasi');
    })->name('informasi');

});

    Route::get('/wali', function () {
        return view('wali');
    })->name('wali');

    Route::post('/logout', function () {
    return redirect()->route('welcome');
})->name('logout');
