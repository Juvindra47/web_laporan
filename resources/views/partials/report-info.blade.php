{{-- Variabel: $report (relasi sudah di-load), $showRepair (bool) --}}
<div class="card-s mb-3">
    <div class="card-head">
        <span><span class="ticket">{{ $report->ticket_number }}</span></span>
        <x-status-badge :report="$report" />
    </div>
    <div class="card-body-s">
        <h5 class="fw-bold mb-3">{{ $report->title }}</h5>

        @if ($report->status === 'ditolak' && $report->rejection_reason)
            <div class="alert alert-danger border-0"><div class="fw-semibold mb-1"><i class="bi bi-x-octagon me-1"></i>Alasan penolakan</div>{{ $report->rejection_reason }}</div>
        @endif

        <div class="row g-3 mb-3 small">
            <div class="col-sm-6"><div class="text-muted">Kategori</div><div class="fw-semibold">{{ $report->category->name }}</div></div>
            <div class="col-sm-6"><div class="text-muted">Lokasi / Ruangan</div><div class="fw-semibold">{{ $report->room->full_name }}</div></div>
            <div class="col-sm-6"><div class="text-muted">Pelapor</div><div class="fw-semibold">{{ $report->reporter->name }}</div></div>
            <div class="col-sm-6"><div class="text-muted">Waktu laporan</div><div class="fw-semibold">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</div></div>
            <div class="col-sm-6"><div class="text-muted">Petugas</div><div class="fw-semibold">{{ $report->assignment?->technician?->name ?? 'Belum ditugaskan' }}</div></div>
            <div class="col-sm-6"><div class="text-muted">Pembaruan terakhir</div><div class="fw-semibold">{{ $report->updated_at->diffForHumans() }}</div></div>
        </div>

        <div class="text-muted small mb-1">Deskripsi kerusakan</div>
        <p style="white-space:pre-line">{{ $report->description }}</p>

        <div class="text-muted small mb-2">Foto bukti kerusakan</div>
        <a href="{{ $report->photo_url }}" target="_blank"><img src="{{ $report->photo_url }}" class="photo-lg" alt="Foto kerusakan"></a>
    </div>
</div>

@if ($showRepair)
    <div class="card-s mb-3">
        <div class="card-head">Catatan Perbaikan <span class="badge text-bg-light">{{ $report->repairLogs->count() }}</span></div>
        <div class="card-body-s">
            @forelse ($report->repairLogs as $log)
                <div class="@if (! $loop->last) border-bottom pb-3 mb-3 @endif">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span><i class="bi bi-person-gear me-1"></i>{{ $log->technician->name }}</span>
                        <span>{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <p class="mb-2" style="white-space:pre-line">{{ $log->description }}</p>
                    <div class="row g-2">
                        @if ($log->before_photo_url)
                            <div class="col-6"><div class="small text-muted mb-1">Sebelum</div><a href="{{ $log->before_photo_url }}" target="_blank"><img src="{{ $log->before_photo_url }}" class="photo-lg" style="max-height:180px" alt="Sebelum"></a></div>
                        @endif
                        @if ($log->after_photo_url)
                            <div class="col-6"><div class="small text-muted mb-1">Sesudah</div><a href="{{ $log->after_photo_url }}" target="_blank"><img src="{{ $log->after_photo_url }}" class="photo-lg" style="max-height:180px" alt="Sesudah"></a></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty"><i class="bi bi-tools"></i>Belum ada catatan perbaikan.</div>
            @endforelse
        </div>
    </div>
@endif
