@extends('layouts.dashboard')
@section('title', $title)

@section('content')
<div class="page-head"><h1>{{ $title }}</h1></div>

@include('partials.report-filters', ['action' => route($routeName), 'statusOptions' => $statusOptions, 'categories' => $categories, 'rooms' => $rooms])

@include('partials.report-table', [
    'reports' => $reports,
    'showUrl' => fn ($id) => route('admin.reports.show', $id),
    'showReporter' => true,
    'showTech' => true,
])
@endsection
