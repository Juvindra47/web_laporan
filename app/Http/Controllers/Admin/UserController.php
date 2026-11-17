<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('username', 'like', "%{$s}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', (bool) $request->active))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true, 'role' => User::ROLE_PELAPOR])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = $request->validate(['password' => ['required', 'string', 'min:8']])['password'];
        $data['is_active'] = $request->boolean('is_active');

        $created = User::create($data);
        \App\Services\ActivityLogger::log('buat', "Menambah pengguna {$created->name} ({$created->role_label}).", $created);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = $request->validate(['password' => ['string', 'min:8']])['password'];
        }

        // Admin tidak boleh menurunkan/menonaktifkan dirinya sendiri
        if ($user->id === $request->user()->id) {
            $data['role'] = $user->role;
            $data['is_active'] = true;
        }

        if ($user->role === User::ROLE_PETUGAS
            && (! $data['is_active'] || $data['role'] !== User::ROLE_PETUGAS)
            && $this->hasActiveTasks($user)) {
            return back()->withInput()->with('error', 'Petugas ini masih memiliki tugas yang belum selesai. Pindahkan penugasannya dulu sebelum menonaktifkan atau mengubah role.');
        }

        $user->update($data);
        \App\Services\ActivityLogger::log('ubah', "Mengubah data pengguna {$user->name}.", $user);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === User::ROLE_PETUGAS && $this->hasActiveTasks($user)) {
            return back()->with('error', 'Petugas ini masih memiliki tugas yang belum selesai. Pindahkan penugasannya dulu sebelum menghapus.');
        }

        $user->delete(); // soft delete: riwayat laporan tetap utuh
        \App\Services\ActivityLogger::log('hapus', "Menghapus (mengarsipkan) pengguna {$user->name}.", $user);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna dihapus (diarsipkan).');
    }

    private function hasActiveTasks(User $user): bool
    {
        return \App\Models\Assignment::where('technician_id', $user->id)
            ->whereHas('report', fn ($q) => $q->whereIn('status', [
                \App\Models\Report::STATUS_MENUNGGU_PETUGAS,
                \App\Models\Report::STATUS_SEDANG_DIKERJAKAN,
                \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI_HASIL,
            ]))
            ->exists();
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user?->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_PELAPOR, User::ROLE_PETUGAS])],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
