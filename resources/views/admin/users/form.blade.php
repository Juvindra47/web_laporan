@extends('layouts.dashboard')
@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<div class="page-head"><h1>{{ $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div>

<div class="card-s"><div class="card-body-s">
<form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf @if ($user->exists) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Nama lengkap</label>
            <input name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Username <span class="text-muted fw-normal">(opsional, untuk login)</span></label>
            <input name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror">@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" @disabled($user->exists && $user->id === auth()->id())>
                @foreach (\App\Models\User::ROLE_LABELS as $val => $label)<option value="{{ $val }}" @selected(old('role', $user->role) === $val)>{{ $label }}</option>@endforeach
            </select>
            @if ($user->exists && $user->id === auth()->id())<input type="hidden" name="role" value="{{ $user->role }}"><div class="form-text">Role akun Anda sendiri tidak dapat diubah.</div>@endif
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Password @if ($user->exists)<span class="text-muted fw-normal">(kosongkan jika tidak diganti)</span>@endif</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" @required(! $user->exists)>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Nomor telepon</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label fw-semibold">Alamat</label>
            <textarea name="address" rows="2" class="form-control">{{ old('address', $user->address) }}</textarea></div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $user->is_active)) @disabled($user->exists && $user->id === auth()->id())>
                <label class="form-check-label" for="is_active">Akun aktif</label>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2"><button class="btn btn-primary px-4">Simpan</button><a href="{{ route('admin.users.index') }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
