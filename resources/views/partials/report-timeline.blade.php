{{-- Variabel: $report (histories.user sudah di-load) --}}
<div class="card-s">
    <div class="card-head">Riwayat Status</div>
    <div class="card-body-s">
        <ul class="timeline">
            @forelse ($report->histories as $h)
                <li>
                    <div class="fw-semibold small">
                        @if ($h->old_status && $h->old_status !== $h->new_status)
                            {{ $h->old_label }} <i class="bi bi-arrow-right-short"></i> {{ $h->new_label }}
                        @else
                            {{ $h->new_label }}
                        @endif
                    </div>
                    @if ($h->notes)<div class="small text-muted">{{ $h->notes }}</div>@endif
                    <div class="text-muted" style="font-size:.72rem">{{ $h->user->name }} &middot; {{ $h->created_at->translatedFormat('d M Y, H:i') }}</div>
                </li>
            @empty
                <li class="text-muted small">Belum ada riwayat.</li>
            @endforelse
        </ul>
    </div>
</div>
