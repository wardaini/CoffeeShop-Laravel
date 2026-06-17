@extends('layouts.app')
@section('title', 'Manajemen Produk')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); vertical-align:middle; }
    .badge-available { background:rgba(39,174,96,.15); color:#6fcf97; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .badge-unavailable { background:rgba(192,57,43,.15); color:#e07070; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .action-group { display:flex; gap:.4rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">Manajemen Produk</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-gold btn-sm">+ Tambah Produk</a>
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td style="font-size:1.4rem;">{{ $product->display_icon }}</td>
                    <td>{{ $product->name }}</td>
                    <td style="color:var(--muted);">{{ $product->category->name }}</td>
                    <td>{{ $product->formatted_price }}</td>
                    <td>
                        <span class="{{ $product->is_available ? 'badge-available' : 'badge-unavailable' }}">
                            {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm">✏️ Edit</a>
                            <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">
                                    {{ $product->is_available ? '🚫 Nonaktif' : '✅ Aktif' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;">{{ $products->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection