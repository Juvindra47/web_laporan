@extends('layouts.dashboard')
@section('title', 'Detail Laporan')

@section('content')
<div class="page-head">
    <h1>Detail Laporan</h1>
    <a href="{{ url()->previous() === url()->current() ? route('admin.reports.index') : url()->previous() }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        @include('partials.report-info', ['report' => $report, 'showRepair' => true])
    </div>

    <div class="col-lg-4">
        {{-- PANEL AKSI ADMIN --}}
        <div class="card-s mb-3">
            <div class="card-head">Tindakan Admin</div>
            <div class="card-body-s">
                @if ($report->status === \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI)
                    <form method="POST" action="{{ route('admin.reports.accept', $report) }}" class="mb-3">@csrf
                        <label class="form-label small fw-semibold">Terima laporan</label>
                        <select name="technician_id" class="form-select mb-2">
                            <option value="">Tentukan petugas nanti</option>
                            @foreach ($technicians as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                        </select>
                        <input type="text" name="notes" class="form-control mb-2" placeholder="Catatan (opsional)">
                        <button class="btn btn-emerald w-100"><i class="bi bi-check2-circle me-1"></i>Terima Laporan</button>
                    </form>
                    <hr>
                    <form method="POST" action="{{ route('admin.reports.reject', $report) }}">@csrf
                        <label class="form-label small fw-semibold">Tolak laporan</label>
                        <textarea name="rejection_reason" rows="3" class="form-control mb-2" placeholder="Alasan penolakan (wajib)" required minlength="5">{{ old('rejection_reason') }}</textarea>
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Tolak Laporan</button>
                    </form>

                @elseif (in_array($report->status, [\App\Models\Report::STATUS_MENUNGGU_PETUGAS, \App\Models\Report::STATUS_SEDANG_DIKERJAKAN]))
                    <form method="POST" action="{{ route('admin.assignments.store', $report) }}">@csrf
                        <label class="form-label small fw-semibold">{{ $report->assignment ? 'Ganti petugas' : 'Tentukan petugas' }}</label>
                        <select name="technician_id" class="form-select mb-2" required>
                            <option value="">Pilih petugas</option>
                            @foreach ($technicians as $t)
                                <option value="{{ $t->id }}" @selected($report->assignment?->technician_id === $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary w-100"><i class="bi bi-person-check me-1"></i>Simpan Penugasan</button>
                    </form>
                    @if ($report->assignment && $report->status === \App\Models\Report::STATUS_MENUNGGU_PETUGAS)
                        <form method="POST" action="{{ route('admin.assignments.destroy', $report->assignment) }}" data-confirm="Batalkan penugasan petugas ini?" class="mt-2">@csrf @method('DELETE')
                            <button class="btn btn-outline-danger w-100 btn-sm">Batalkan Penugasan</button>
                        </form>
                    @endif

                @elseif ($report->status === \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI_HASIL)
                    <p class="small text-muted">Periksa catatan dan foto perbaikan di sebelah kiri, lalu putuskan.</p>
                    <form method="POST" action="{{ route('admin.reports.approve', $report) }}" class="mb-3">@csrf
                        <input type="text" name="notes" class="form-control mb-2" placeholder="Catatan (opsional)">
                        <button class="btn btn-emerald w-100"><i class="bi bi-check2-all me-1"></i>Setujui, Tandai Selesai</button>
                    </form>
                    <hr>
                    <form method="POST" action="{{ route('admin.reports.return', $report) }}">@csrf
                        <textarea name="notes" rows="2" class="form-control mb-2" placeholder="Alasan dikembalikan ke petugas (wajib)" required minlength="5"></textarea>
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-arrow-counterclockwise me-1"></i>Kembalikan ke Petugas</button>
                    </form>

                @else
                    <p class="text-muted small mb-3">Tidak ada tindakan untuk status ini.</p>
                @endif

                @if (in_array($report->status, [\App\Models\Report::STATUS_DITOLAK, \App\Models\Report::STATUS_SELESAI]))
                    <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" data-confirm="Hapus (arsipkan) laporan ini? Riwayat tetap tersimpan di database." class="mt-2">@csrf @method('DELETE')
                        <button class="btn btn-outline-secondary w-100 btn-sm"><i class="bi bi-trash me-1"></i>Hapus Laporan</button>
                    </form>
                @endif
            </div>
        </div>

        @include('partials.report-timeline', ['report' => $report])
    </div>
</div>
@endsection
