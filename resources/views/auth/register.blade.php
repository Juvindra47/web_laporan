<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>Daftar Akun Pelapor - SIMAK-SARPRAS</title>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-side" @if ($setting->hero_photo_url) style="background-image:url('{{ $setting->hero_photo_url }}')" @endif>
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold">
            @if ($setting->logo_url)
                <img src="{{ $setting->logo_url }}" class="brand-logo" alt="Logo">
            @else
                <span class="brand-fallback"><i class="bi bi-building-gear"></i></span>
            @endif
            {{ $setting->school_name }}
        </a>
        <div>
            <span class="badge bg-emerald text-white px-3 py-2 mb-3 rounded-pill fw-semibold" style="background-color: var(--emerald) !important;">
                <i class="bi bi-person-plus me-1"></i>Registrasi Pelapor
            </span>
            <h2 class="fw-bold mb-2" style="max-width:18ch">Laporkan Kerusakan Lebih Cepat &amp; Terpantau.</h2>
            <p style="opacity:.8;max-width:42ch">Setiap siswa, guru, dan staf memiliki akun tersendiri untuk melacak status laporan fasilitas hingga selesai diperbaiki oleh petugas.</p>
        </div>
        <small style="opacity:.6">SIMAK-SARPRAS - Sistem Informasi Manajemen Kerusakan Sekolah</small>
    </div>

    <div class="auth-main py-4">
        <div class="auth-card" style="max-width: 440px;">
            <div class="mb-3">
                <span class="eyebrow" style="color: var(--emerald-dark);">Akun Baru</span>
                <h3 class="fw-bold mb-1" style="color:var(--navy)">Daftar Akun Pelapor</h3>
                <p class="text-muted small mb-0">Isi data di bawah ini untuk membuat akun pelapor kerusakan.</p>
            </div>

            @include('partials.flash')

            <form method="POST" action="{{ route('register.store') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi / Ibu Siti" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="email">Alamat Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text" style="font-size: .75rem;">Digunakan untuk login dan menerima pembaruan laporan.</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small" for="username">Username / NISN / NIP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-at"></i></span>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Opsional" class="form-control @error('username') is-invalid @enderror" autocomplete="username">
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small" for="phone">No. WhatsApp / HP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" class="form-control @error('phone') is-invalid @enderror" autocomplete="tel">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small" for="password">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small" for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password di atas" class="form-control" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password_confirmation" aria-label="Tampilkan konfirmasi password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>

                <button class="btn btn-emerald w-100 py-2 fw-bold" type="submit">
                    <i class="bi bi-check2-circle me-1"></i>Daftar Sekarang
                </button>
            </form>

            <p class="text-center small mt-3 mb-0 text-muted">
                Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk di sini</a>
            </p>

            <div class="text-center mt-3"><a href="{{ route('home') }}" class="small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke beranda</a></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/simak-sarpras.js') }}"></script>
</body>
</html>
