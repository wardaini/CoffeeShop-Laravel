@extends('layouts.app')
@section('title', 'Pesanan Berhasil — BrewNest')

@section('content')
<div style="max-width:600px; margin:6rem auto; padding:0 5%; text-align:center;">
    <div style="font-size:4rem; margin-bottom:1.5rem;">✅</div>
    <h1 style="font-size:2rem; color:var(--cream); margin-bottom:.8rem;">Pesanan Dikonfirmasi!</h1>

    @if($order)
    <p style="color:var(--muted); margin-bottom:2rem;">
        Terima kasih, <strong style="color:var(--cream);">{{ $order->customer_name }}</strong>!
        Kode pesananmu adalah:
    </p>
    <div style="background:var(--card); border:2px dashed rgba(200,151,58,.4); border-radius:10px; padding:1.5rem 2rem; display:inline-block; margin-bottom:2rem;">
        <div style="font-size:.75rem; text-transform:uppercase; letter-spacing:.15em; color:var(--muted);">Kode Pesanan</div>
        <div style="font-family:'Playfair Display',serif; font-size:2rem; color:var(--gold); margin-top:.3rem; letter-spacing:.05em;">
            {{ $order->order_code }}
        </div>
    </div>

    <div style="background:var(--card); border:1px solid rgba(200,151,58,.12); border-radius:10px; padding:1.5rem; margin-bottom:2rem; text-align:left;">
        @foreach($order->items as $item)
        <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid rgba(200,151,58,.08); font-size:.9rem; color:var(--muted);">
            <span>{{ $item->product->name ?? 'Produk' }} ×{{ $item->quantity }}</span>
            <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div style="display:flex; justify-content:space-between; padding:.8rem 0 0; font-weight:700; color:var(--gold-soft);">
            <span>Total</span>
            <span>{{ $order->formatted_total }}</span>
        </div>
    </div>

    <p style="font-size:.85rem; color:var(--muted); margin-bottom:2rem;">
        Simpan kode pesananmu untuk melacak status di halaman Lacak Pesanan.
    </p>
    @endif

    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('order.track') }}" class="btn btn-outline">Lacak Pesanan</a>
        <a href="{{ route('menu.index') }}" class="btn btn-gold">Pesan Lagi</a>
    </div>
</div>
@endsection