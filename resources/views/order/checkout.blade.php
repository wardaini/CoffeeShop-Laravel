@extends('layouts.app')
@section('title', 'Checkout — BrewNest')

@push('styles')
<style>
    .checkout-wrap { max-width: 960px; margin: 0 auto; padding: 4rem 5%; display: grid; grid-template-columns: 1fr 320px; gap: 2.5rem; }
    .form-title { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--cream); margin-bottom:1.8rem; }
    .form-group { margin-bottom: 1.3rem; }
    .form-group label { display:block; font-size:.82rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group textarea {
        width:100%; padding:.75rem 1rem;
        background:var(--card);
        border:1px solid rgba(200,151,58,.2);
        border-radius:8px;
        color:var(--text);
        font-family:'DM Sans',sans-serif;
        font-size:.95rem;
        outline:none; transition:border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#e07070; margin-top:.35rem; }
    .order-summary { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.6rem; height:fit-content; position:sticky; top:90px; }
    .sum-title { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--cream); margin-bottom:1.2rem; }
    .sum-item { display:flex; justify-content:space-between; font-size:.88rem; margin-bottom:.7rem; color:var(--muted); }
    .sum-total { display:flex; justify-content:space-between; font-weight:700; font-size:1.1rem; color:var(--gold-soft); border-top:1px solid rgba(200,151,58,.2); padding-top:.9rem; margin-top:.5rem; }
    @media(max-width:768px){ .checkout-wrap{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

<div style="padding:3rem 5% 1rem; max-width:960px; margin:0 auto;">
    <h1 style="font-size:2rem; color:var(--cream);">Checkout</h1>
</div>

<div class="checkout-wrap">
    <div>
        <div class="form-title">Data Pemesan</div>
        <form method="POST" action="{{ route('order.store') }}">
            @csrf

            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Nama kamu">
                @error('customer_name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" required placeholder="email@contoh.com">
                @error('customer_email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="0812xxxxxxxx">
            </div>

            <div class="form-group">
                <label>Catatan Pesanan</label>
                <textarea name="notes" rows="3" placeholder="Contoh: less sugar, no ice...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">
                Konfirmasi Pesanan →
            </button>
        </form>
    </div>

    <div>
        <div class="order-summary">
            <div class="sum-title">Pesananmu</div>
            @foreach($cart as $item)
            <div class="sum-item">
                <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="sum-total">
                <span>Total</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

@endsectionmese 