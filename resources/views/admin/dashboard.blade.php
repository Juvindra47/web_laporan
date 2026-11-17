@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')

@section('content')
<div class="page-head">
    <h1>Dashboard Admin</h1>
    <a href="{{ route('admin.verifications') }}" class="btn btn-primary"><i class="bi bi-patch-check me-1"></i>Verifikasi Laporan</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total Laporan', $stats['total'], 'bi-folder2-open', 'tone-navy'],
        ['Menunggu Verifikasi', $stats['menunggu'], 'bi-hourglass-split', 'tone-amber'],
        ['Diterima (Menunggu Petugas)', $stats['diterima'], 'bi-person-check', 'tone-sky'],
        ['Sedang Dikerjakan', $stats['dikerjakan'], 'bi-tools', 'tone-indigo'],
        ['Selesai', $stats['selesai'], 'bi-check2-circle', 'tone-green'],
    ] as [$label, $value, $icon, $tone])
        <div class="col-6 col-xl">
            <div class="card-s stat-card">
                <div class="icon {{ $tone }}"><i class="bi {{ $icon }}"></i></div>
                <div><div class="num">{{ $value }}</div><div class="lbl">{{ $label }}</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card-s h-100"><div class="card-head">Laporan per Bulan (12 bulan terakhir)</div>
            <div class="card-body-s"><canvas id="monthChart" height="110"></canvas></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card-s h-100"><div class="card-head">Laporan per Kategori</div>
            <div class="card-body-s">
                @if (collect($categoryData)->sum() > 0)
                    <canvas id="categoryChart"></canvas>
                @else
                    <div class="empty"><i class="bi bi-pie-chart"></i>Belum ada data.</div>
                @endif
            </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card-s">
            <div class="card-head">Laporan Terbaru <a href="{{ route('admin.reports.index') }}" class="small fw-semibold text-decoration-none">Lihat semua</a></div>
            <div class="table-responsive">
                <table class="table table-s">
                    <thead><tr><th>Tiket</th><th>Judul</th><th>Pelapor</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($recentReports as $r)
                        <tr>
                            <td><span class="ticket">{{ $r->ticket_number }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($r->title, 40) }}</td>
                            <td>{{ $r->reporter->name }}</td>
                            <td><x-status-badge :report="$r" /></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.reports.show', $r) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty">Belum ada laporan.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-s">
            <div class="card-head">Aktivitas Terbaru <a href="{{ route('admin.activities') }}" class="small fw-semibold text-decoration-none">Semua</a></div>
            <div class="card-body-s">
                <ul class="timeline">
                    @forelse ($activities as $a)
                        <li>
                            <div class="small fw-semibold">{{ $a->report->ticket_number }} &middot; {{ $a->new_label }}</div>
                            @if ($a->notes)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($a->notes, 70) }}</div>@endif
                            <div class="text-muted" style="font-size:.72rem">{{ $a->user->name }} &middot; {{ $a->created_at->diffForHumans() }}</div>
                        </li>
                    @empty
                        <li class="text-muted small">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('monthChart'), {
        type: 'bar',
        data: { labels: @json($monthLabels), datasets: [{ label: 'Jumlah laporan', data: @json($monthData), backgroundColor: '#10b981', borderRadius: 6 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
    var cat = document.getElementById('categoryChart');
    if (cat) {
        new Chart(cat, {
            type: 'doughnut',
            data: { labels: @json($categoryLabels), datasets: [{ data: @json($categoryData), backgroundColor: ['#0f1f3d','#10b981','#22396a','#34d399','#5b6678','#6ee7b7','#94a3b8','#065f46'] }] },
            options: { plugins: { legend: { position: 'bottom' } }, cutout: '62%' }
        });
    }
</script>
@endpush
