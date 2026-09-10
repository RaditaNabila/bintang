<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KelolaAkunController extends Controller
{
    /**
     * Menampilkan semua akun pengguna dengan Pagination & Search
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $pengguna = Pengguna::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nama_pengguna', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('guru.kelola', compact('pengguna'));
    }

    /**
     * Menyimpan akun baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',

            'nama_pengguna' => [
                'required',
                'string',
                'max:100',
                'unique:pengguna,nama_pengguna',
            ],

            'peran' => [
                'required',
                Rule::in(['admin', 'guru', 'orang_tua']),
            ],

            'kata_sandi' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',

            'nama_pengguna.required' => 'Username wajib diisi.',
            'nama_pengguna.unique' => 'Username sudah digunakan.',

            'peran.required' => 'Peran wajib dipilih.',
            'peran.in' => 'Peran tidak valid.',

            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        Pengguna::create([
            'nama'          => $validated['nama'],
            'nama_pengguna' => $validated['nama_pengguna'],
            'kata_sandi'    => Hash::make($validated['kata_sandi']),
            'peran'         => $validated['peran'],
            'status'        => 'aktif',
        ]);

        return redirect()
            ->route('guru.kelola')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Mengubah akun pengguna
     */
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',

            'nama_pengguna' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pengguna', 'nama_pengguna')
                    ->ignore($pengguna->id),
            ],

            'peran' => [
                'required',
                Rule::in(['admin', 'guru', 'orang_tua']),
            ],

            'status' => [
                'nullable',
                Rule::in(['aktif', 'nonaktif']),
            ],

            'kata_sandi' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',

            'nama_pengguna.required' => 'Username wajib diisi.',
            'nama_pengguna.unique' => 'Username sudah digunakan.',

            'peran.required' => 'Peran wajib dipilih.',
            'peran.in' => 'Peran tidak valid.',

            'status.in' => 'Status tidak valid.',

            'kata_sandi.min' => 'Kata sandi minimal 6 karakter.',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $pengguna->nama = $validated['nama'];
        $pengguna->nama_pengguna = $validated['nama_pengguna'];
        $pengguna->peran = $validated['peran'];

        if (!empty($validated['status'])) {
            $pengguna->status = $validated['status'];
        }

        if (!empty($validated['kata_sandi'])) {
            $pengguna->kata_sandi = Hash::make($validated['kata_sandi']);
        }

        $pengguna->save();

        return redirect()
            ->route('guru.kelola')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus akun
     */
    public function destroy($id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $pengguna->delete();

        return redirect()
            ->route('guru.kelola')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
