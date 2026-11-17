@extends('layouts.dashboard')
@section('title', $room->exists ? 'Edit Ruangan' : 'Tambah Ruangan')

@section('content')
<div class="page-head"><h1>{{ $room->exists ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h1>
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div>
<div class="card-s" style="max-width:640px"><div class="card-body-s">
<form method="POST" action="{{ $room->exists ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}">
    @csrf @if ($room->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label fw-semibold">Nama ruangan / lokasi</label>
        <input name="room_name" value="{{ old('room_name', $room->room_name) }}" class="form-control @error('room_name') is-invalid @enderror" required>@error('room_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label fw-semibold">Gedung</label>
        <input name="building" value="{{ old('building', $room->building) }}" class="form-control @error('building') is-invalid @enderror">@error('building')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $room->description) }}</textarea></div>
    <div class="d-flex gap-2"><button class="btn btn-primary px-4">Simpan</button><a href="{{ route('admin.rooms.index') }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
