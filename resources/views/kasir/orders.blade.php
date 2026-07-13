@extends('layouts.app')
@section('title', 'Semua Order')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:2rem auto; padding:0 5%; }
    .stats { display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .stat-box { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1rem 1.5rem; flex:1; min-width:160px; }
    .stat-box .num { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--gold); }
    .stat-box .lbl { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .filter-bar select, .filter-bar input { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.85rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); vertical-align:middle; }
    .badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-unpaid { background:rgba(192,57,43,.15); color:#e07070; }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-processing { background:rgba(52,152,219,.15); color:#74b9ff; }
    .badge-ready { background:rgba(155,89,182,.15); color:#c39bd3; }
    .badge-completed { background:rgba(39,174,96,.2); color:#6fcf97; }
    .badge-cancelled { background:rgba(192,57,43,.15); color:#e07070; }
    .action-group { display:flex; gap:.4rem; flex-wrap:wrap; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.8rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">📦 Manajemen Order</h1>
        <a href="{{ route('kasir.queue') }}" class="btn btn-gold btn-sm">📋 Antrian Aktif</a>
    </div>

    {{-- Stats Hari Ini --}}
    <div class="stats">
        <div class="stat-box">
            <div class="num">{{ $todayCount }}</div>
            <div class="lbl">Total Order Hari Ini</div>
        </div>
        <div class="stat-box">
            <div class="num" style="font-size:1.2rem;">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="lbl">Pendapatan Hari Ini</div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-bar">
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        <select name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
            <option value="processing" {{ request('status')==='processing'?'selected':'' }}>Diproses</option>
            <option value="ready" {{ request('status')==='ready'?'selected':'' }}>Siap</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Selesai</option>
            <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Dibatalkan</option>
        </select>
        <select name="payment" onchange="this.form.submit()">
            <option value="">Semua Pembayaran</option>
            <option value="unpaid" {{ request('payment')==='unpaid'?'selected':'' }}>Belum Bayar</option>
            <option value="paid" {{ request('payment')==='paid'?'selected':'' }}>Sudah Bayar</option>
        </select>
        @if(request()->hasAny(['date','status','payment']))
        <a href="{{ route('kasir.orders') }}" style="color:var(--muted); font-size:.85rem; align-self:center;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Pembayaran</th>
                    <th>Total</th>
                    <th>Status Order</th>
                    <th>Status Bayar</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="color:var(--gold); font-size:.82rem;">{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td style="font-size:.8rem; color:var(--muted);">{{ $order->order_type_label }}</td>
                    <td style="font-size:.8rem; color:var(--muted);">{{ $order->payment_method_label }}</td>
                    <td style="font-weight:600;">{{ $order->formatted_grand_total }}</td>
                    <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                    <td>
                        <span class="badge badge-{{ $order->payment_status }}">
                            {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum' }}
                        </span>
                    </td>
                    <td style="font-size:.8rem; color:var(--muted);">{{ $order->created_at->format('d/m H:i') }}</td>
                    <td>
                        <div class="action-group">
                            {{-- Konfirmasi bayar cash/qris --}}
                            @if($order->payment_status === 'unpaid' && in_array($order->payment_method, ['cash', 'qris']))
                            <form method="POST" action="{{ route('kasir.confirm-payment', $order) }}">
                                @csrf
                                <button type="submit" class="btn btn-gold btn-sm">💵 Lunas</button>
                            </form>
                            @endif

                            {{-- Struk --}}
                            <a href="{{ route('order.receipt', $order) }}" class="btn btn-outline btn-sm" target="_blank">🧾</a>

                            {{-- Selesaikan --}}
                            @if(in_array($order->status, ['processing', 'ready']))
                            <form method="POST" action="{{ route('kasir.complete', $order) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">✅</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;">{{ $orders->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection