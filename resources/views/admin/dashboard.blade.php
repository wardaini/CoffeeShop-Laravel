@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('styles')
<style>
    .dash-wrap { max-width:1100px; margin:3rem auto; padding:0 5%; }
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.2rem; margin-bottom:2.5rem; }
    .stat-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; }
    .stat-card .icon { font-size:1.8rem; margin-bottom:.8rem; }
    .stat-card .num { font-family:'Playfair Display',serif; font-size:2rem; color:var(--gold); }
    .stat-card .label { font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-top:.2rem; }
    .section-title { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.3rem; margin:2rem 0 1rem; }
    .menu-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; margin-bottom:2rem; }
    .menu-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.3rem; text-align:center; transition:.2s; }
    .menu-card:hover { border-color:rgba(200,151,58,.35); transform:translateY(-2px); }
    .menu-card .icon { font-size:1.8rem; margin-bottom:.5rem; }
    .menu-card .label { font-size:.9rem; color:var(--cream); }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); }
    .badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-unpaid { background:rgba(192,57,43,.15); color:#e07070; }
</style>
@endpush

@section('content')
<div class="dash-wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.3rem;">Dashboard Admin</h1>
    <p style="color:var(--muted); margin-bottom:2rem;">Selamat datang, {{ auth()->user()->name }}</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon">📦</div>
            <div class="num">{{ $totalOrders }}</div>
            <div class="label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="icon">⏳</div>
            <div class="num">{{ $pendingOrders }}</div>
            <div class="label">Pesanan Pending</div>
        </div>
        <div class="stat-card">
            <div class="icon">💳</div>
            <div class="num">{{ $unpaidOrders }}</div>
            <div class="label">Belum Dibayar</div>
        </div>
        <div class="stat-card">
            <div class="icon">💰</div>
            <div class="num" style="font-size:1.3rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="label">Total Pendapatan</div>
        </div>
        <div class="stat-card">
            <div class="icon">👥</div>
            <div class="num">{{ $totalEmployees }}</div>
            <div class="label">Karyawan</div>
        </div>
        <div class="stat-card">
            <div class="icon">🔔</div>
            <div class="num" style="color:{{ $pendingVerif > 0 ? '#e07070' : 'var(--gold)' }};">{{ $pendingVerif }}</div>
            <div class="label">Verifikasi Pending</div>
        </div>
    </div>

    <div class="menu-grid">
        <a href="{{ route('admin.employees.index') }}" class="menu-card">
            <div class="icon">👨‍💼</div>
            <div class="label">Verifikasi Karyawan</div>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="menu-card">
            <div class="icon">📋</div>
            <div class="label">Manajemen Order</div>
        </a>
        <a href="{{ route('admin.products.index') }}" class="menu-card">
            <div class="icon">☕</div>
            <div class="label">Manajemen Produk</div>
        </a>
    </div>

    <div class="section-title">Pesanan Terbaru</div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td style="color:var(--gold);">{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->order_type_label }}</td>
                    <td>{{ $order->formatted_grand_total }}</td>
                    <td>
                        <span class="badge badge-{{ $order->payment_status }}">
                            {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td>
                        @if($order->payment_status === 'unpaid')
                        <form method="POST" action="{{ route('admin.orders.confirm-payment', $order) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-gold btn-sm">✅ Konfirmasi</button>
                        </form>
                        @else
                        <span style="color:var(--muted); font-size:.8rem;">Sudah Lunas</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection