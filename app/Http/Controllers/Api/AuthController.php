<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // CARA BARU: Cari user secara manual
        $user = \App\Models\User::where($loginField, $request->username)->first();

        // Gunakan pengecekan Hash murni tanpa Auth::attempt agar Vercel tidak mencoba menulis Session File
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {

            // Terbitkan Token Sanctum
            $token = $user->createToken('bmp-mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username/Email atau Password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Hapus token akses yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
