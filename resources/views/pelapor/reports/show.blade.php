@extends('layouts.dashboard')
@section('title', 'Detail Laporan')

@section('content')
<div class="page-head"><h1>Detail Laporan</h1>
    <div class="d-flex gap-2">
        @if ($report->status === \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI)
            <a href="{{ route('pelapor.reports.edit', $report) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('pelapor.reports.destroy', $report) }}" data-confirm="Batalkan laporan ini?">@csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Batalkan</button></form>
        @endif
        <a href="{{ route('pelapor.reports.index') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>
<div class="row g-3">
    <div class="col-lg-8">
        {{-- Catatan perbaikan hanya ditampilkan setelah laporan selesai --}}
        @include('partials.report-info', ['report' => $report, 'showRepair' => $report->status === \App\Models\Report::STATUS_SELESAI])
    </div>
    <div class="col-lg-4">@include('partials.report-timeline', ['report' => $report])</div>
</div>
@endsection
