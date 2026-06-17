@extends('layouts.app')
@section('title', 'Manajemen Order')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.88rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); vertical-align:middle; }
    .badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-unpaid { background:rgba(192,57,43,.15); color:#e07070; }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-completed { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-cancelled { background:rgba(192,57,43,.15); color:#e07070; }
    .action-group { display:flex; gap:.4rem; flex-wrap:wrap; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Manajemen Order</h1>

    <form method="GET" class="filter-bar">
        <select name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
            <option value="processing" {{ request('status')==='processing'?'selected':'' }}>Diproses</option>
            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Selesai</option>
            <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Dibatalkan</option>
        </select>
        <select name="payment" onchange="this.form.submit()">
            <option value="">Semua Pembayaran</option>
            <option value="unpaid" {{ request('payment')==='unpaid'?'selected':'' }}>Belum Bayar</option>
            <option value="paid" {{ request('payment')==='paid'?'selected':'' }}>Sudah Bayar</option>
        </select>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="color:var(--gold); font-size:.8rem;">{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td style="font-size:.8rem;">{{ $order->order_type_label }}</td>
                    <td style="font-size:.8rem;">{{ $order->payment_method_label }}</td>
                    <td>{{ $order->formatted_grand_total }}</td>
                    <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                    <td><span class="badge badge-{{ $order->payment_status }}">{{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum' }}</span></td>
                    <td>
                        <div class="action-group">
                            @if($order->payment_status === 'unpaid')
                            <form method="POST" action="{{ route('admin.orders.confirm-payment', $order) }}">
                                @csrf
                                <button type="submit" class="btn btn-gold btn-sm">✅ Lunas</button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" style="padding:.35rem .6rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.8rem; outline:none;">
                                    <option value="pending" {{ $order->status==='pending'?'selected':'' }}>Pending</option>
                                    <option value="processing" {{ $order->status==='processing'?'selected':'' }}>Diproses</option>
                                    <option value="completed" {{ $order->status==='completed'?'selected':'' }}>Selesai</option>
                                    <option value="cancelled" {{ $order->status==='cancelled'?'selected':'' }}>Batal</option>
                                </select>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada order.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">
        {{ $orders->links('pagination::simple-bootstrap-4') }}
    </div>
</div>
@endsection