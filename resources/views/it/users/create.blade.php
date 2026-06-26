@extends('layouts.app')
@section('title', 'Tambah User Baru')

@push('styles')
<style>
    .wrap { max-width:560px; margin:3rem auto; padding:0 5%; }
    .form-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:14px; padding:2rem; }
    .form-group { margin-bottom:1.2rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group select {
        width:100%; padding:.75rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-size:.95rem; outline:none;
    }
    .form-error { font-size:.78rem; color:#e07070; margin-top:.3rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('it.dashboard') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">Tambah User Baru</h1>

    <div class="form-card">
        <form method="POST" action="{{ route('it.users.store') }}">
            @csrf

            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Role *</label>
                <select name="role" required onchange="togglePosition(this.value)">
                    <option value="">-- Pilih Role --</option>
                    <optgroup label="Staff">
                        <option value="admin">Admin</option>
                        <option value="bos">Bos</option>
                        <option value="it">IT</option>
                    </optgroup>
                    <optgroup label="Karyawan">
                        <option value="kasir">Kasir</option>
                        <option value="barista">Barista</option>
                        <option value="dapur">Dapur</option>
                        <option value="kurir">Kurir</option>
                        <option value="cleaning">Cleaning Service</option>
                    </optgroup>
                </select>
                @error('role')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group" id="positionField" style="display:none;">
                <label>Posisi</label>
                <input type="text" name="position" value="{{ old('position') }}" placeholder="Kasir, Barista, Kurir, dll">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="8">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Buat Akun</button>
        </form>
    </div>
</div>

<script>
function togglePosition(role) {
    const staffRoles = ['admin', 'bos', 'it'];
    const posField = document.getElementById('positionField');
    posField.style.display = staffRoles.includes(role) ? 'none' : 'block';

    // Auto-fill position berdasarkan role
    const posInput = document.querySelector('input[name="position"]');
    const map = {
        kasir: 'Kasir', barista: 'Barista', dapur: 'Dapur',
        kurir: 'Kurir', cleaning: 'Cleaning Service'
    };
    if (map[role]) posInput.value = map[role];
}
</script>
@endsection