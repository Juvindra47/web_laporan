<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>Login - SIMAK-SARPRAS</title>
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
            <h2 class="fw-bold mb-2" style="max-width:18ch">{{ $setting->hero_title }}</h2>
            <p style="opacity:.8;max-width:42ch">Masuk untuk melaporkan kerusakan, memantau perbaikan, atau mengelola penugasan.</p>
        </div>
        <small style="opacity:.6">SIMAK-SARPRAS - Sistem Informasi Manajemen Kerusakan Sekolah</small>
    </div>

    <div class="auth-main">
        <div class="auth-card">
            <h3 class="fw-bold mb-1" style="color:var(--navy)">Masuk ke akun</h3>
            <p class="text-muted mb-4">Gunakan email atau username Anda.</p>

            @include('partials.flash')

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="login">Email / Username</label>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror" autofocus autocomplete="username">
                    @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="password">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Masuk</button>
            </form>

            <p class="text-center small mt-3 mb-0 text-muted">
                Belum punya akun pelapor? <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sekarang</a>
            </p>

            <div class="text-center mt-4"><a href="{{ route('home') }}" class="small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke beranda</a></div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/simak-sarpras.js') }}"></script>
</body>
</html>
