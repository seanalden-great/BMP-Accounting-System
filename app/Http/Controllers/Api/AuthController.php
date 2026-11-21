<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
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

    // --- TAMBAHAN BARU: METODE REGISTRASI API ---
    public function register(Request $request)
    {
        // $validated = $request->validate([
        //     'full_name' => 'required|string|max:100',
        //     'username'  => 'required|string|max:50|unique:users,username',
        //     'email'     => 'nullable|email|max:255|unique:users,email',
        //     'password'  => 'required|string|min:6',
        //     'role'      => 'required|in:Admin,Gudang,Developer,Direktur',
        // ]);

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'nullable|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:Admin,Gudang,Developer,Direktur,HEAD/SPV,Purchasing',
        ]);

        try {
            $user = User::create([
                'name'        => $validated['full_name'],
                'full_name'   => $validated['full_name'],
                'username'    => $validated['username'],
                'email'       => $validated['email'] ?? null,
                'password'    => Hash::make($validated['password']),
                'role'        => $validated['role'],
                'permissions' => '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil didaftarkan! Silakan Login.',
                'data'    => $user
            ], 201); // 201 = Created
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftarkan akun.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
