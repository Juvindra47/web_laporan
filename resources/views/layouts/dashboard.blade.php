<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Dashboard') - SIMAK-SARPRAS</title>
    @stack('styles')
</head>
<body>
@php
    $setting = \App\Models\WebsiteSetting::current();
    $authUser = auth()->user();
    $unreadCount = $authUser->unreadNotifications()->count();
    $latestNotifs = $authUser->notifications()->latest()->take(5)->get();
@endphp
<div class="app-shell">
    @include('partials.sidebar')
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main">
        <header class="topbar">
            <button class="btn btn-light d-lg-none" data-toggle-sidebar type="button"><i class="bi bi-list fs-5"></i></button>
            <div class="me-auto">
                <div class="fw-bold text-navy" style="color:var(--navy)">{{ $setting->school_name }}</div>
            </div>

            <div class="dropdown">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown" type="button" aria-label="Notifikasi">
                    <i class="bi bi-bell"></i>
                    @if ($unreadCount)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width:320px;max-width:92vw">
                    <div class="p-3 border-bottom fw-bold">Notifikasi</div>
                    @forelse ($latestNotifs as $n)
                        <a href="{{ route('notifications.read', $n->id) }}" class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-light fw-semibold' }}" style="white-space:normal">
                            <div class="small">{{ $n->data['message'] ?? '' }}</div>
                            <div class="text-muted" style="font-size:.72rem">{{ $n->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <div class="empty py-4">Belum ada notifikasi.</div>
                    @endforelse
                    <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small py-2 border-top">Lihat semua</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" type="button">
                    <img src="{{ $authUser->profile_photo_url }}" class="avatar" alt="">
                    <span class="d-none d-md-inline small fw-semibold">{{ $authUser->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-1 small text-muted">{{ $authUser->role_label }}</li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Lihat Website</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="content">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="icon-box mx-auto" style="background:rgba(220,53,69,.1);color:#dc3545"><i class="bi bi-exclamation-triangle"></i></div>
                <h6 class="fw-bold">Konfirmasi</h6>
                <p class="text-muted small mb-3" id="confirmText">Yakin melanjutkan?</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmYes">Ya, lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/simak-sarpras.js') }}"></script>
@stack('scripts')
</body>
</html>
