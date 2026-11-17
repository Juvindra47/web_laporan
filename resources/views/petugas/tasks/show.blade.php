@extends('layouts.dashboard')
@section('title', 'Detail Tugas')

@section('content')
@php $S = \App\Models\Report::class; @endphp
<div class="page-head"><h1>Detail Tugas</h1>
    <a href="{{ route('petugas.tasks.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a></div>

<div class="row g-3">
    <div class="col-lg-8">
        @include('partials.report-info', ['report' => $report, 'showRepair' => false])

        {{-- CATATAN PERBAIKAN (dapat diubah petugas saat Sedang Dikerjakan) --}}
        <div class="card-s mb-3">
            <div class="card-head">Catatan Perbaikan <span class="badge text-bg-light">{{ $report->repairLogs->count() }}</span></div>
            <div class="card-body-s">
                @forelse ($report->repairLogs as $log)
                    <div class="@if (! $loop->last) border-bottom pb-3 mb-3 @endif">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                            @if ($report->status === $S::STATUS_SEDANG_DIKERJAKAN)
                                <span>
                                    <button class="btn btn-sm btn-outline-primary py-0" type="button" data-bs-toggle="collapse" data-bs-target="#edit{{ $log->id }}">Edit</button>
                                    <form method="POST" action="{{ route('petugas.logs.destroy', $log) }}" class="d-inline" data-confirm="Hapus catatan ini?">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0">Hapus</button></form>
                                </span>
                            @endif
                        </div>
                        <p class="mb-2" style="white-space:pre-line">{{ $log->description }}</p>
                        <div class="row g-2">
                            @if ($log->before_photo_url)<div class="col-6"><div class="small text-muted mb-1">Sebelum</div><a href="{{ $log->before_photo_url }}" target="_blank"><img src="{{ $log->before_photo_url }}" class="photo-lg" style="max-height:160px" alt=""></a></div>@endif
                            @if ($log->after_photo_url)<div class="col-6"><div class="small text-muted mb-1">Sesudah</div><a href="{{ $log->after_photo_url }}" target="_blank"><img src="{{ $log->after_photo_url }}" class="photo-lg" style="max-height:160px" alt=""></a></div>@endif
                        </div>

                        @if ($report->status === $S::STATUS_SEDANG_DIKERJAKAN)
                            <div class="collapse mt-3" id="edit{{ $log->id }}">
                                <form method="POST" action="{{ route('petugas.logs.update', $log) }}" enctype="multipart/form-data" class="bg-light p-3 rounded">@csrf @method('PUT')
                                    <textarea name="description" rows="3" class="form-control mb-2" required minlength="5">{{ $log->description }}</textarea>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6"><label class="small">Foto sebelum (ganti)</label><input type="file" name="before_photo" accept="image/png,image/jpeg" class="form-control form-control-sm"></div>
                                        <div class="col-md-6"><label class="small">Foto sesudah (ganti)</label><input type="file" name="after_photo" accept="image/png,image/jpeg" class="form-control form-control-sm"></div>
                                    </div>
                                    <button class="btn btn-sm btn-primary">Simpan perubahan</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty"><i class="bi bi-tools"></i>Belum ada catatan perbaikan.</div>
                @endforelse
            </div>
        </div>

        @if ($report->status === $S::STATUS_SEDANG_DIKERJAKAN)
            <div class="card-s mb-3">
                <div class="card-head">Tambah Catatan Perbaikan</div>
                <div class="card-body-s">
                    <form method="POST" action="{{ route('petugas.logs.store', $report) }}" enctype="multipart/form-data">@csrf
                        <div class="mb-3"><label class="form-label fw-semibold">Catatan / progres pekerjaan</label>
                            <textarea name="description" rows="3" class="form-control" required minlength="5" placeholder="Apa yang sudah dikerjakan?">{{ old('description') }}</textarea></div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6"><label class="form-label fw-semibold">Foto sebelum perbaikan</label>
                                <input type="file" name="before_photo" accept="image/png,image/jpeg" class="form-control" data-preview="#beforePrev"><img id="beforePrev" class="preview-img" alt=""></div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Foto sesudah perbaikan</label>
                                <input type="file" name="after_photo" accept="image/png,image/jpeg" class="form-control" data-preview="#afterPrev"><img id="afterPrev" class="preview-img" alt=""></div>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Simpan Catatan</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card-s mb-3">
            <div class="card-head">Tindakan</div>
            <div class="card-body-s">
                @if ($report->status === $S::STATUS_MENUNGGU_PETUGAS)
                    <p class="small text-muted">Tugas baru dari admin. Mulai pekerjaan agar status berubah menjadi Sedang Dikerjakan.</p>
                    <form method="POST" action="{{ route('petugas.tasks.start', $report) }}" data-confirm="Mulai mengerjakan perbaikan ini?">@csrf
                        <button class="btn btn-emerald w-100"><i class="bi bi-play-circle me-1"></i>Mulai Pekerjaan</button></form>
                @elseif ($report->status === $S::STATUS_SEDANG_DIKERJAKAN)
                    <p class="small text-muted">Pastikan ada minimal satu catatan dengan foto sesudah perbaikan, lalu kirim hasil ke admin.</p>
                    <form method="POST" action="{{ route('petugas.tasks.submit', $report) }}" data-confirm="Kirim hasil perbaikan ke admin untuk diverifikasi?">@csrf
                        <input type="text" name="notes" class="form-control mb-2" placeholder="Catatan untuk admin (opsional)">
                        <button class="btn btn-emerald w-100"><i class="bi bi-send-check me-1"></i>Kirim Hasil ke Admin</button></form>
                @elseif ($report->status === $S::STATUS_MENUNGGU_VERIFIKASI_HASIL)
                    <div class="alert alert-info border-0 mb-0 small">Hasil sudah dikirim. Menunggu verifikasi admin.</div>
                @else
                    <div class="alert alert-success border-0 mb-0 small">Pekerjaan ini sudah selesai.</div>
                @endif
            </div>
        </div>
        @include('partials.report-timeline', ['report' => $report])
    </div>
</div>
@endsection
