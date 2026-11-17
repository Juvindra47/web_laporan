@extends('layouts.dashboard')
@section('title', $report->exists ? 'Edit Laporan' : 'Buat Laporan')

@section('content')
<div class="page-head"><h1>{{ $report->exists ? 'Edit Laporan ' . $report->ticket_number : 'Buat Laporan Kerusakan' }}</h1>
    <a href="{{ route('pelapor.reports.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div>

<div class="card-s" style="max-width:820px"><div class="card-body-s">
<form method="POST" enctype="multipart/form-data" action="{{ $report->exists ? route('pelapor.reports.update', $report) : route('pelapor.reports.store') }}">
    @csrf @if ($report->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label fw-semibold">Judul kerusakan</label>
        <input name="title" value="{{ old('title', $report->title) }}" class="form-control @error('title') is-invalid @enderror" placeholder="Contoh: Lampu ruang kelas mati" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Kategori kerusakan</label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((string) old('category_id', $report->category_id) === (string) $c->id)>{{ $c->name }}</option>@endforeach
            </select>@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label fw-semibold">Ruangan / lokasi</label>
            <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                <option value="">Pilih ruangan</option>
                @foreach ($rooms as $r)<option value="{{ $r->id }}" @selected((string) old('room_id', $report->room_id) === (string) $r->id)>{{ $r->full_name }}</option>@endforeach
            </select>@error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
    <div class="mb-3"><label class="form-label fw-semibold">Deskripsi kerusakan</label>
        <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Jelaskan kondisi kerusakan sejelas mungkin" required>{{ old('description', $report->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-4"><label class="form-label fw-semibold">Foto bukti kerusakan <span class="text-muted fw-normal">(JPG/PNG, maks 2 MB)</span></label>
        @if ($report->exists)
            <div class="mb-2"><img src="{{ $report->photo_url }}" class="thumb" style="width:120px;height:90px" alt="Foto saat ini"> <span class="small text-muted ms-2">Foto saat ini. Pilih file baru untuk menggantinya.</span></div>
        @endif
        <input type="file" name="photo" accept="image/png,image/jpeg" class="form-control @error('photo') is-invalid @enderror" data-preview="#photoPreview" @required(! $report->exists)>
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <img id="photoPreview" class="preview-img" alt="Preview foto">
    </div>
    <div class="d-flex gap-2"><button class="btn btn-primary px-4"><i class="bi bi-send me-1"></i>{{ $report->exists ? 'Simpan Perubahan' : 'Kirim Laporan' }}</button>
        <a href="{{ route('pelapor.reports.index') }}" class="btn btn-light">Batal</a></div>
</form></div></div>
@endsection
