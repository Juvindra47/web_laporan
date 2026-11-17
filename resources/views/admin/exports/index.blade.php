@extends('layouts.dashboard')
@section('title', 'Laporan & Cetak PDF')

@section('content')
<div class="page-head">
    <h1>Laporan & Cetak PDF</h1>
    <a href="{{ route('admin.exports.pdf', request()->query()) }}" class="btn btn-emerald"><i class="bi bi-file-earmark-pdf me-1"></i>Unduh PDF (sesuai filter)</a>
</div>

@include('partials.report-filters', ['action' => route('admin.exports.index'), 'statusOptions' => $statusOptions, 'categories' => $categories, 'rooms' => $rooms])

@include('partials.report-table', [
    'reports' => $reports,
    'showUrl' => fn ($id) => route('admin.reports.show', $id),
    'showReporter' => true,
    'showTech' => true,
])
@endsection
