@extends('layouts.app')
@section('title', 'Dashboard Bos')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.2rem; margin-bottom:2rem; }
    .stat-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; }
    .stat-card .icon { font-size:1.8rem; margin-bottom:.8rem; }
    .stat-card .num { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--gold); }
    .stat-card .label { font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-top:.2rem; }
    .menu-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; }
    .menu-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.5rem; text-align:center; transition:.2s; }
    .menu-card:hover { border-color:rgba(200,151,58,.35); transform:translateY(-2px); }
    .menu-card .icon { font-size:2rem; margin-bottom:.5rem; }
    .menu-card .label { font-size:.9rem; color:var(--cream); }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.3rem;">Dashboard Bos</h1>
    <p style="color:var(--muted); margin-bottom:2rem;">Selamat datang, {{ auth()->user()->name }}</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon">💰</div>
            <div class="num" style="font-size:1.2rem;">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div>
            <div class="label">Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="icon">📊</div>
            <div class="num" style="font-size:1.2rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="label">Total Pendapatan</div>
        </div>
        <div class="stat-card">
            <div class="icon">📦</div>
            <div class="num">{{ $totalOrders }}</div>
            <div class="label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="icon">👥</div>
            <div class="num">{{ $totalEmployees }}</div>
            <div class="label">Total Karyawan</div>
        </div>
    </div>

    <div style="margin-top:2rem;">
        <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">Menu</div>
        <div class="menu-grid">
            <a href="{{ route('bos.report') }}" class="menu-card">
                <div class="icon">📈</div>
                <div class="label">Laporan Keuangan</div>
            </a>
            <a href="{{ route('bos.salary.index') }}" class="menu-card">
                <div class="icon">💰</div>
                <div class="label">Persetujuan Gaji</div>
            </a>
            <a href="{{ route('bos.employees') }}" class="menu-card">
                <div class="icon">👥</div>
                <div class="label">Data Karyawan</div>
            </a>
            <a href="{{ route('bos.attendances') }}" class="menu-card">
                <div class="icon">📋</div>
                <div class="label">Rekap Absensi</div>
            </a>
        </div>
    </div>
</div>
@endsection