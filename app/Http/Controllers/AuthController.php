<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. Map input form ke nama kolom di tabel pengguna
        $credentials = [
            'nama_pengguna' => $request->username,
            'password'      => $request->password,
        ];

        // 3. Proses Authentikasi Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Ambil data pengguna yang berhasil login
            $user = Auth::user();

            // 4. Redirect berdasarkan nilai kolom 'peran'
            if ($user->peran === 'guru' || $user->peran === 'admin') {
                return redirect()->route('guru.dashboard');
            }

            // Pengarahan untuk akun Orang Tua / Wali ke views/wali.blade.php
            if ($user->peran === 'orang_tua' || $user->peran === 'wali') {
                return redirect()->route('wali');
            }

            // Fallback default jika peran tidak terdefinisi spesifik
            return redirect()->route('guru.dashboard');
        }

        // 5. Jika kredensial salah / login gagal
        return back()->with('error', 'Username atau password salah!')->withInput();
    }

    public function updatePassword(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 2. Cari data pengguna berdasarkan kolom 'nama_pengguna' menggunakan model Pengguna
        $user = \App\Models\Pengguna::where('nama_pengguna', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'Username tidak ditemukan dalam sistem!')->withInput();
        }

        // 3. Update password baru ke kolom 'kata_sandi' dan enkripsi (Hash)
        $user->kata_sandi = Hash::make($request->password);
        $user->save();

        // 4. Redirect kembali dengan pesan sukses
        return back()->with('success', 'Password berhasil diubah! Silakan login kembali.');
    }
}