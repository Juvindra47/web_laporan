<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login', ['setting' => WebsiteSetting::current()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $data['login'],
            'password' => $data['password'],
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            \App\Services\ActivityLogger::log('login_gagal', 'Percobaan login gagal untuk "' . \Illuminate\Support\Str::limit($data['login'], 60) . '".');
            throw ValidationException::withMessages([
                'login' => 'Email/username atau password salah, atau akun Anda nonaktif.',
            ]);
        }

        $request->session()->regenerate();
        \App\Services\ActivityLogger::log('login', 'Login ke sistem.', null, Auth::user());

        return redirect(Auth::user()->dashboardRoute())->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
    }

    public function destroy(Request $request)
    {
        \App\Services\ActivityLogger::log('logout', 'Logout dari sistem.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil logout.');
    }
}
