@extends('layouts.dashboard')
@section('title', 'Notifikasi')

@section('content')
<div class="page-head"><h1>Notifikasi</h1>
    <form method="POST" action="{{ route('notifications.readAll') }}">@csrf<button class="btn btn-light"><i class="bi bi-check2-all me-1"></i>Tandai semua dibaca</button></form></div>
<div class="card-s">
    @forelse ($notifications as $n)
        <a href="{{ route('notifications.read', $n->id) }}" class="d-flex gap-3 p-3 text-decoration-none border-bottom {{ $n->read_at ? '' : 'bg-light' }}" style="color:inherit">
            <div class="icon-box mb-0 flex-shrink-0"><i class="bi bi-bell"></i></div>
            <div><div class="{{ $n->read_at ? '' : 'fw-semibold' }}">{{ $n->data['message'] ?? '' }}</div>
                <div class="small text-muted">{{ $n->created_at->translatedFormat('d M Y, H:i') }}</div></div>
        </a>
    @empty
        <div class="empty"><i class="bi bi-bell-slash"></i>Belum ada notifikasi.</div>
    @endforelse
    @if ($notifications->hasPages())<div class="p-3">{{ $notifications->links() }}</div>@endif
</div>
@endsection
