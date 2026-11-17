@extends('layouts.dashboard')
@section('title', 'Tugas Saya')

@section('content')
<div class="page-head"><h1>Tugas Saya</h1></div>

<form method="GET" class="card-s mb-3"><div class="card-body-s"><div class="row g-2">
    <div class="col-md-6"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari tiket / judul"></div>
    <div class="col-md-4">
        <select name="status" class="form-select"><option value="">Semua status</option>
            @foreach ([\App\Models\Report::STATUS_MENUNGGU_PETUGAS, \App\Models\Report::STATUS_SEDANG_DIKERJAKAN, \App\Models\Report::STATUS_MENUNGGU_VERIFIKASI_HASIL] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ \App\Models\Report::STATUS_LABELS[$s] }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary flex-fill">Cari</button><a href="{{ route('petugas.tasks.index') }}" class="btn btn-light">Reset</a></div>
</div></div></form>

@include('partials.report-table', ['reports' => $tasks, 'showUrl' => fn ($id) => route('petugas.tasks.show', $id)])
@endsection
