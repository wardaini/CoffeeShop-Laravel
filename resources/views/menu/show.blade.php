@extends('layouts.app')

@section('title', $product->name . ' — BrewNest')

@push('styles')
<style>
    .detail-wrap { max-width: 1100px; margin: 0 auto; padding: 4rem 5%; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; }
    .detail-img { border-radius: 14px; overflow: hidden; background: var(--card); display: flex; align-items: center; justify-content: center; min-height: 380px; }
    .detail-img img { width: 100%; height: 420px; object-fit: cover; }
    .detail-img .ph { font-size: 6rem; color: var(--muted); }
    .detail-badge { font-size: .75rem; text-transform: uppercase; letter-spacing: .15em; color: var(--gold); margin-bottom: .8rem; }
    .detail-name { font-size: clamp(1.8rem, 3vw, 2.5rem); color: var(--cream); margin-bottom: .8rem; }
    .detail-price { font-size: 1.8rem; font-weight: 700; color: var(--gold-soft); margin-bottom: 1.5rem; }
    .detail-desc { color: var(--muted); line-height: 1.7; margin-bottom: 2rem; }
    .qty-wrap { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
    .qty-wrap label { font-size: .85rem; color: var(--muted); text-transform: uppercase; letter-spacing: .1em; }
    .qty-input { width: 70px; padding: .6rem; text-align: center; background: var(--card); border: 1px solid rgba(200,151,58,.3); border-radius: 7px; color: var(--text); font-size: 1rem; }
    .related-section { padding: 0 5% 5rem; max-width: 1100px; margin: 0 auto; }
    .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.2rem; margin-top: 1.5rem; }
    .mini-card { background: var(--card); border: 1px solid rgba(200,151,58,.1); border-radius: 10px; overflow: hidden; transition: transform .2s; }
    .mini-card:hover { transform: translateY(-3px); }
    .mini-card img { width:100%; height:150px; object-fit:cover; }
    .mini-ph { width:100%; height:150px; background:var(--surface); display:flex; align-items:center; justify-content:center; font-size:2rem; color:var(--muted); }
    .mini-body { padding: .9rem; }
    .mini-name { font-family:'Playfair Display',serif; font-size:1rem; color:var(--cream); }
    .mini-price { font-size:.9rem; color:var(--gold-soft); margin-top:.3rem; }
    @media(max-width:768px){ .detail-wrap{ grid-template-columns:1fr; gap:2rem; } }
</style>
@endpush

@section('content')

<div class="detail-wrap">
    <div class="detail-img">
        @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
        @else
            <div class="ph">☕</div>
        @endif
    </div>

    <div>
        <div class="detail-badge">{{ $product->category->name }}</div>
        <h1 class="detail-name">{{ $product->name }}</h1>
        <div class="detail-price">{{ $product->formatted_price }}</div>
        <p class="detail-desc">{{ $product->description ?? 'Produk kopi pilihan yang disajikan dengan bahan berkualitas tinggi.' }}</p>

        @if($product->is_available)
        <form method="POST" action="{{ route('cart.add', $product) }}">
            @csrf
            <div class="qty-wrap">
                <label for="qty">Jumlah</label>
                <input type="number" id="qty" name="quantity" value="1" min="1" max="20" class="qty-input">
            </div>
            <button type="submit" class="btn btn-gold">Tambah ke Keranjang 🛒</button>
        </form>
        @else
            <div style="color:#e07070; font-size:.9rem; padding:.8rem; background:rgba(192,57,43,.1); border-radius:8px; display:inline-block;">Produk sedang tidak tersedia</div>
        @endif

        <div style="margin-top:1.5rem;">
            <a href="{{ route('menu.index') }}" style="color:var(--muted); font-size:.85rem;">← Kembali ke Menu</a>
        </div>
    </div>
</div>

@if($related->isNotEmpty())
<div class="related-section">
    <h3 style="font-family:'Playfair Display',serif; color:var(--cream);">Produk Serupa</h3>
    <div class="related-grid">
        @foreach($related as $r)
        <a href="{{ route('menu.show', $r->slug) }}" class="mini-card">
            @if($r->image)
                <img src="{{ $r->image_url }}" alt="{{ $r->name }}">
            @else
                <div class="mini-ph">☕</div>
            @endif
            <div class="mini-body">
                <div class="mini-name">{{ $r->name }}</div>
                <div class="mini-price">{{ $r->formatted_price }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection