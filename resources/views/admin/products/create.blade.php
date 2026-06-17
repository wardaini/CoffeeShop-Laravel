@extends('layouts.app')
@section('title', 'Tambah Produk')

@push('styles')
<style>
    .wrap { max-width:600px; margin:3rem auto; padding:0 5%; }
    .form-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:14px; padding:2rem; }
    .form-group { margin-bottom:1.2rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; padding:.75rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-family:'DM Sans',sans-serif;
        font-size:.95rem; outline:none;
    }
    .form-group input:focus, .form-group select:focus { border-color:var(--gold); }
    .form-error { font-size:.78rem; color:#e07070; margin-top:.3rem; }
    .check-row { display:flex; gap:1.5rem; }
    .check-item { display:flex; align-items:center; gap:.5rem; font-size:.9rem; color:var(--text); }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('admin.products.index') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">Tambah Produk</h1>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Kategori *</label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Harga (Rp) *</label>
                <input type="number" name="price" value="{{ old('price') }}" min="0" step="500" required>
                @error('price')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Icon/Emoji <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional, contoh: ☕)</span></label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="☕" maxlength="10">
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>Foto Produk</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <div class="check-row">
                    <label class="check-item">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', '1') ? 'checked' : '' }}>
                        Tersedia
                    </label>
                    <label class="check-item">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        Tampilkan di Featured
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Simpan Produk</button>
        </form>
    </div>
</div>
@endsection