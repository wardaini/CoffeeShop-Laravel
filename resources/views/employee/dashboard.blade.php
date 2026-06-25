@extends('layouts.app')
@section('title', 'Dashboard Karyawan')

@push('styles')
<style>
    .dash-wrap { max-width: 1000px; margin: 3rem auto; padding: 0 5%; }
    .dash-header { margin-bottom: 2rem; }
    .dash-header h1 { font-family:'Playfair Display',serif; color:var(--cream); }
    .dash-header p { color:var(--muted); margin-top:.3rem; }
    .menu-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:1.2rem; }
    .menu-card {
        background: var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px;
        padding:1.8rem; text-align:center; transition: border-color .2s, transform .2s;
    }
    .menu-card:hover { border-color: rgba(200,151,58,.35); transform: translateY(-3px); }
    .menu-icon { font-size:2.2rem; margin-bottom:.8rem; }
    .menu-label { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.05rem; }
    .today-status { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; margin-bottom:2rem; }
    .today-status .label { font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .today-status .row { display:flex; justify-content:space-between; font-size:.95rem; color:var(--text); padding:.4rem 0; }
</style>
@endpush

@section('content')
<div class="dash-wrap">
    <div class="dash-header">
        <h1>Halo, {{ auth()->user()->name }} 👋</h1>
        <p>{{ auth()->user()->employeeProfile->position ?? '-' }} · Kode: {{ auth()->user()->employeeProfile->employee_code ?? '-' }}</p>
    </div>

    @php
        $today = \App\Models\Attendance::where('user_id', auth()->id())->where('date', today())->first();
    @endphp

    <div class="today-status">
        <div class="label">Absensi Hari Ini</div>
        <div class="row"><span>Jam Masuk</span><span>{{ $today?->clock_in?->format('H:i') ?? '-' }}</span></div>
        <div class="row"><span>Jam Keluar</span><span>{{ $today?->clock_out?->format('H:i') ?? '-' }}</span></div>
        <div class="row"><span>Status</span><span>{{ $today ? ucfirst($today->status) : 'Belum Absen' }}</span></div>
    </div>

    <div class="menu-grid">
        <a href="{{ route('employee.leave.index') }}" class="menu-card">
            <div class="menu-icon">📋</div>
            <div class="menu-label">Cuti / Izin</div>
        </a>
        <a href="{{ route('notifications.index') }}" class="menu-card">
            <div class="menu-icon">🔔</div>
            <div class="menu-label">Notifikasi</div>
        </a>
        <a href="{{ route('employee.barcode') }}" class="menu-card">
            <div class="menu-icon">🪪</div>
            <div class="menu-label">Barcode Absensi</div>
        </a>
        <a href="{{ route('employee.attendance.history') }}" class="menu-card">
            <div class="menu-icon">📋</div>
            <div class="menu-label">Riwayat Absensi</div>
        </a>
        <a href="{{ route('employee.salary') }}" class="menu-card">
            <div class="menu-icon">💰</div>
            <div class="menu-label">Slip Gaji</div>
        </a>
        @if(auth()->user()->employeeProfile->position === 'Kurir')
        <a href="{{ route('employee.delivery.index') }}" class="menu-card">
            <div class="menu-icon">🛵</div>
            <div class="menu-label">Delivery Order</div>
        </a>
        @endif
    </div>
</div>
@endsection