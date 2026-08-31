<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
