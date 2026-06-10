@extends('layouts.app')

@section('title', 'Menu — BrewNest Coffee')

@push('styles')
<style>
    .page-header {
        padding: 5rem 5% 3rem;
        background: linear-gradient(135deg, var(--surface) 0%, var(--bg) 100%);
        border-bottom: 1px solid rgba(200,151,58,.12);
    }

    .page-header h1 { font-size: clamp(2rem, 4vw, 3rem); color: var(--cream); }
    .page-header p  { color: var(--muted); margin-top: .5rem; }

    .menu-layout { display: grid; grid-template-columns: 240px 1fr; gap: 2.5rem; padding: 3rem 5%; }

    .sidebar { position: sticky; top: 90px; height: fit-content; }

    .filter-title { font-size: .75rem; text-transform: uppercase; letter-spacing: .15em; color: var(--gold); margin-bottom: 1rem; }

    .filter-link {
        display: block; padding: .65rem 1rem;
        border-radius: 7px;
        font-size: .9rem;
        color: var(--muted);
        margin-bottom: .3rem;
        transition: background .2s, color .2s;
    }

    .filter-link:hover, .filter-link.active {
        background: rgba(200,151,58,.1);
        color: var(--gold);
    }

    .search-box {
        display: flex; margin-bottom: 1.5rem;
    }

    .search-box input {
        flex: 1; padding: .7rem 1rem;
        background: var(--card);
        border: 1px solid rgba(200,151,58,.2);
        border-right: none;
        border-radius: 7px 0 0 7px;
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: .9rem;
        outline: none;
    }

    .search-box input:focus { border-color: var(--gold); }

    .search-box button {
        padding: .7rem 1.1rem;
        background: var(--gold);
        border: none;
        border-radius: 0 7px 7px 0;
        cursor: pointer;
        font-size: 1rem;
    }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.4rem; }

    .product-card {
        background: var(--card);
        border: 1px solid rgba(200,151,58,.1);
        border-radius: 12px;
        overflow: hidden;
        transition: transform .2s, border-color .2s;
    }

    .product-card:hover { transform: translateY(-3px); border-color: rgba(200,151,58,.3); }

    .product-img { width: 100%; height: 180px; object-fit: cover; }
    .product-placeholder { width:100%; height:180px; background:var(--surface); display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:var(--muted); }

    .product-body { padding: 1.1rem; }
    .product-cat  { font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: var(--gold); margin-bottom: .3rem; }
    .product-name { font-family: 'Playfair Display', serif; font-size: 1.05rem; color: var(--cream); margin-bottom: .3rem; }
    .product-desc { font-size: .82rem; color: var(--muted); margin-bottom: .8rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .product-foot { display: flex; align-items: center; justify-content: space-between; }
    .product-price { font-weight: 700; color: var(--gold-soft); }

    .badge-unavail { font-size:.7rem; background:rgba(192,57,43,.2); color:#e07070; padding:.2rem .6rem; border-radius:4px; }

    .pagination-wrap { margin-top: 2.5rem; display: flex; justify-content: center; gap: .5rem; }
    .pagination-wrap a, .pagination-wrap span {
        padding: .5rem 1rem; border-radius: 6px;
        border: 1px solid rgba(200,151,58,.2);
        font-size: .85rem;
        color: var(--muted);
        transition: .2s;
    }
    .pagination-wrap a:hover { border-color: var(--gold); color: var(--gold); }
    .pagination-wrap .active-page { background: var(--gold); color: var(--bg); border-color: var(--gold); }

    @media (max-width: 768px) {
        .menu-layout { grid-template-columns: 1fr; }
        .sidebar { position: static; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="container">
        <h1>Menu Kami</h1>
        <p>{{ $products->total() }} produk tersedia untuk kamu</p>
    </div>
</div>

<div class="menu-layout" style="max-width:1200px; margin:0 auto;">

    {{-- Sidebar Filter --}}
    <aside class="sidebar">
        <div class="filter-title">Pencarian</div>
        <form method="GET" action="{{ route('menu.index') }}" class="search-box">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
            <button type="submit">🔍</button>
        </form>

        <div class="filter-title">Kategori</div>
        <a href="{{ route('menu.index') }}" class="filter-link {{ !request('category') ? 'active' : '' }}">Semua Menu</a>
        @foreach($categories as $cat)
            <a href="{{ route('menu.index', ['category' => $cat->slug, 'search' => request('search')]) }}"
               class="filter-link {{ request('category') === $cat->slug ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </aside>

    {{-- Products --}}
    <div>
        @if($products->isEmpty())
            <div style="text-align:center; padding:4rem 0; color:var(--muted);">
                <div style="font-size:3rem; margin-bottom:1rem;">😕</div>
                <p>Produk tidak ditemukan.</p>
                <a href="{{ route('menu.index') }}" class="btn btn-outline btn-sm" style="margin-top:1.5rem;">Reset Filter</a>
            </div>
        @else
        <div class="product-grid">
            @foreach($products as $product)
            <div class="product-card">
                @if($product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                @else
                    <div class="product-placeholder">☕</div>
                @endif
                <div class="product-body">
                    <div class="product-cat">{{ $product->category->name }}</div>
                    <div class="product-name">{{ $product->name }}</div>
                    @if($product->description)
                        <div class="product-desc">{{ $product->description }}</div>
                    @endif
                    <div class="product-foot">
                        <span class="product-price">{{ $product->formatted_price }}</span>
                        @if(!$product->is_available)
                            <span class="badge-unavail">Habis</span>
                        @else
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-gold btn-sm">+ Keranjang</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $products->links('pagination::simple-bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

@endsection