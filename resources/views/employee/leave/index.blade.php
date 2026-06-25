@extends('layouts.app')
@section('title', 'Pengajuan Cuti')

@push('styles')
<style>
    .wrap { max-width:900px; margin:3rem auto; padding:0 5%; }
    .leave-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.3rem; margin-bottom:.8rem; }
    .leave-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
    .leave-type { font-family:'Playfair Display',serif; color:var(--cream); font-size:1rem; }
    .leave-date { font-size:.82rem; color:var(--muted); }
    .leave-reason { font-size:.85rem; color:var(--muted); margin-top:.3rem; }
    .status-badge { padding:.25rem .8rem; border-radius:20px; font-size:.78rem; font-weight:600; }
    .status-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-approved { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-rejected { background:rgba(192,57,43,.15); color:#e07070; }
    .rejection-note { font-size:.78rem; color:#e07070; margin-top:.3rem; }
    .empty { text-align:center; padding:4rem 0; color:var(--muted); }
    .empty div { font-size:3rem; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">Pengajuan Cuti / Izin</h1>
        <a href="{{ route('employee.leave.create') }}" class="btn btn-gold btn-sm">+ Ajukan Cuti</a>
    </div>

    @forelse($leaves as $leave)
    <div class="leave-card">
        <div class="leave-header">
            <div>
                <div class="leave-type">{{ $leave->type_label }}</div>
                <div class="leave-date">
                    {{ $leave->start_date->format('d M Y') }}
                    @if($leave->start_date != $leave->end_date)
                        — {{ $leave->end_date->format('d M Y') }}
                    @endif
                    · {{ $leave->total_days }} hari
                </div>
                <div class="leave-reason">{{ $leave->reason }}</div>
                @if($leave->status === 'rejected' && $leave->rejection_reason)
                <div class="rejection-note">Alasan ditolak: {{ $leave->rejection_reason }}</div>
                @endif
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:.5rem;">
                <span class="status-badge status-{{ $leave->status }}">{{ $leave->status_label }}</span>
                @if($leave->status === 'pending')
                <form method="POST" action="{{ route('employee.leave.cancel', $leave) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Batalkan pengajuan ini?')">Batalkan</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty">
        <div>📋</div>
        <p>Belum ada pengajuan cuti.</p>
        <a href="{{ route('employee.leave.create') }}" class="btn btn-gold" style="margin-top:1rem;">Ajukan Cuti Sekarang</a>
    </div>
    @endforelse
</div>
@endsection