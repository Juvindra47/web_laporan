@extends('layouts.dashboard')
@section('title', 'Pengaturan Website')

@section('content')
<div class="page-head"><h1>Pengaturan Website</h1>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-light"><i class="bi bi-box-arrow-up-right me-1"></i>Lihat Landing Page</a></div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-s mb-3"><div class="card-head">Identitas Sekolah</div><div class="card-body-s">
            <div class="mb-3"><label class="form-label fw-semibold">Nama sekolah</label>
                <input name="school_name" value="{{ old('school_name', $setting->school_name) }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Alamat sekolah</label>
                <textarea name="school_address" rows="3" class="form-control" placeholder="Isi dengan alamat resmi sekolah">{{ old('school_address', $setting->school_address) }}</textarea></div>
            <div class="mb-3"><label class="form-label fw-semibold">Nomor kontak</label>
                <input name="school_phone" value="{{ old('school_phone', $setting->school_phone) }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label fw-semibold">Logo sekolah <span class="text-muted fw-normal">(JPG/PNG/WEBP, maks 2 MB)</span></label>
                @if ($setting->logo_url)
                    <div class="mb-2 d-flex align-items-center gap-3"><img src="{{ $setting->logo_url }}" style="height:56px;border-radius:8px" alt="Logo">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo"><label for="remove_logo" class="form-check-label small">Hapus logo</label></div></div>
                @endif
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control" data-preview="#logoPreview">
                <img id="logoPreview" class="preview-img" style="max-height:90px" alt="Preview logo">
            </div>
            <div><label class="form-label fw-semibold">Teks footer</label>
                <textarea name="footer_text" rows="2" class="form-control">{{ old('footer_text', $setting->footer_text) }}</textarea></div>
        </div></div>

        <div class="card-s"><div class="card-head">Lokasi Sekolah</div><div class="card-body-s">
            <div class="row g-3">
                <div class="col-6"><label class="form-label fw-semibold">Latitude</label>
                    <input name="latitude" value="{{ old('latitude', $setting->latitude) }}" class="form-control" placeholder="contoh: -7.xxxxx"></div>
                <div class="col-6"><label class="form-label fw-semibold">Longitude</label>
                    <input name="longitude" value="{{ old('longitude', $setting->longitude) }}" class="form-control" placeholder="contoh: 109.xxxxx"></div>
                <div class="col-12"><label class="form-label fw-semibold">URL Google Maps</label>
                    <input name="google_maps_url" value="{{ old('google_maps_url', $setting->google_maps_url) }}" class="form-control" placeholder="https://maps.app.goo.gl/...">
                    <div class="form-text">Salin link lokasi sekolah dari Google Maps (Bagikan &gt; Salin link). Koordinat dipakai untuk menampilkan peta di landing page; jika Anda menempel link <em>embed</em> Google Maps, peta Google yang akan ditampilkan.</div></div>
            </div>
        </div></div>
    </div>

    <div class="col-lg-6">
        <div class="card-s"><div class="card-head">Hero Landing Page</div><div class="card-body-s">
            <div class="mb-3"><label class="form-label fw-semibold">Judul hero</label>
                <input name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label fw-semibold">Deskripsi hero</label>
                <textarea name="hero_description" rows="3" class="form-control">{{ old('hero_description', $setting->hero_description) }}</textarea></div>

            <div class="mb-3"><label class="form-label fw-semibold d-block">Jenis media latar</label>
                @php $type = old('hero_type', $setting->hero_type); @endphp
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="hero_type" id="type_photo" value="photo" @checked($type === 'photo')>
                    <label class="btn btn-outline-primary" for="type_photo"><i class="bi bi-image me-1"></i>Foto</label>
                    <input type="radio" class="btn-check" name="hero_type" id="type_video" value="video" @checked($type === 'video')>
                    <label class="btn btn-outline-primary" for="type_video"><i class="bi bi-camera-video me-1"></i>Video</label>
                </div>
            </div>

            <div class="mb-3"><label class="form-label fw-semibold">Foto latar <span class="text-muted fw-normal">(JPG/PNG/WEBP, maks 5 MB; juga jadi cadangan jika video gagal diputar)</span></label>
                @if ($setting->hero_photo_url)
                    <div class="mb-2"><img src="{{ $setting->hero_photo_url }}" style="max-height:120px;border-radius:10px" alt="Foto hero saat ini">
                        <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_hero_photo" value="1" id="rm_photo"><label class="form-check-label small" for="rm_photo">Hapus foto saat ini</label></div></div>
                @endif
                <input type="file" name="hero_photo" accept="image/png,image/jpeg,image/webp" class="form-control" data-preview="#photoPreview">
                <img id="photoPreview" class="preview-img" alt="Preview foto">
            </div>

            <div class="mb-1"><label class="form-label fw-semibold">Video latar <span class="text-muted fw-normal">(MP4, maks 50 MB)</span></label>
                @if ($setting->hero_video_url)
                    <video src="{{ $setting->hero_video_url }}" muted controls style="max-width:100%;max-height:160px;border-radius:10px" class="mb-1"></video>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="remove_hero_video" value="1" id="rm_video"><label class="form-check-label small" for="rm_video">Hapus video saat ini</label></div>
                @endif
                <input type="file" name="hero_video" accept="video/mp4" class="form-control" data-preview="#videoPreview">
                <video id="videoPreview" class="preview-img" muted controls></video>
                <div class="form-text">Jika upload gagal untuk file besar, naikkan <code>upload_max_filesize</code> dan <code>post_max_size</code> di php.ini (lihat README).</div>
            </div>
        </div></div>
    </div>
</div>

<div class="mt-3"><button class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button></div>
</form>
@endsection
