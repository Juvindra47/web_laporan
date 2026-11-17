<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth">
<head>
    @include('partials.head')
    <title>SIMAK-SARPRAS - {{ $setting->school_name }}</title>
    <meta name="description" content="Sistem Informasi Manajemen Kerusakan Sekolah {{ $setting->school_name }}">
</head>
<body>

@php
    $user = auth()->user();

    $reportUrl = $user
        ? ($user->isPelapor() ? route('pelapor.reports.create') : $user->dashboardRoute())
        : route('register');

    $allReportsUrl = match (true) {
        $user === null => route('login'),
        $user->isAdmin() => route('admin.reports.index'),
        $user->isPetugas() => route('petugas.tasks.index'),
        default => route('pelapor.reports.index'),
    };

    $reportDetailUrl = function ($report) use ($user) {
        if ($user === null) {
            return route('login');
        }

        if ($user->isAdmin()) {
            return route('admin.reports.show', $report);
        }

        if ($user->isPetugas()) {
            return (int) $report->assignment?->technician_id === $user->id
                ? route('petugas.tasks.show', $report)
                : route('petugas.tasks.index');
        }

        return (int) $report->user_id === $user->id
            ? route('pelapor.reports.show', $report)
            : route('pelapor.reports.index');
    };

    $statusMeta = [
        \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI => ['label' => 'Menunggu Verifikasi', 'tone' => 'amber'],
        \App\Models\Report::STATUS_MENUNGGU_PETUGAS => ['label' => 'Menunggu Petugas', 'tone' => 'sky'],
        \App\Models\Report::STATUS_SEDANG_DIKERJAKAN => ['label' => 'Sedang Dikerjakan', 'tone' => 'indigo'],
        \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI_HASIL => ['label' => 'Menunggu Verifikasi Hasil', 'tone' => 'slate'],
        \App\Models\Report::STATUS_SELESAI => ['label' => 'Selesai', 'tone' => 'green'],
    ];
@endphp

<nav class="site-nav navbar navbar-expand-lg fixed-top" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-bold" href="#beranda">
            @if ($setting->logo_url)
                <img src="{{ $setting->logo_url }}" class="brand-logo" alt="Logo">
            @else
                <span class="brand-fallback"><i class="bi bi-building-gear"></i></span>
            @endif
            <span style="line-height:1.1">{{ $setting->school_name }}<small class="d-block fw-normal" style="font-size:.68rem;opacity:.7">SIMAK-SARPRAS</small></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#laporan">Laporan</a></li>
                <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Sistem</a></li>
                <li class="nav-item"><a class="nav-link" href="#alur">Alur Pelaporan</a></li>
                <li class="nav-item"><a class="nav-link" href="#lokasi">Lokasi Sekolah</a></li>
                <li class="nav-item ms-lg-2">
                    @auth
                        <a class="btn btn-emerald btn-sm px-3" href="{{ auth()->user()->dashboardRoute() }}">Dashboard</a>
                    @else
                        <div class="d-flex align-items-center gap-1">
                            <a class="btn btn-outline-light btn-sm px-3" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                            <a class="btn btn-emerald btn-sm px-3" href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Daftar</a>
                        </div>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- HERO --}}
<header id="beranda" class="hero" @if ($setting->hero_photo_url) style="background-image:url('{{ $setting->hero_photo_url }}')" @endif>
    @if ($setting->hero_type === 'video' && $setting->hero_video_url)
        <video class="hero-media" autoplay muted loop playsinline preload="metadata"
               @if ($setting->hero_photo_url) poster="{{ $setting->hero_photo_url }}" @endif>
            <source src="{{ $setting->hero_video_url }}" type="video/mp4">
        </video>
    @endif
    <div class="container py-5 mt-5">
        <span class="eyebrow" style="color:var(--emerald)">SIMAK-SARPRAS</span>
        <h1 class="mt-2 mb-3">{{ $setting->hero_title }}</h1>
        <p class="lead mb-4">{{ $setting->hero_description }}</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ $reportUrl }}" class="btn btn-emerald btn-lg px-4"><i class="bi bi-megaphone me-2"></i>Laporkan Kerusakan</a>
            <a href="#laporan" class="btn btn-outline-light btn-lg px-4">Lihat Laporan</a>
        </div>
    </div>
</header>

{{-- RINGKASAN STATUS --}}
<section class="status-strip" aria-label="Ringkasan status laporan">
    <div class="container">
        <div class="status-panel">
            <div class="status-panel-head">
                <div>
                    <div class="eyebrow">Ringkasan Status</div>
                    <h2>Pantauan laporan sarana &amp; prasarana</h2>
                </div>
                <span class="status-updated"><i class="bi bi-clock-history"></i>Diperbarui {{ now()->translatedFormat('d M Y, H:i') }}</span>
            </div>
            <div class="status-grid">
                @foreach ([
                    ['Total Laporan', $stats['total'], 'bi-folder2-open', 'navy'],
                    ['Menunggu Verifikasi', $stats['menunggu'], 'bi-hourglass-split', 'amber'],
                    ['Menunggu Petugas', $stats['menunggu_petugas'], 'bi-person-check', 'sky'],
                    ['Sedang Dikerjakan', $stats['dikerjakan'], 'bi-tools', 'indigo'],
                    ['Selesai', $stats['selesai'], 'bi-check2-circle', 'green'],
                ] as [$label, $value, $icon, $tone])
                    <div class="status-metric">
                        <div class="status-metric-icon tone-{{ $tone }}"><i class="bi {{ $icon }}"></i></div>
                        <div>
                            <div class="status-metric-num">{{ number_format($value, 0, ',', '.') }}</div>
                            <div class="status-metric-label">{{ $label }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- LAPORAN TERBARU --}}
<section id="laporan" class="report-showcase">
    <div class="container">
        <div class="showcase-head">
            <div>
                <div class="eyebrow">Transparansi Laporan</div>
                <h2>Laporan kerusakan terbaru</h2>
                <p>Setiap laporan dari siswa dan guru tampil lengkap dengan foto, lokasi, serta status terkininya. Status mengikuti alur verifikasi admin dan pengerjaan petugas sarpras.</p>
            </div>
            <a href="{{ $reportUrl }}" class="btn btn-emerald"><i class="bi bi-plus-circle me-1"></i>Buat Laporan</a>
        </div>

        @if ($recentReports->isNotEmpty())
            <div class="report-filters" data-report-filter role="group" aria-label="Filter laporan berdasarkan status">
                <button type="button" class="report-filter is-active" data-filter="semua" aria-pressed="true">
                    Semua <span class="count">{{ $recentReports->count() }}</span>
                </button>
                @foreach ($statusMeta as $status => $meta)
                    @if (($statusCounts[$status] ?? 0) > 0)
                        <button type="button" class="report-filter" data-filter="{{ $status }}" aria-pressed="false">
                            {{ $meta['label'] }} <span class="count">{{ $statusCounts[$status] }}</span>
                        </button>
                    @endif
                @endforeach
            </div>

            <div class="row g-4">
                @foreach ($recentReports as $report)
                    @php $meta = $statusMeta[$report->status] ?? ['label' => $report->status_label, 'tone' => 'slate']; @endphp
                    <div class="col-md-6 col-xl-4" data-report-item data-status="{{ $report->status }}">
                        <a href="{{ $reportDetailUrl($report) }}" class="report-card" aria-label="Lihat laporan {{ $report->title }}">
                            <div class="report-card-media">
                                <img src="{{ $report->photo_url }}" alt="Foto kerusakan: {{ $report->title }}" loading="lazy">
                                <span class="status-chip is-{{ $meta['tone'] }}"><span class="status-dot"></span>{{ $meta['label'] }}</span>
                                <span class="report-card-cat">{{ $report->category->name }}</span>
                            </div>
                            <div class="report-card-body">
                                <div class="report-card-ticket">
                                    <span>{{ $report->ticket_number }}</span>
                                    <span class="dot">&middot;</span>
                                    <span>{{ $report->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <h3 class="report-card-title">{{ $report->title }}</h3>
                                <div class="report-card-loc"><i class="bi bi-geo-alt"></i>{{ $report->room->full_name }}</div>
                                <div class="report-card-foot">
                                    <div class="report-reporter">
                                        <img src="{{ $report->reporter->profile_photo_url }}" alt="{{ $report->reporter->name }}">
                                        <div class="min-w-0">
                                            <span class="name">{{ $report->reporter->name }}</span>
                                            <span class="role">{{ $report->reporter->role_label }}</span>
                                        </div>
                                    </div>
                                    <span class="report-card-arrow"><i class="bi bi-arrow-up-right"></i></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach

                <div class="col-12 d-none" data-report-empty>
                    <div class="report-empty"><i class="bi bi-inbox"></i>Belum ada laporan dengan status ini pada daftar terbaru.</div>
                </div>
            </div>

            <div class="report-showcase-foot">
                <span class="note"><i class="bi bi-info-circle me-1"></i>Menampilkan <strong data-report-count>{{ $recentReports->count() }}</strong> dari {{ $recentReports->count() }} laporan terbaru. Status ditampilkan sama dengan dashboard admin.</span>
                <a href="{{ $allReportsUrl }}" class="btn btn-outline-primary">Lihat semua laporan <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        @else
            <div class="report-empty"><i class="bi bi-inbox"></i>Belum ada laporan kerusakan yang masuk. Jadilah yang pertama melapor melalui tombol di atas.</div>
        @endif
    </div>
</section>

{{-- TENTANG --}}
<section id="tentang" class="section">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width:640px">
            <div class="eyebrow">Tentang Sistem</div>
            <h2 class="section-title mt-1">Pelaporan kerusakan yang tercatat, terpantau, dan tuntas</h2>
            <p class="text-muted">SIMAK-SARPRAS menggantikan laporan lisan atau catatan manual dengan tiket digital, sehingga setiap kerusakan fasilitas punya jejak yang jelas dari laporan sampai selesai diperbaiki.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4"><div class="feature-card"><div class="icon-box"><i class="bi bi-ticket-detailed"></i></div><h5 class="fw-bold">Tiket otomatis</h5><p class="text-muted mb-0">Setiap laporan mendapat nomor tiket unik, lengkap dengan foto bukti, kategori, dan lokasi ruangan.</p></div></div>
            <div class="col-md-4"><div class="feature-card"><div class="icon-box"><i class="bi bi-bar-chart-steps"></i></div><h5 class="fw-bold">Status transparan</h5><p class="text-muted mb-0">Pelapor dapat memantau setiap perubahan status kapan saja, tanpa harus bertanya ke bagian sarpras.</p></div></div>
            <div class="col-md-4"><div class="feature-card"><div class="icon-box"><i class="bi bi-clipboard2-check"></i></div><h5 class="fw-bold">Perbaikan terdokumentasi</h5><p class="text-muted mb-0">Petugas mencatat progres dan mengunggah foto sebelum dan sesudah, lalu admin memverifikasi hasilnya.</p></div></div>
        </div>
    </div>
</section>

{{-- ALUR --}}
<section id="alur" class="section section-alt">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="eyebrow">Alur Pelaporan</div>
                <h2 class="section-title mt-1">Dari laporan hingga selesai dalam tujuh langkah</h2>
                <p class="text-muted">Setiap langkah tercatat pada riwayat status sehingga mudah ditelusuri.</p>
            </div>
            <div class="col-lg-7">
                @foreach ([
                    ['Pelapor login', 'Siswa atau guru masuk dengan akun yang diberikan sekolah.'],
                    ['Mengisi formulir laporan', 'Isi judul, kategori, ruangan, deskripsi, dan unggah foto kerusakan.'],
                    ['Admin memverifikasi', 'Admin menerima laporan, atau menolaknya disertai alasan.'],
                    ['Admin menugaskan petugas', 'Laporan yang diterima diteruskan ke petugas sarana dan prasarana.'],
                    ['Petugas melakukan perbaikan', 'Petugas mencatat progres dan foto sebelum serta sesudah perbaikan.'],
                    ['Admin memverifikasi hasil', 'Admin memeriksa hasil pekerjaan petugas.'],
                    ['Laporan selesai', 'Pelapor menerima pemberitahuan bahwa perbaikan telah selesai.'],
                ] as $i => [$t, $d])
                    <div class="step">
                        <span class="step-no">{{ $i + 1 }}</span>
                        <div class="fw-bold">{{ $t }}</div>
                        <div class="text-muted small">{{ $d }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- LOKASI --}}
<section id="lokasi" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="eyebrow">Lokasi Sekolah</div>
            <h2 class="section-title mt-1">Temukan kami</h2>
        </div>
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="feature-card">
                    <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                    <h5 class="fw-bold">{{ $setting->school_name }}</h5>
                    <p class="text-muted">{{ $setting->school_address ?: 'Alamat sekolah belum diisi. Admin dapat mengisinya melalui Pengaturan Website.' }}</p>
                    @if ($setting->school_phone)
                        <p class="mb-3"><i class="bi bi-telephone me-2 text-emerald"></i>{{ $setting->school_phone }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        @if ($setting->directions_url)
                            <a href="{{ $setting->directions_url }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="bi bi-signpost-split me-1"></i>Petunjuk Arah</a>
                        @endif
                        @if ($setting->open_maps_url)
                            <a href="{{ $setting->open_maps_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary"><i class="bi bi-map me-1"></i>Buka di Google Maps</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                @if ($setting->map_embed_url)
                    <iframe class="map-frame" src="{{ $setting->map_embed_url }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi sekolah"></iframe>
                @else
                    <div class="map-empty"><div><i class="bi bi-map fs-1 d-block mb-2"></i>Peta belum tersedia.<br>Admin dapat mengisi koordinat lokasi di Pengaturan Website.</div></div>
                @endif
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="fw-bold text-white fs-5 mb-1">{{ $setting->school_name }}</div>
                <div class="small">{{ $setting->footer_text }}</div>
            </div>
            <div class="col-md-4">
                <div class="fw-semibold text-white mb-2">Alamat</div>
                <div class="small">{{ $setting->school_address ?: '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="fw-semibold text-white mb-2">Tautan</div>
                <div class="small d-flex flex-column gap-1">
                    <a href="#laporan">Laporan Terbaru</a>
                    <a href="#tentang">Tentang Sistem</a>
                    <a href="#alur">Alur Pelaporan</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Daftar Akun</a>
                </div>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.12)" class="my-4">
        <div class="small text-center">&copy; {{ date('Y') }} {{ $setting->school_name }}. SIMAK-SARPRAS - Sistem Informasi Manajemen Kerusakan Sekolah.</div>
    </div>
</footer>

@if (session('success'))
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:2000"><div class="alert alert-success shadow mb-0">{{ session('success') }}</div></div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/simak-sarpras.js') }}"></script>
</body>
</html>
