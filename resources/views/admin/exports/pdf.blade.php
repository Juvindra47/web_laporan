<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Kerusakan</title>
<style>
    @page { margin: 28px 26px 42px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1d2a44; }
    h1 { font-size: 15px; margin: 0 0 2px; color: #0f1f3d; }
    .muted { color: #5b6678; font-size: 9px; }
    .head { border-bottom: 2px solid #10b981; padding-bottom: 8px; margin-bottom: 4px; }
    .head .school { font-size: 11px; font-weight: bold; color: #0f1f3d; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    thead { display: table-header-group; }
    th { background: #0f1f3d; color: #fff; text-align: left; padding: 6px; font-size: 8.5px; }
    td { border-bottom: 1px solid #d5dbe6; padding: 5px 6px; vertical-align: top; }
    tr { page-break-inside: avoid; }
    tbody tr:nth-child(even) td { background: #f4f6fa; }
    .center { text-align: center; }
    .footer { position: fixed; bottom: -28px; left: 0; right: 0; color: #5b6678; font-size: 8px; border-top: 1px solid #d5dbe6; padding-top: 4px; }
</style>
</head>
<body>
<div class="head">
    <h1>Laporan Kerusakan Fasilitas</h1>
    <div class="school">{{ $setting->school_name }}</div>
    @if ($setting->school_address)<div class="muted">{{ $setting->school_address }}@if ($setting->school_phone) &middot; {{ $setting->school_phone }}@endif</div>@endif
    <div class="muted">SIMAK-SARPRAS &middot; Dicetak {{ now()->translatedFormat('d F Y, H:i') }} &middot; Total {{ $reports->count() }} laporan</div>
    @php
        $activeFilters = [];
        if (! empty($filters['status'])) $activeFilters[] = 'Status: ' . (\App\Models\Report::STATUS_LABELS[$filters['status']] ?? $filters['status']);
        if (! empty($filters['category_id'])) $activeFilters[] = 'Kategori: ' . ($categories[$filters['category_id']] ?? '-');
        if (! empty($filters['room_id'])) $activeFilters[] = 'Lokasi: ' . ($rooms[$filters['room_id']] ?? '-');
        if (! empty($filters['date_from'])) $activeFilters[] = 'Dari: ' . $filters['date_from'];
        if (! empty($filters['date_to'])) $activeFilters[] = 'Sampai: ' . $filters['date_to'];
        if (! empty($filters['search'])) $activeFilters[] = 'Kata kunci: ' . $filters['search'];
    @endphp
    @if ($activeFilters)<div class="muted">Filter: {{ implode(' | ', $activeFilters) }}</div>@endif
</div>

<table>
    <thead><tr><th>No</th><th>Tiket</th><th>Tanggal</th><th>Judul</th><th>Pelapor</th><th>Kategori</th><th>Lokasi</th><th>Petugas</th><th>Status</th></tr></thead>
    <tbody>
    @forelse ($reports as $i => $r)
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $r->ticket_number }}</td>
            <td>{{ $r->created_at->format('d/m/Y') }}</td>
            <td>{{ $r->title }}</td>
            <td>{{ $r->reporter?->name ?? '-' }}</td>
            <td>{{ $r->category?->name ?? '-' }}</td>
            <td>{{ $r->room?->full_name ?? '-' }}</td>
            <td>{{ $r->assignment?->technician?->name ?? '-' }}</td>
            <td>{{ $r->status_label }}</td>
        </tr>
    @empty
        <tr><td colspan="9" class="center">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    {{ $setting->school_name }} &middot; Laporan Kerusakan Fasilitas &middot; SIMAK-SARPRAS
</div>
</body>
</html>
