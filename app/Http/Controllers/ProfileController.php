<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $new = $request->file('profile_photo')->store('profiles', 'public');
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $new;
        } else {
            unset($data['profile_photo']);
        }

        // role & is_active tidak pernah diambil dari input profil
        $user->update($data);
        \App\Services\ActivityLogger::log('ubah', 'Memperbarui profil.', $user);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $request->password]);
        \App\Services\ActivityLogger::log('ubah', 'Mengubah password akun.', $request->user());

        return back()->with('success', 'Password berhasil diubah.');
    }
}
