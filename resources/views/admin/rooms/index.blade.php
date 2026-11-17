@extends('layouts.dashboard')
@section('title', 'Data Ruangan')

@section('content')
<div class="page-head"><h1>Data Ruangan</h1>
    <a href="{{ route('admin.rooms.create') }}" class="btn btn-emerald"><i class="bi bi-plus-lg me-1"></i>Tambah Ruangan</a></div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-9"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama ruangan atau gedung"></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary flex-fill">Cari</button><a href="{{ route('admin.rooms.index') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Nama ruangan</th><th>Gedung</th><th>Deskripsi</th><th>Jumlah laporan</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse ($rooms as $r)
        <tr>
            <td class="fw-semibold">{{ $r->room_name }}</td>
            <td>{{ $r->building ?: '-' }}</td>
            <td class="text-muted">{{ $r->description ?: '-' }}</td>
            <td>{{ $r->reports_count }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.rooms.edit', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('admin.rooms.destroy', $r) }}" class="d-inline" data-confirm="Hapus ruangan {{ $r->room_name }}?">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5"><div class="empty"><i class="bi bi-door-open"></i>Belum ada ruangan.</div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($rooms->hasPages())<div class="p-3 border-top">{{ $rooms->links() }}</div>@endif
</div>
@endsection
