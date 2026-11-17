@php
    $role = auth()->user()->role;

    $menus = match ($role) {
        'admin' => [
            ['Utama', [
                ['Dashboard', 'bi-grid-1x2', route('admin.dashboard'), 'admin.dashboard'],
            ]],
            ['Laporan', [
                ['Data Laporan', 'bi-folder2-open', route('admin.reports.index'), 'admin.reports.*'],
                ['Verifikasi Laporan', 'bi-patch-check', route('admin.verifications'), 'admin.verifications'],
                ['Penugasan Petugas', 'bi-person-gear', route('admin.assignments.index'), 'admin.assignments.*'],
                ['Progres Perbaikan', 'bi-tools', route('admin.progress'), 'admin.progress'],
                ['Riwayat Aktivitas', 'bi-clock-history', route('admin.activities'), 'admin.activities'],
                ['Laporan & Cetak PDF', 'bi-file-earmark-pdf', route('admin.exports.index'), 'admin.exports.*'],
            ]],
            ['Master Data', [
                ['Data Pengguna', 'bi-people', route('admin.users.index'), 'admin.users.*'],
                ['Kategori Kerusakan', 'bi-tags', route('admin.categories.index'), 'admin.categories.*'],
                ['Data Ruangan', 'bi-door-open', route('admin.rooms.index'), 'admin.rooms.*'],
            ]],
            ['Sistem', [
                ['Pengaturan Website', 'bi-sliders', route('admin.settings.edit'), 'admin.settings.*'],
                ['Profil', 'bi-person-circle', route('profile.edit'), 'profile.*'],
            ]],
        ],
        'petugas' => [
            ['Pekerjaan', [
                ['Dashboard', 'bi-grid-1x2', route('petugas.dashboard'), 'petugas.dashboard'],
                ['Tugas Saya', 'bi-clipboard-check', route('petugas.tasks.index'), 'petugas.tasks.index'],
                ['Riwayat Pekerjaan', 'bi-clock-history', route('petugas.tasks.history'), 'petugas.tasks.history'],
            ]],
            ['Akun', [
                ['Profil', 'bi-person-circle', route('profile.edit'), 'profile.*'],
            ]],
        ],
        default => [
            ['Laporan', [
                ['Dashboard', 'bi-grid-1x2', route('pelapor.dashboard'), 'pelapor.dashboard'],
                ['Buat Laporan', 'bi-plus-circle', route('pelapor.reports.create'), 'pelapor.reports.create'],
                ['Laporan Saya', 'bi-folder2-open', route('pelapor.reports.index'), 'pelapor.reports.index'],
            ]],
            ['Akun', [
                ['Profil', 'bi-person-circle', route('profile.edit'), 'profile.*'],
            ]],
        ],
    };
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        @if ($setting->logo_url)
            <img src="{{ $setting->logo_url }}" class="brand-logo" alt="Logo">
        @else
            <span class="brand-fallback"><i class="bi bi-building-gear"></i></span>
        @endif
        <div>
            <div class="name">SIMAK-SARPRAS</div>
            <small>{{ \Illuminate\Support\Str::limit($setting->school_name, 26) }}</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        @foreach ($menus as [$label, $items])
            <div class="sidebar-label">{{ $label }}</div>
            @foreach ($items as [$text, $icon, $url, $pattern])
                <a href="{{ $url }}" class="sidebar-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i>{{ $text }}
                </a>
            @endforeach
        @endforeach
        <div class="sidebar-label">Keluar</div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="sidebar-link"><i class="bi bi-box-arrow-right"></i>Logout</button>
        </form>
    </nav>
</aside>
