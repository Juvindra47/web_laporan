@extends('layouts.dashboard')
@section('title', 'Dashboard Petugas')

@section('content')
<div class="page-head"><div><h1>Halo, {{ auth()->user()->name }}</h1><div class="text-muted small">Ringkasan tugas perbaikan Anda.</div></div>
    <a href="{{ route('petugas.tasks.index') }}" class="btn btn-primary"><i class="bi bi-clipboard-check me-1"></i>Semua Tugas</a></div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total Tugas', $stats['total'], 'bi-clipboard-data', 'tone-navy'],
        ['Tugas Baru', $stats['baru'], 'bi-bell', 'tone-sky'],
        ['Sedang Dikerjakan', $stats['dikerjakan'], 'bi-tools', 'tone-indigo'],
        ['Menunggu Verifikasi Hasil', $stats['verifikasi'], 'bi-hourglass-split', 'tone-amber'],
        ['Selesai', $stats['selesai'], 'bi-check2-circle', 'tone-green'],
    ] as [$label, $value, $icon, $tone])
        <div class="col-6 col-xl"><div class="card-s stat-card"><div class="icon {{ $tone }}"><i class="bi {{ $icon }}"></i></div>
            <div><div class="num">{{ $value }}</div><div class="lbl">{{ $label }}</div></div></div></div>
    @endforeach
</div>

<div class="card-s">
    <div class="card-head">Pekerjaan Aktif</div>
    <div class="table-responsive"><table class="table table-s">
        <thead><tr><th>Foto</th><th>Tiket</th><th>Kerusakan</th><th>Lokasi</th><th>Kategori</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($tasks as $t)
            <tr>
                <td><img src="{{ $t->photo_url }}" class="thumb" alt=""></td>
                <td><span class="ticket">{{ $t->ticket_number }}</span></td>
                <td>{{ \Illuminate\Support\Str::limit($t->title, 40) }}</td>
                <td>{{ $t->room->full_name }}</td>
                <td>{{ $t->category->name }}</td>
                <td><x-status-badge :report="$t" /></td>
                <td class="text-end"><a href="{{ route('petugas.tasks.show', $t) }}" class="btn btn-sm btn-outline-primary">Buka</a></td>
            </tr>
        @empty
            <tr><td colspan="7"><div class="empty"><i class="bi bi-emoji-smile"></i>Tidak ada pekerjaan aktif.</div></td></tr>
        @endforelse
        </tbody></table></div>
</div>
@endsection
