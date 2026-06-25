@extends('layouts.app')
@section('title', 'Notifikasi')

@push('styles')
<style>
    .wrap { max-width:700px; margin:3rem auto; padding:0 5%; }
    .notif-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.1rem 1.3rem; margin-bottom:.6rem; display:flex; gap:1rem; align-items:flex-start; transition:.2s; }
    .notif-card.unread { border-color:rgba(200,151,58,.35); background:rgba(200,151,58,.05); }
    .notif-icon { font-size:1.5rem; flex-shrink:0; }
    .notif-title { font-size:.95rem; color:var(--cream); font-weight:500; }
    .notif-message { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .notif-time { font-size:.75rem; color:var(--muted); margin-top:.3rem; }
    .empty { text-align:center; padding:4rem 0; color:var(--muted); }
    .empty div { font-size:3rem; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">🔔 Notifikasi</h1>

    @forelse($notifications as $notif)
    <div class="notif-card {{ !$notif->is_read ? 'unread' : '' }}">
        <div class="notif-icon">{{ $notif->icon }}</div>
        <div style="flex:1;">
            <div class="notif-title">{{ $notif->title }}</div>
            <div class="notif-message">{{ $notif->message }}</div>
            <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
        </div>
        @if($notif->link)
        <a href="{{ $notif->link }}" class="btn btn-outline btn-sm" style="flex-shrink:0;">Lihat →</a>
        @endif
    </div>
    @empty
    <div class="empty">
        <div>🔔</div>
        <p>Tidak ada notifikasi.</p>
    </div>
    @endforelse

    <div style="margin-top:1.5rem;">{{ $notifications->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection