@extends('layouts.dashboard')
@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="page-head"><h1>Riwayat Aktivitas</h1></div>

<ul class="nav nav-pills mb-3 gap-1">
    <li class="nav-item"><a class="nav-link {{ $tab === 'pengguna' ? 'active' : '' }}" href="{{ route('admin.activities', ['tab' => 'pengguna']) }}"><i class="bi bi-person-lines-fill me-1"></i>Aktivitas Pengguna</a></li>
    <li class="nav-item"><a class="nav-link {{ $tab === 'laporan' ? 'active' : '' }}" href="{{ route('admin.activities', ['tab' => 'laporan']) }}"><i class="bi bi-arrow-left-right me-1"></i>Perubahan Status Laporan</a></li>
</ul>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="col-md-3"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ $tab === 'laporan' ? 'Tiket, nama, atau catatan' : 'Nama pengguna atau keterangan' }}"></div>
    <div class="col-md-3">
        @if ($tab === 'laporan')
            <select name="status" class="form-select"><option value="">Semua status</option>
                @foreach ($statusOptions as $val => $label)<option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>@endforeach
            </select>
        @else
            <select name="action" class="form-select"><option value="">Semua aktivitas</option>
                @foreach ($actionOptions as $val => $label)<option value="{{ $val }}" @selected(request('action') === $val)>{{ $label }}</option>@endforeach
            </select>
        @endif
    </div>
    <div class="col-md-2"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
    <div class="col-md-2"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('admin.activities', ['tab' => $tab]) }}" class="btn btn-light">Reset</a></div>
</div></div></form>

@if ($tab === 'laporan')
<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Waktu</th><th>Tiket</th><th>Perubahan</th><th>Oleh</th><th>Catatan</th></tr></thead>
    <tbody>
    @forelse ($activities as $a)
        <tr>
            <td class="text-nowrap small">{{ $a->created_at->translatedFormat('d M Y, H:i') }}</td>
            <td><a class="ticket text-decoration-none" href="{{ route('admin.reports.show', $a->report_id) }}">{{ $a->report->ticket_number }}</a></td>
            <td class="small">@if ($a->old_status && $a->old_status !== $a->new_status){{ $a->old_label }} <i class="bi bi-arrow-right-short"></i> @endif<strong>{{ $a->new_label }}</strong></td>
            <td>{{ $a->user->name }}</td>
            <td class="text-muted small">{{ $a->notes ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="5"><div class="empty"><i class="bi bi-clock-history"></i>Belum ada aktivitas.</div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($activities->hasPages())<div class="p-3 border-top">{{ $activities->links() }}</div>@endif
</div>
@else
<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Keterangan</th><th>IP</th></tr></thead>
    <tbody>
    @forelse ($logs as $l)
        <tr>
            <td class="text-nowrap small">{{ $l->created_at->translatedFormat('d M Y, H:i:s') }}</td>
            <td>
                @if ($l->user)
                    <div class="fw-semibold">{{ $l->user->name }}</div><div class="small text-muted">{{ $l->user->role_label }}</div>
                @else
                    <span class="text-muted">Tidak dikenal</span>
                @endif
            </td>
            <td><span class="badge text-bg-{{ $l->action_badge }}">{{ $l->action_label }}</span></td>
            <td class="small">{{ $l->description }}</td>
            <td class="small text-muted text-nowrap">{{ $l->ip_address ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="5"><div class="empty"><i class="bi bi-clock-history"></i>Belum ada aktivitas pengguna.</div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($logs->hasPages())<div class="p-3 border-top">{{ $logs->links() }}</div>@endif
</div>
@endif
@endsection
