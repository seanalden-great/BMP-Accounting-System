<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    // Menampilkan Halaman Profil
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    // Memproses Pembaruan Data Profil
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'name'      => 'required|string|max:255', // Nama Panggilan/Display
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:50',
            // Jika password diisi, harus minimal 6 karakter dan konfirmasi (password_confirmation) harus cocok
            'password'  => 'nullable|string|min:6|confirmed', 
        ]);

        // Menyimpan data teks
        $user->full_name = $validated['full_name'];
        $user->name      = $validated['name'];
        $user->username  = $validated['username'];
        $user->email     = $validated['email'] ?? $user->email;
        $user->phone     = $validated['phone'] ?? $user->phone;

        // Jika user mengetikkan password baru, maka kita enkripsi dan simpan
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Data profil dan keamanan berhasil diperbarui!');
    }
}w