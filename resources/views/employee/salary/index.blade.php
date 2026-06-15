@extends('layouts.app')
@section('title', 'Slip Gaji')

@push('styles')
<style>
    .salary-wrap { max-width: 900px; margin: 3rem auto; padding: 0 5%; }
    .salary-card {
        background: var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px;
        padding:1.5rem; margin-bottom:1rem; display:grid; grid-template-columns: 1fr auto; gap:1rem; align-items:center;
    }
    .salary-period { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--cream); }
    .salary-detail { font-size:.85rem; color:var(--muted); margin-top:.3rem; }
    .salary-total { font-size:1.3rem; font-weight:700; color:var(--gold-soft); text-align:right; }
    .salary-status { font-size:.78rem; padding:.25rem .8rem; border-radius:20px; }
    .status-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-draft { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .empty-state { text-align:center; padding:4rem 0; color:var(--muted); }
    .empty-state div { font-size:3rem; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="salary-wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Slip Gaji</h1>

    @forelse($salaries as $salary)
    <div class="salary-card">
        <div>
            <div class="salary-period">{{ $salary->period_label }}</div>
            <div class="salary-detail">
                Gaji Pokok: {{ $salary->formatted_base }} ·
                Hadir: {{ $salary->total_present }} hari
                @if($salary->bonus > 0) · Bonus: {{ $salary->formatted_bonus }} @endif
                @if($salary->deduction > 0) · Potongan: {{ $salary->formatted_deduction }} @endif
            </div>
            <div style="margin-top:.5rem;">
                <span class="salary-status status-{{ $salary->status }}">
                    {{ $salary->status === 'paid' ? '✅ Sudah Dibayar' : '⏳ Belum Dibayar' }}
                </span>
            </div>
        </div>
        <div class="salary-total">{{ $salary->formatted_total }}</div>
    </div>
    @empty
    <div class="empty-state">
        <div>💰</div>
        <p>Belum ada data gaji. Slip gaji akan muncul setiap akhir bulan.</p>
    </div>
    @endforelse
</div>
@endsection