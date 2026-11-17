@extends('layouts.dashboard')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="page-head"><h1>{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div>
<div class="card-s" style="max-width:640px"><div class="card-body-s">
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf @if ($category->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label fw-semibold">Nama kategori</label>
        <input name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="d-flex gap-2"><button class="btn btn-primary px-4">Simpan</button><a href="{{ route('admin.categories.index') }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
