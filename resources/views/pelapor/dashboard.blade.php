@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <div><h1>Halo, {{ auth()->user()->name }}</h1><div class="text-muted small">Pantau laporan kerusakan Anda di sini.</div></div>
    <a href="{{ route('pelapor.reports.create') }}" class="btn btn-emerald"><i class="bi bi-plus-lg me-1"></i>Buat Laporan Baru</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total Laporan Saya', $stats['total'], 'bi-folder2-open', 'tone-navy'],
        ['Menunggu Verifikasi', $stats['menunggu'], 'bi-hourglass-split', 'tone-amber'],
        ['Sedang Diproses', $stats['diproses'], 'bi-tools', 'tone-indigo'],
        ['Selesai', $stats['selesai'], 'bi-check2-circle', 'tone-green'],
    ] as [$label, $value, $icon, $tone])
        <div class="col-6 col-xl-3"><div class="card-s stat-card"><div class="icon {{ $tone }}"><i class="bi {{ $icon }}"></i></div>
            <div><div class="num">{{ $value }}</div><div class="lbl">{{ $label }}</div></div></div></div>
    @endforeach
</div>

<div class="card-s">
    <div class="card-head">Laporan Terbaru <a href="{{ route('pelapor.reports.index') }}" class="small fw-semibold text-decoration-none">Lihat semua</a></div>
    <div class="table-responsive"><table class="table table-s">
        <thead><tr><th>Tiket</th><th>Tanggal</th><th>Judul</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($recentReports as $r)
            <tr>
                <td><span class="ticket">{{ $r->ticket_number }}</span></td>
                <td class="text-nowrap">{{ $r->created_at->translatedFormat('d M Y') }}</td>
                <td>{{ \Illuminate\Support\Str::limit($r->title, 50) }}</td>
                <td><x-status-badge :report="$r" /></td>
                <td class="text-end"><a href="{{ route('pelapor.reports.show', $r) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="5"><div class="empty"><i class="bi bi-inbox"></i>Anda belum membuat laporan.<br><a href="{{ route('pelapor.reports.create') }}">Buat laporan pertama</a></div></td></tr>
        @endforelse
        </tbody></table></div>
</div>
@endsection
