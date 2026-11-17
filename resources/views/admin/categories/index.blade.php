@extends('layouts.dashboard')
@section('title', 'Kategori Kerusakan')

@section('content')
<div class="page-head"><h1>Kategori Kerusakan</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-emerald"><i class="bi bi-plus-lg me-1"></i>Tambah Kategori</a></div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-9"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama kategori"></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary flex-fill">Cari</button><a href="{{ route('admin.categories.index') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

<div class="card-s"><div class="table-responsive"><table class="table table-s">
    <thead><tr><th>Nama</th><th>Deskripsi</th><th>Jumlah laporan</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    @forelse ($categories as $c)
        <tr>
            <td class="fw-semibold">{{ $c->name }}</td>
            <td class="text-muted">{{ $c->description ?: '-' }}</td>
            <td>{{ $c->reports_count }}</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="d-inline" data-confirm="Hapus kategori {{ $c->name }}?">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4"><div class="empty"><i class="bi bi-tags"></i>Belum ada kategori.</div></td></tr>
    @endforelse
    </tbody></table></div>
    @if ($categories->hasPages())<div class="p-3 border-top">{{ $categories->links() }}</div>@endif
</div>
@endsection
