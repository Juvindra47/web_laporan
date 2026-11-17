<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register', [
            'setting' => WebsiteSetting::current(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda strip (-) dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?: null,
            'phone' => $data['phone'] ?: null,
            'password' => $data['password'],
            'role' => User::ROLE_PELAPOR,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log('daftar', "Pengguna mendaftar mandiri sebagai pelapor ({$user->name}).", $user, $user);

        return redirect()->route('pelapor.reports.create')->with('success', 'Pendaftaran berhasil! Selamat datang, '.$user->name.'. Silakan buat laporan kerusakan Anda.');
    }
}
