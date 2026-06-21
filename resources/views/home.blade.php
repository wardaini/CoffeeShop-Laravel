@extends('layouts.app')

@section('title', 'BrewNest Coffee — Lhokseumawe')

@push('styles')
<style>
    /* Hero */
    .hero {
        min-height: 92vh;
        display: flex; flex-direction: column; justify-content: center;
        padding: 0 5%;
        background:
            linear-gradient(to right, rgba(14,10,7,.95) 55%, rgba(14,10,7,.5)),
            url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1400&q=80') center/cover no-repeat;
    }

    .hero-label {
        text-transform: uppercase;
        letter-spacing: .2em;
        font-size: .78rem;
        color: var(--gold);
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .hero h1 {
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        line-height: 1.15;
        color: var(--cream);
        max-width: 640px;
        margin-bottom: 1.2rem;
    }

    .hero h1 em { font-style: italic; color: var(--gold-soft); }

    .hero p {
        max-width: 480px;
        color: var(--muted);
        margin-bottom: 2.5rem;
        font-size: 1.05rem;
    }

    .hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }

    /* Stats strip */
    .stats {
        display: flex; gap: 0;
        background: var(--surface);
        border-top: 1px solid rgba(200,151,58,.15);
        border-bottom: 1px solid rgba(200,151,58,.15);
    }

    .stat-item {
        flex: 1; text-align: center;
        padding: 2rem 1rem;
        border-right: 1px solid rgba(200,151,58,.1);
    }

    .stat-item:last-child { border-right: none; }

    .stat-num {
        font-family: 'Playfair Display', serif;
        font-size: 2.2rem;
        color: var(--gold);
        display: block;
    }

    .stat-lbl { font-size: .8rem; color: var(--muted); text-transform: uppercase; letter-spacing: .1em; }

    /* Section */
    .section { padding: 6rem 5%; }

    .section-tag {
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: .2em;
        font-size: .75rem;
        color: var(--gold);
        border: 1px solid rgba(200,151,58,.35);
        border-radius: 20px;
        padding: .25rem .85rem;
        margin-bottom: 1rem;
    }

    .section-title { font-size: clamp(1.8rem, 3vw, 2.6rem); color: var(--cream); margin-bottom: .5rem; }
    .section-sub   { color: var(--muted); max-width: 480px; margin-bottom: 3rem; }

    /* Product grid */
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }

    .product-card {
        background: var(--card);
        border: 1px solid rgba(200,151,58,.1);
        border-radius: 12px;
        overflow: hidden;
        transition: transform .25s, border-color .25s;
    }

    .product-card:hover { transform: translateY(-4px); border-color: rgba(200,151,58,.35); }

    .product-img { width: 100%; height: 200px; object-fit: cover; background: var(--surface); }

    .product-img-placeholder {
        width: 100%; height: 200px;
        display: flex; align-items: center; justify-content: center;
        background: var(--surface);
        font-size: 3rem;
        color: var(--muted);
    }

    .product-body { padding: 1.2rem; }

    .product-cat {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: var(--gold);
        margin-bottom: .35rem;
    }

    .product-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.15rem;
        color: var(--cream);
        margin-bottom: .4rem;
    }

    .product-price { font-size: 1.1rem; font-weight: 700; color: var(--gold-soft); margin-bottom: 1rem; }

    .product-actions { display: flex; gap: .6rem; }

    /* Categories */
    .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }

    .cat-card {
        background: var(--card);
        border: 1px solid rgba(200,151,58,.12);
        border-radius: 10px;
        padding: 2rem 1.5rem;
        text-align: center;
        transition: background .2s, border-color .2s;
        cursor: pointer;
    }

    .cat-card:hover { background: rgba(200,151,58,.08); border-color: rgba(200,151,58,.35); }

    .cat-icon { font-size: 2.5rem; margin-bottom: .8rem; }

    .cat-name { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--cream); }

    .cat-count { font-size: .82rem; color: var(--muted); margin-top: .2rem; }

    /* CTA banner */
    .cta-banner {
        background: linear-gradient(135deg, #1A1410 0%, #2A1F15 100%);
        border: 1px solid rgba(200,151,58,.2);
        border-radius: 16px;
        padding: 4rem 5%;
        text-align: center;
        margin: 0 5% 6rem;
    }

    .cta-banner h2 { font-size: clamp(1.8rem, 3vw, 2.4rem); color: var(--cream); margin-bottom: .8rem; }
    .cta-banner p  { color: var(--muted); margin-bottom: 2rem; }

    @media (max-width: 600px) {
        .stats { flex-wrap: wrap; }
        .stat-item { flex: 0 0 50%; border-bottom: 1px solid rgba(200,151,58,.1); }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-label">Specialty Coffee · Lhokseumawe</div>
    <h1>Setiap tegukan<br>adalah <em>cerita</em><br>yang baru.</h1>
    <p>Biji kopi pilihan, diseduh dengan penuh perhatian. Nikmati pengalaman kopi yang tak terlupakan di BrewNest.</p>
    <div class="hero-btns">
    <a href="{{ route('menu.index') }}" class="btn btn-gold">Lihat Menu</a>
    <a href="{{ route('order.track') }}" class="btn btn-outline">Lacak Pesanan</a>
    </div>

    @guest
    <div style="margin-top:1.5rem; display:flex; gap:.8rem; flex-wrap:wrap;">
        <a href="{{ route('employee.login') }}" style="font-size:.8rem; color:var(--muted); text-decoration:underline;">Login Karyawan</a>
        <span style="color:var(--muted);">·</span>
        <a href="{{ route('staff.login') }}" style="font-size:.8rem; color:var(--muted); text-decoration:underline;">Login Admin/Bos/IT</a>
    </div>
    @endguest
</section>

{{-- Stats --}}
<div class="stats">
    <div class="stat-item"><span class="stat-num">50+</span><span class="stat-lbl">Menu Tersedia</span></div>
    <div class="stat-item"><span class="stat-num">4</span><span class="stat-lbl">Kategori</span></div>
    <div class="stat-item"><span class="stat-num">1K+</span><span class="stat-lbl">Pelanggan Puas</span></div>
    <div class="stat-item"><span class="stat-num">5★</span><span class="stat-lbl">Rating</span></div>
</div>

{{-- Featured Products --}}
<section class="section">
    <div class="container">
        <span class="section-tag">Menu Unggulan</span>
        <h2 class="section-title">Pilihan Terbaik Kami</h2>
        <p class="section-sub">Dikurasi dengan cermat oleh barista kami untuk pengalaman terbaik kamu.</p>

        <div class="product-grid">
            @foreach($featured as $product)
            <div class="product-card">
                @if($product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                @else
                    <div class="product-img-placeholder">☕</div>
                @endif
                <div class="product-body">
                    <div class="product-cat">{{ $product->category->name }}</div>
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="product-price">{{ $product->formatted_price }}</div>
                    <div class="product-actions">
                        <a href="{{ route('menu.show', $product->slug) }}" class="btn btn-outline btn-sm">Detail</a>
                        <form method="POST" action="{{ route('cart.add', $product) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-gold btn-sm">+ Keranjang</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align:center; margin-top:3rem;">
            <a href="{{ route('menu.index') }}" class="btn btn-outline">Lihat Semua Menu →</a>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="section" style="padding-top:0">
    <div class="container">
        <span class="section-tag">Kategori</span>
        <h2 class="section-title">Temukan Favoritmu</h2>
        <p class="section-sub">Dari kopi espresso klasik hingga minuman non-kopi yang menyegarkan.</p>

        <div class="cat-grid">
            @foreach($categories as $cat)
            <a href="{{ route('menu.index', ['category' => $cat->slug]) }}" class="cat-card">
                <div class="cat-icon">
                    @php $icons = ['Kopi'=>'☕','Non-Kopi'=>'🍵','Makanan'=>'🍰','Snack'=>'🥐']; @endphp
                    {{ $icons[$cat->name] ?? '🥤' }}
                </div>
                <div class="cat-name">{{ $cat->name }}</div>
                <div class="cat-count">{{ $cat->products_count }} produk</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<div class="cta-banner">
    <h2>Siap Memesan Sekarang?</h2>
    <p>Pilih menu favoritmu dan nikmati kelezatannya.</p>
    <a href="{{ route('menu.index') }}" class="btn btn-gold">Pesan Sekarang</a>
</div>

@endsection