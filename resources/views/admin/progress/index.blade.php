@extends('layouts.dashboard')
@section('title', 'Progres Perbaikan')

@section('content')
<div class="page-head"><h1>Progres Perbaikan</h1></div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-6"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari tiket / judul"></div>
    <div class="col-md-4">
        <select name="status" class="form-select"><option value="">Semua tahap</option>
            @foreach ($statuses as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ \App\Models\Report::STATUS_LABELS[$s] }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('admin.progress') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Tiket</th><th>Laporan</th><th>Lokasi</th><th>Petugas</th><th>Catatan</th><th>Pembaruan</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($reports as $r)
        <tr>
            <td><span class="ticket">{{ $r->ticket_number }}</span></td>
            <td>{{ \Illuminate\Support\Str::limit($r->title, 38) }}</td>
            <td>{{ $r->room->full_name }}</td>
            <td>{{ $r->assignment?->technician?->name ?? '-' }}</td>
            <td><span class="badge text-bg-light border">{{ $r->repair_logs_count }} catatan</span></td>
            <td class="text-nowrap small">{{ $r->updated_at->diffForHumans() }}</td>
            <td><x-status-badge :report="$r" /></td>
            <td class="text-end"><a href="{{ route('admin.reports.show', $r) }}" class="btn btn-sm btn-outline-primary">Periksa</a></td>
        </tr>
    @empty
        <tr><td colspan="8"><div class="empty"><i class="bi bi-tools"></i>Tidak ada perbaikan yang sedang berjalan.</div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($reports->hasPages())<div class="p-3 border-top">{{ $reports->links() }}</div>@endif
</div>
@endsection
