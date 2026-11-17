@extends('layouts.dashboard')
@section('title', 'Data Pengguna')

@section('content')
<div class="page-head">
    <h1>Data Pengguna</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-emerald"><i class="bi bi-plus-lg me-1"></i>Tambah Pengguna</a>
</div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-5"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, email, atau username"></div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="">Semua role</option>
            @foreach (\App\Models\User::ROLE_LABELS as $val => $label)<option value="{{ $val }}" @selected(request('role') === $val)>{{ $label }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="active" class="form-select">
            <option value="">Semua status</option>
            <option value="1" @selected(request('active') === '1')>Aktif</option>
            <option value="0" @selected(request('active') === '0')>Nonaktif</option>
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary flex-fill">Filter</button><a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

<div class="card-s">
    <div class="table-responsive">
        <table class="table table-s">
            <thead><tr><th>Pengguna</th><th>Username</th><th>Role</th><th>Telepon</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse ($users as $u)
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="{{ $u->profile_photo_url }}" class="avatar" alt=""><div><div class="fw-semibold">{{ $u->name }}</div><div class="small text-muted">{{ $u->email }}</div></div></div></td>
                    <td>{{ $u->username ?: '-' }}</td>
                    <td><span class="badge text-bg-light border">{{ $u->role_label }}</span></td>
                    <td>{{ $u->phone ?: '-' }}</td>
                    <td>@if ($u->is_active)<span class="badge text-bg-success">Aktif</span>@else<span class="badge text-bg-secondary">Nonaktif</span>@endif</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" data-confirm="Hapus pengguna {{ $u->name }}? Riwayat laporannya tetap tersimpan.">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><i class="bi bi-people"></i>Tidak ada pengguna.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())<div class="p-3 border-top">{{ $users->links() }}</div>@endif
</div>
@endsection
