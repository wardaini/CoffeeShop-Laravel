@extends('layouts.app')
@section('title', 'Keranjang — BrewNest')

@push('styles')
<style>
    .cart-wrap { max-width: 1000px; margin: 0 auto; padding: 4rem 5%; display: grid; grid-template-columns: 1fr 320px; gap: 2.5rem; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: .8rem 1rem; text-align: left; font-size: .75rem; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); border-bottom: 1px solid rgba(200,151,58,.15); }
    td { padding: 1rem; border-bottom: 1px solid rgba(200,151,58,.08); vertical-align: middle; color: var(--text); }
    .item-name { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--cream); }
    .item-price { color: var(--muted); font-size: .85rem; }
    .qty-form { display: flex; align-items: center; gap: .5rem; }
    .qty-form input { width: 55px; text-align: center; padding: .4rem; background: var(--card); border: 1px solid rgba(200,151,58,.25); border-radius: 6px; color: var(--text); }
    .qty-form button { padding: .4rem .8rem; font-size: .8rem; }
    .remove-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 1.1rem; transition: color .2s; }
    .remove-btn:hover { color: var(--danger); }
    .summary-card { background: var(--card); border: 1px solid rgba(200,151,58,.15); border-radius: 12px; padding: 1.8rem; height: fit-content; position: sticky; top: 90px; }
    .summary-title { font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--cream); margin-bottom: 1.5rem; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: .9rem; font-size: .9rem; }
    .summary-row.total { font-weight: 700; font-size: 1.1rem; color: var(--gold-soft); border-top: 1px solid rgba(200,151,58,.2); padding-top: .9rem; margin-top: .5rem; }
    .empty-cart { text-align: center; padding: 5rem 0; color: var(--muted); }
    .empty-cart div { font-size: 3.5rem; margin-bottom: 1rem; }
    @media(max-width:768px){ .cart-wrap{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div style="padding: 3rem 5% 1rem; max-width:1000px; margin:0 auto;">
    <h1 style="font-size:2rem; color:var(--cream);">Keranjang Belanja</h1>
</div>

@if(empty($cart))
    <div class="empty-cart">
        <div>🛒</div>
        <p>Keranjangmu masih kosong.</p>
        <a href="{{ route('menu.index') }}" class="btn btn-gold" style="margin-top:1.5rem;">Lihat Menu</a>
    </div>
@else
<div class="cart-wrap">
    <div>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item['name'] }}</div>
                    </td>
                    <td class="item-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('cart.update', $id) }}" class="qty-form">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="20">
                            <button type="submit" class="btn btn-outline btn-sm">Update</button>
                        </form>
                    </td>
                    <td style="color:var(--gold-soft); font-weight:600;">
                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('cart.remove', $id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="remove-btn" title="Hapus">✕</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <div class="summary-card">
            <div class="summary-title">Ringkasan</div>
            @foreach($cart as $item)
            <div class="summary-row">
                <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="summary-row total">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('order.checkout') }}" class="btn btn-gold" style="width:100%; text-align:center; margin-top:1.5rem; display:block;">
                Lanjut Checkout →
            </a>
        </div>
    </div>
</div>
@endif

@endsection