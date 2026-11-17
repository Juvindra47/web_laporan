@extends('layouts.dashboard')
@section('title', 'Penugasan Petugas')

@section('content')
<div class="page-head"><h1>Penugasan Petugas</h1></div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-4"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari tiket / judul"></div>
    <div class="col-md-3">
        <select name="status" class="form-select"><option value="">Semua status</option>
            @foreach ([\App\Models\Report::STATUS_MENUNGGU_PETUGAS, \App\Models\Report::STATUS_SEDANG_DIKERJAKAN, \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI_HASIL] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ \App\Models\Report::STATUS_LABELS[$s] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="technician_id" class="form-select"><option value="">Semua petugas</option>
            @foreach ($technicians as $t)<option value="{{ $t->id }}" @selected((string) request('technician_id') === (string) $t->id)>{{ $t->name }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('admin.assignments.index') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Tiket</th><th>Laporan</th><th>Lokasi</th><th>Status</th><th style="min-width:260px">Petugas</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse ($reports as $r)
        @php $editable = in_array($r->status, [\App\Models\Report::STATUS_MENUNGGU_PETUGAS, \App\Models\Report::STATUS_SEDANG_DIKERJAKAN]); @endphp
        <tr>
            <td><span class="ticket">{{ $r->ticket_number }}</span></td>
            <td>{{ \Illuminate\Support\Str::limit($r->title, 38) }}<div class="small text-muted">{{ $r->category->name }}</div></td>
            <td>{{ $r->room->full_name }}</td>
            <td><x-status-badge :report="$r" /></td>
            <td>
                @if ($editable)
                    <form method="POST" action="{{ $r->assignment ? route('admin.assignments.update', $r->assignment) : route('admin.assignments.store', $r) }}" class="d-flex gap-1">
                        @csrf @if ($r->assignment) @method('PUT') @endif
                        <select name="technician_id" class="form-select form-select-sm" required>
                            <option value="">Pilih petugas</option>
                            @foreach ($technicians as $t)<option value="{{ $t->id }}" @selected($r->assignment?->technician_id === $t->id)>{{ $t->name }}</option>@endforeach
                        </select>
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                @else
                    {{ $r->assignment?->technician?->name ?? '-' }}
                @endif
            </td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.reports.show', $r) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                @if ($r->assignment && $r->status === \App\Models\Report::STATUS_MENUNGGU_PETUGAS)
                    <form method="POST" action="{{ route('admin.assignments.destroy', $r->assignment) }}" class="d-inline" data-confirm="Batalkan penugasan {{ $r->assignment->technician->name }}?">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Batalkan penugasan"><i class="bi bi-x-lg"></i></button></form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="6"><div class="empty"><i class="bi bi-person-gear"></i>Belum ada laporan yang perlu penugasan.<br><small>Terima laporan terlebih dahulu di menu Verifikasi Laporan.</small></div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($reports->hasPages())<div class="p-3 border-top">{{ $reports->links() }}</div>@endif
</div>
@endsection
