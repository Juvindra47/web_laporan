{{-- Variabel: $reports (paginator), $showUrl (closure id => url), opsional $showReporter, $showTech --}}
<div class="card-s">
    <div class="table-responsive">
        <table class="table table-s">
            <thead>
                <tr>
                    <th>Tiket</th><th>Judul</th>
                    @if ($showReporter ?? false)<th>Pelapor</th>@endif
                    <th>Kategori</th><th>Lokasi</th>
                    @if ($showTech ?? false)<th>Petugas</th>@endif
                    <th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $r)
                    <tr>
                        <td><span class="ticket">{{ $r->ticket_number }}</span></td>
                        <td style="min-width:180px"><div class="fw-semibold">{{ \Illuminate\Support\Str::limit($r->title, 48) }}</div></td>
                        @if ($showReporter ?? false)<td>{{ $r->reporter->name }}</td>@endif
                        <td>{{ $r->category->name }}</td>
                        <td>{{ $r->room->full_name }}</td>
                        @if ($showTech ?? false)<td>{{ $r->assignment?->technician?->name ?? '-' }}</td>@endif
                        <td class="text-nowrap">{{ $r->created_at->translatedFormat('d M Y') }}</td>
                        <td><x-status-badge :report="$r" /></td>
                        <td class="text-end"><a href="{{ $showUrl($r->id) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9"><div class="empty"><i class="bi bi-inbox"></i>Tidak ada data laporan.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($reports->hasPages())
        <div class="p-3 border-top">{{ $reports->links() }}</div>
    @endif
</div>
