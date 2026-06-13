@extends('layouts.app')
@section('title', 'Daftar Karyawan')

@push('styles')
<style>
    .auth-wrap { max-width: 560px; margin: 4rem auto; padding: 0 5%; }
    .auth-card { background: var(--card); border: 1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; }
    .auth-card h1 { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--cream); margin-bottom:.3rem; text-align:center; }
    .auth-card p.sub { text-align:center; color:var(--muted); font-size:.88rem; margin-bottom:2rem; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group select {
        width:100%; padding:.8rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-size:.95rem; outline:none;
    }
    .form-group input:focus, .form-group select:focus { border-color:var(--gold); }
    .form-group input[type=file] { padding:.6rem; font-size:.85rem; }
    .form-error { font-size:.78rem; color:#e07070; margin-top:.35rem; }
    .auth-links { text-align:center; margin-top:1.5rem; font-size:.85rem; color:var(--muted); }
    .auth-links a { color:var(--gold); }
    .section-label { font-size:.75rem; text-transform:uppercase; letter-spacing:.15em; color:var(--gold); margin: 1.5rem 0 1rem; border-bottom:1px solid rgba(200,151,58,.15); padding-bottom:.5rem; }
    @media(max-width:600px){ .form-row{ grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <h1>👨‍🍳 Daftar sebagai Karyawan</h1>
        <p class="sub">Lengkapi data diri untuk verifikasi oleh Admin/IT</p>

        <form method="POST" action="{{ route('employee.register.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="section-label">Data Akun</div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <div class="section-label">Posisi & Identitas</div>

            <div class="form-group">
                <label>Posisi yang Dilamar</label>
                <select name="position" required>
                    <option value="">-- Pilih Posisi --</option>
                    <option value="Kasir">Kasir</option>
                    <option value="Barista">Barista</option>
                    <option value="Kurir">Kurir / Delivery</option>
                    <option value="Dapur">Dapur</option>
                    <option value="Cleaning">Cleaning Service</option>
                </select>
                @error('position')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Nomor KTP</label>
                <input type="text" name="ktp_number" value="{{ old('ktp_number') }}" maxlength="16" required>
                @error('ktp_number')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Foto KTP</label>
                    <input type="file" name="ktp_photo" accept="image/*" required>
                    @error('ktp_photo')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Foto Wajah (Selfie)</label>
                    <input type="file" name="face_photo" accept="image/*" capture="user" required>
                    @error('face_photo')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%; margin-top:.5rem;">Daftar Sekarang</button>
        </form>

        <div class="auth-links">
            Sudah punya akun? <a href="{{ route('employee.login') }}">Login di sini</a>
        </div>
    </div>
</div>
@endsection