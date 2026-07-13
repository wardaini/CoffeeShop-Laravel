@extends('layouts.app')
@section('title', 'Edit User')

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
    .form-group input:focus { border-color:var(--gold); }
    .form-error { font-size:.78rem; color:#e07070; margin-top:.3rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .danger-zone { margin-top:2rem; padding-top:1.5rem; border-top:1px solid rgba(192,57,43,.2); }
    .danger-title { font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:#e07070; margin-bottom:1rem; }
    .info-box { background:rgba(52,152,219,.08); border:1px solid rgba(52,152,219,.2); border-radius:8px; padding:.8rem 1rem; margin-bottom:1.2rem; font-size:.82rem; color:#74b9ff; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('it.dashboard') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">Edit User</h1>

    <div class="form-card">
        <div class="info-box">
            ℹ️ Kosongkan field password jika tidak ingin mengubah password.
        </div>

        <form method="POST" action="{{ route('it.users.update', $user) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
            </div>

            @if($user->employeeProfile)
            <div class="form-group">
                <label>Posisi</label>
                <select name="position">
                    <option value="Kasir" {{ $user->employeeProfile->position === 'Kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="Barista" {{ $user->employeeProfile->position === 'Barista' ? 'selected' : '' }}>Barista</option>
                    <option value="Dapur" {{ $user->employeeProfile->position === 'Dapur' ? 'selected' : '' }}>Dapur</option>
                    <option value="Kurir" {{ $user->employeeProfile->position === 'Kurir' ? 'selected' : '' }}>Kurir</option>
                    <option value="Cleaning Service" {{ $user->employeeProfile->position === 'Cleaning Service' ? 'selected' : '' }}>Cleaning Service</option>
                </select>
            </div>
            @endif

            <button type="submit" class="btn btn-gold" style="width:100%;">💾 Simpan Perubahan</button>
        </form>

        {{-- Danger Zone --}}
        <div class="danger-zone">
            <div class="danger-title">⚠️ Danger Zone</div>
            <div style="display:flex; gap:.8rem; flex-wrap:wrap;">
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('it.users.destroy', $user) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus akun {{ $user->name }}? Tidak bisa dibatalkan!')">
                        🗑 Hapus Akun
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection