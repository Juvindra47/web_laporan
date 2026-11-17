@extends('layouts.dashboard')
@section('title', 'Profil')

@section('content')
<div class="page-head"><h1>Profil Saya</h1></div>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-s"><div class="card-head">Data Profil</div><div class="card-body-s">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $user->profile_photo_url }}" class="rounded-circle" style="width:72px;height:72px;object-fit:cover" id="avatarPreview" alt="">
                    <div class="flex-fill"><label class="form-label fw-semibold mb-1">Foto profil <span class="text-muted fw-normal">(JPG/PNG, maks 2 MB)</span></label>
                        <input type="file" name="profile_photo" accept="image/png,image/jpeg" class="form-control" data-preview="#avatarPreview"></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Nama</label><input name="name" value="{{ old('name', $user->name) }}" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Username</label><input name="username" value="{{ old('username', $user->username) }}" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Telepon</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control"></div>
                    <div class="col-12"><label class="form-label fw-semibold">Alamat</label><textarea name="address" rows="2" class="form-control">{{ old('address', $user->address) }}</textarea></div>
                </div>
                <div class="mt-3"><span class="badge text-bg-light border me-2">{{ $user->role_label }}</span><button class="btn btn-primary px-4">Simpan Profil</button></div>
            </form>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card-s"><div class="card-head">Ubah Password</div><div class="card-body-s">
            <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
                <div class="mb-3"><label class="form-label fw-semibold">Password saat ini</label><input type="password" name="current_password" class="form-control" autocomplete="current-password" required></div>
                <div class="mb-3"><label class="form-label fw-semibold">Password baru <span class="text-muted fw-normal">(min. 8 karakter)</span></label><input type="password" name="password" class="form-control" autocomplete="new-password" required></div>
                <div class="mb-3"><label class="form-label fw-semibold">Konfirmasi password baru</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required></div>
                <button class="btn btn-primary">Ubah Password</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
