@extends('layouts.app')
@section('title', 'Dashboard Cleaning')

@push('styles')
<style>
    .wrap { max-width:700px; margin:2rem auto; padding:0 5%; }
    .progress-bar { background:var(--surface); border-radius:10px; height:12px; overflow:hidden; margin:1rem 0; }
    .progress-fill { height:100%; background:var(--gold); border-radius:10px; transition:.3s; }
    .schedule-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.2rem 1.5rem; margin-bottom:.8rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; }
    .schedule-card.done { opacity:.6; border-color:rgba(39,174,96,.2); }
    .area-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.05rem; }
    .area-meta { font-size:.8rem; color:var(--muted); margin-top:.2rem; }
    .done-time { font-size:.78rem; color:#6fcf97; margin-top:.2rem; }
    .empty { text-align:center; padding:3rem 0; color:var(--muted); }
    .empty div { font-size:3rem; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">🧹 Dashboard Cleaning</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Jadwal kebersihan hari ini, {{ now()->format('d M Y') }}</p>

    {{-- Progress --}}
    @if($todaySchedules->isNotEmpty())
    <div style="background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.3rem; margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:.5rem;">
            <span style="font-size:.85rem; color:var(--muted);">Progress Hari Ini</span>
            <span style="font-size:.85rem; color:var(--gold);">{{ $doneCount }} / {{ $todaySchedules->count() }} area</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $todaySchedules->count() > 0 ? ($doneCount / $todaySchedules->count() * 100) : 0 }}%"></div>
        </div>
        @if($doneCount === $todaySchedules->count())
        <div style="text-align:center; color:#6fcf97; font-size:.88rem; margin-top:.5rem;">✅ Semua area sudah dibersihkan!</div>
        @endif
    </div>
    @endif

    {{-- Jadwal Pending --}}
    @if($pendingCount > 0)
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">⏳ Belum Dibersihkan ({{ $pendingCount }})</div>
    @foreach($todaySchedules->where('status', 'pending') as $schedule)
    <div class="schedule-card">
        <div>
            <div class="area-name">{{ $schedule->area }}</div>
            <div class="area-meta">{{ $schedule->frequency_label }}</div>
        </div>
        <form method="POST" action="{{ route('cleaning.mark-done', $schedule) }}">
            @csrf
            <button type="submit" class="btn btn-gold btn-sm">✅ Selesai</button>
        </form>
    </div>
    @endforeach
    @endif

    {{-- Jadwal Selesai --}}
    @if($doneCount > 0)
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin:1.5rem 0 1rem;">✅ Sudah Dibersihkan ({{ $doneCount }})</div>
    @foreach($todaySchedules->where('status', 'done') as $schedule)
    <div class="schedule-card done">
        <div>
            <div class="area-name">{{ $schedule->area }}</div>
            <div class="done-time">Selesai: {{ $schedule->completed_at?->format('H:i') }}</div>
        </div>
        <span style="color:#6fcf97; font-size:1.5rem;">✅</span>
    </div>
    @endforeach
    @endif

    @if($todaySchedules->isEmpty())
    <div class="empty">
        <div>🧹</div>
        <p>Tidak ada jadwal kebersihan hari ini.</p>
    </div>
    @endif
</div>
@endsection