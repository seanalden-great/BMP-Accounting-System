<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    // Mengambil Data Profil & Log Aktivitas
    public function index()
    {
        $user = Auth::user();

        $loginLogs = \App\Models\LoginActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'login_logs' => $loginLogs
            ]
        ], 200);
    }

    // Memproses Pembaruan Data dari Mobile
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi input dari JSON payload
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:50',
            'password'  => 'nullable|string|min:6',
        ]);

        $user->full_name = $validated['full_name'] ?? $user->full_name;
        $user->name      = $validated['name'];
        $user->username  = $validated['username'];
        $user->email     = $validated['email'] ?? $user->email;
        $user->phone     = $validated['phone'] ?? $user->phone;

        // Tangkap boolean dari mobile (jika true = 1, false = 0)
        if ($request->has('email_notifications')) {
            $user->email_notifications = $request->boolean('email_notifications') ? 1 : 0;
        }

        // Enkripsi kata sandi jika diubah
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Data profil dan keamanan berhasil diperbarui!',
            'data'    => $user
        ], 200);
    }
}
