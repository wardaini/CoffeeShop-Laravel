@extends('layouts.app')
@section('title', 'Lacak Pesanan — BrewNest')

@section('content')
<div style="max-width:640px; margin:5rem auto; padding:0 5%;">
    <h1 style="font-family:'Playfair Display',serif; font-size:2.2rem; color:var(--cream); margin-bottom:.5rem;">Lacak Pesanan</h1>
    <p style="color:var(--muted); margin-bottom:2rem;">Masukkan kode pesananmu untuk melihat status terkini.</p>

    <form method="GET" action="{{ route('order.track') }}" style="display:flex; gap:.8rem; margin-bottom:2.5rem;">
        <input type="text" name="order_code" value="{{ request('order_code') }}"
               placeholder="Contoh: ORD-ABCD1234"
               style="flex:1; padding:.8rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.25); border-radius:8px; color:var(--text); font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none;">
        <button type="submit" class="btn btn-gold">Cari</button>
    </form>

    @if(request('order_code') && !$order)
        <div class="alert alert-error">Kode pesanan tidak ditemukan. Periksa kembali kodenya.</div>
    @endif

    @if($order)
    <div style="background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; overflow:hidden;">
        <div style="padding:1.5rem; border-bottom:1px solid rgba(200,151,58,.1); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-size:.75rem; text-transform:uppercase; letter-spacing:.12em; color:var(--muted);">Kode Pesanan</div>
                <div style="font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--gold);">{{ $order->order_code }}</div>
            </div>
            @php
                $statusConfig = [
                    'pending'    => ['bg'=>'rgba(200,151,58,.15)', 'color'=>'#E8B860', 'label'=>'Menunggu'],
                    'processing' => ['bg'=>'rgba(52,152,219,.15)', 'color'=>'#74b9ff', 'label'=>'Diproses'],
                    'completed'  => ['bg'=>'rgba(39,174,96,.15)',  'color'=>'#6fcf97', 'label'=>'Selesai'],
                    'cancelled'  => ['bg'=>'rgba(192,57,43,.15)',  'color'=>'#e07070', 'label'=>'Dibatalkan'],
                ];
                $sc = $statusConfig[$order->status] ?? $statusConfig['pending'];
            @endphp
            <span style="background:{{ $sc['bg'] }}; color:{{ $sc['color'] }}; padding:.4rem 1rem; border-radius:20px; font-size:.82rem; font-weight:600;">
                {{ $sc['label'] }}
            </span>
        </div>

        <div style="padding:1.5rem;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; font-size:.88rem;">
                <div>
                    <div style="color:var(--muted); margin-bottom:.3rem;">Nama</div>
                    <div style="color:var(--cream);">{{ $order->customer_name }}</div>
                </div>
                <div>
                    <div style="color:var(--muted); margin-bottom:.3rem;">Tanggal</div>
                    <div style="color:var(--cream);">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>

            @foreach($order->items as $item)
            <div style="display:flex; justify-content:space-between; padding:.6rem 0; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--muted);">
                <span>{{ $item->product->name ?? 'Produk' }} ×{{ $item->quantity }}</span>
                <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div style="display:flex; justify-content:space-between; padding:.9rem 0 0; font-weight:700; color:var(--gold-soft);">
                <span>Total</span>
                <span>{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection