@extends('layouts.app')
@section('title', 'Manajemen Cuti')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .leave-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.3rem; margin-bottom:.8rem; }
    .leave-header { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap; }
    .emp-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1rem; }
    .leave-info { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .leave-reason { font-size:.85rem; color:var(--text); margin-top:.4rem; }
    .action-group { display:flex; gap:.5rem; flex-direction:column; align-items:flex-end; }
    .reject-form input { padding:.4rem .7rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.82rem; outline:none; width:200px; }
    .section-title { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.2rem; margin:2rem 0 1rem; }
    .status-badge { padding:.25rem .8rem; border-radius:20px; font-size:.78rem; font-weight:600; }
    .status-approved { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-rejected { background:rgba(192,57,43,.15); color:#e07070; }
    .empty { text-align:center; padding:2rem 0; color:var(--muted); }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Manajemen Cuti Karyawan</h1>

    {{-- Pending --}}
    <div class="section-title">⏳ Menunggu Persetujuan ({{ $pending->count() }})</div>

    @forelse($pending as $leave)
    <div class="leave-card">
        <div class="leave-header">
            <div>
                <div class="emp-name">{{ $leave->user->name }}</div>
                <div class="leave-info">
                    {{ $leave->type_label }} ·
                    {{ $leave->start_date->format('d M Y') }}
                    @if($leave->start_date != $leave->end_date) — {{ $leave->end_date->format('d M Y') }} @endif
                    · {{ $leave->total_days }} hari
                </div>
                <div class="leave-reason">{{ $leave->reason }}</div>
            </div>
            <div class="action-group">
                <form method="POST" action="{{ route('admin.leave.approve', $leave) }}">
                    @csrf
                    <button type="submit" class="btn btn-gold btn-sm">✅ Setujui</button>
                </form>
                <form method="POST" action="{{ route('admin.leave.reject', $leave) }}" style="display:flex; gap:.4rem; align-items:center;">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="Alasan penolakan..." required>
                    <button type="submit" class="btn btn-danger btn-sm">❌ Tolak</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="empty">Tidak ada pengajuan cuti yang menunggu.</div>
    @endforelse

    {{-- History --}}
    @if($history->isNotEmpty())
    <div class="section-title">📋 Riwayat Cuti</div>
    @foreach($history as $leave)
    <div class="leave-card">
        <div class="leave-header">
            <div>
                <div class="emp-name">{{ $leave->user->name }}</div>
                <div class="leave-info">
                    {{ $leave->type_label }} ·
                    {{ $leave->start_date->format('d M Y') }}
                    @if($leave->start_date != $leave->end_date) — {{ $leave->end_date->format('d M Y') }} @endif
                    · {{ $leave->total_days }} hari
                </div>
            </div>
            <span class="status-badge status-{{ $leave->status }}">{{ $leave->status_label }}</span>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection