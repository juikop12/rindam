<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir autentikasi login ke sistem SIPANDU-WBK
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Akun-akun percontohan untuk mempermudah pengujian & simulasi role
        $demoAccounts = [
            [
                'role' => 'Super Administrator',
                'badge' => 'Sistem & Pengaturan',
                'color' => '#1E293B',
                'name' => 'Super Administrator',
                'email' => 'superadmin@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Pengaturan Superadmin & Kelola Akun',
            ],
            [
                'role' => 'Danrindam',
                'badge' => 'Pusat (View Only 5 Satdik)',
                'color' => '#C9A227',
                'name' => 'Kolonel Inf Danrindam III/Slw',
                'email' => 'danrindam@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Monitoring 5 Satdik (Hanya Lihat)',
            ],
            [
                'role' => 'Operator Danrindam',
                'badge' => 'Pusat (Akses Penuh 5 Satdik)',
                'color' => '#0284C7',
                'name' => 'Mayor Inf Operator Danrindam',
                'email' => 'operator.danrindam@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Kelola Data 5 Satdik (Non-Superadmin)',
            ],
            [
                'role' => 'Operator Satdik',
                'badge' => 'SECABA (Bihbul)',
                'color' => '#15803D',
                'name' => 'Kapten Inf Sutrisno',
                'email' => 'operator.secaba@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Terkunci Khusus Secaba',
            ],
            [
                'role' => 'Operator Satdik',
                'badge' => 'SECATA (Pangalengan)',
                'color' => '#15803D',
                'name' => 'Lettu Inf Rahman',
                'email' => 'operator.secata@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Terkunci Khusus Secata',
            ],
            [
                'role' => 'Operator Satdik',
                'badge' => 'DODIKJUR (Lembang)',
                'color' => '#0369A1',
                'name' => 'Kapten Inf Darmawan',
                'email' => 'operator.dodikjur@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Terkunci Khusus Dodikjur',
            ],
            [
                'role' => 'Operator Satdik',
                'badge' => 'DODIKLATPUR (Ciuyah)',
                'color' => '#B45309',
                'name' => 'Lettu Inf Hendro',
                'email' => 'operator.dodiklatpur@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Terkunci Khusus Dodiklatpur',
            ],
            [
                'role' => 'Operator Satdik',
                'badge' => 'BELA NEGARA (Lembang)',
                'color' => '#6D28D9',
                'name' => 'Kapten Czi Anwar',
                'email' => 'operator.belanegara@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Terkunci Khusus Bela Negara',
            ],
            [
                'role' => 'Pengawas Integritas',
                'badge' => 'TIM ZI / INSPEKTORAT',
                'color' => '#475569',
                'name' => 'Mayor Inf Hadi',
                'email' => 'tim.zi@rindam.mil.id',
                'password' => 'password',
                'scope' => 'Audit Trail & Pengawasan ZI',
            ],
        ];

        return view('auth.login', compact('demoAccounts'));
    }

    /**
     * Proses autentikasi masuk sistem
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau identitas pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Silakan tunggu dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->boolean('remember');

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Kombinasi email atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isOperator() && $user->satdik) {
            return redirect()->intended(route('students.index', ['satdik_id' => $user->satdik_id]))
                ->with('success', "Selamat datang, {$user->name}. Hak akses aktif terkunci pada {$user->satdik->name}.");
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang, {$user->name}. Anda berhasil masuk ke Sistem SIPANDU-WBK Rindam III/Siliwangi.");
    }

    /**
     * Proses keluar dari sistem (Logout)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem secara aman.');
    }
}
