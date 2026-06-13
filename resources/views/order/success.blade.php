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

    <div style="background:var(--card); border:1px solid rgba(200,151,58,.12); border-radius:10px; padding:1rem 1.5rem; margin-bottom:1rem; text-align:left; font-size:.88rem; color:var(--muted);">
        <div style="display:flex; justify-content:space-between; padding:.4rem 0;">
            <span>Tipe Pesanan</span>
            <span style="color:var(--cream);">{{ $order->order_type_label }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; padding:.4rem 0;">
            <span>Pembayaran</span>
            <span style="color:var(--cream);">{{ $order->payment_method_label }}</span>
        </div>
        @if($order->delivery_address)
        <div style="display:flex; justify-content:space-between; padding:.4rem 0;">
            <span>Alamat</span>
            <span style="color:var(--cream); text-align:right; max-width:60%;">{{ $order->delivery_address }}</span>
        </div>
        @endif
    </div>

    <div style="background:var(--card); border:1px solid rgba(200,151,58,.12); border-radius:10px; padding:1.5rem; margin-bottom:2rem; text-align:left;">
        @foreach($order->items as $item)
        <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid rgba(200,151,58,.08); font-size:.9rem; color:var(--muted);">
            <span>{{ $item->product->name ?? 'Produk' }} ×{{ $item->quantity }}</span>
            <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
        </div>
        @endforeach
        @if($order->delivery_fee > 0)
        <div style="display:flex; justify-content:space-between; padding:.5rem 0; font-size:.85rem; color:var(--muted);">
            <span>Subtotal</span>
            <span>{{ $order->formatted_total }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; padding:.5rem 0; font-size:.85rem; color:var(--muted);">
            <span>Ongkir</span>
            <span>{{ $order->formatted_delivery_fee }}</span>
        </div>
        @endif
        <div style="display:flex; justify-content:space-between; padding:.8rem 0 0; font-weight:700; color:var(--gold-soft); border-top:1px solid rgba(200,151,58,.08); margin-top:.3rem;">
            <span>Total</span>
            <span>{{ $order->formatted_grand_total }}</span>
        </div>
    </div>

    <p style="font-size:.85rem; color:var(--muted); margin-bottom:2rem;">
        Simpan kode pesananmu untuk melacak status di halaman Lacak Pesanan.
    </p>
    @endif

    <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        @if($order)
        <a href="{{ route('order.receipt', $order) }}" class="btn btn-outline">📄 Download Struk</a>
        @endif
        <a href="{{ route('order.track') }}" class="btn btn-outline">Lacak Pesanan</a>
        <a href="{{ route('menu.index') }}" class="btn btn-gold">Pesan Lagi</a>
    </div>
</div>
@endsection