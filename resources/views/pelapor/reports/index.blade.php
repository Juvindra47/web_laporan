@extends('layouts.dashboard')
@section('title', 'Laporan Saya')

@section('content')
<div class="page-head"><h1>Laporan Saya</h1>
    <a href="{{ route('pelapor.reports.create') }}" class="btn btn-emerald"><i class="bi bi-plus-lg me-1"></i>Buat Laporan</a></div>

@include('partials.report-filters', ['action' => route('pelapor.reports.index'), 'statusOptions' => \App\Models\Report::STATUS_LABELS, 'categories' => $categories, 'rooms' => $rooms])

@include('partials.report-table', ['reports' => $reports, 'showUrl' => fn ($id) => route('pelapor.reports.show', $id)])
@endsection
