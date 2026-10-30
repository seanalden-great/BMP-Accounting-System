<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;

// class AuthController extends Controller
// {
//     // Menampilkan halaman Login Blade
//     public function showLogin()
//     {
//         return view('auth.login');
//     }

//     // Menampilkan halaman Register Blade
//     public function showRegister()
//     {
//         return view('auth.register');
//     }

//     // Memproses Login (Session Based, Bukan JWT)
//     public function login(Request $request)
//     {
//         $credentials = $request->validate([
//             'username' => 'required|string',
//             'password' => 'required|string',
//         ]);

//         // Auth::attempt akan otomatis mengecek hash password di database
//         if (Auth::attempt($credentials)) {
//             $request->session()->regenerate(); // Mencegah serangan Session Fixation
//             return redirect()->intended('/');  // Arahkan ke Dashboard
//         }

//         // Jika salah password
//         return back()->withErrors([
//             'username' => 'Username atau Password salah.',
//         ])->onlyInput('username');
//     }

//     // Memproses Registrasi Akun
//     public function register(Request $request)
//     {
//         $validated = $request->validate([
//             'username'  => 'required|string|max:50|unique:users,username',
//             'password'  => 'required|string|min:6',
//             'full_name' => 'nullable|string|max:100',
//             'role'      => 'required|in:Admin,Manager,Staff',
//         ]);

//         try {
//             User::create([
//                 'username'  => $validated['username'],
//                 'password'  => Hash::make($validated['password']), // Enkripsi Bcrypt
//                 'full_name' => $validated['full_name'],
//                 'role'      => $validated['role'],
//                 // Jika ingin menambah permissions nanti, bisa disisipkan di sini
//                 'permissions' => '', 
//             ]);

//             return redirect()->route('login')->with('success', 'Akun berhasil didaftarkan! Silakan Login.');
//         } catch (\Exception $e) {
//             return back()->withInput()->with('error', 'Gagal mendaftarkan akun.');
//         }
//     }

//     // Memproses Logout
//     public function logout(Request $request)
//     {
//         Auth::logout();
//         $request->session()->invalidate();
//         $request->session()->regenerateToken();
        
//         return redirect()->route('login');
//     }
// }

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman Login Blade
    public function showLogin()
    {
        return view('auth.login');
    }

    // Menampilkan halaman Register Blade
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses Login (Mendukung Username/Email + Fitur Remember Token Laravel)
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Deteksi apakah user mengetikkan format email atau username
        $loginField = filter_var($validated['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $validated['username'],
            'password'  => $validated['password'],
        ];

        // Ambil status centang "Ingat saya" (remember_token)
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'Username/Email atau Password salah.',
        ])->onlyInput('username');
    }

    // Memproses Registrasi Akun (Mengisi kolom bawaan Laravel & kolom eksisting)
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username',
            'email'     => 'nullable|email|max:255|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:Admin,Manager,Staff',
        ]);

        try {
            User::create([
                'name'        => $validated['full_name'], // Kolom standar Laravel
                'full_name'   => $validated['full_name'], // Kolom eksisting ERP
                'username'    => $validated['username'],
                'email'       => $validated['email'] ?? null,
                'password'    => Hash::make($validated['password']),
                'role'        => $validated['role'],
                'permissions' => '',
            ]);

            return redirect()->route('login')->with('success', 'Akun berhasil didaftarkan! Silakan Login.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mendaftarkan akun.');
        }
    }

    // Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}