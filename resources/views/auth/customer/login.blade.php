@extends('layouts.app')
@section('title', 'Login Pelanggan')

@push('styles')
<style>
    .auth-wrap { max-width: 420px; margin: 5rem auto; padding: 0 5%; }
    .auth-card { background: var(--card); border: 1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; }
    .auth-card h1 { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--cream); margin-bottom:.5rem; text-align:center; }
    .auth-card p.sub { text-align:center; color:var(--muted); font-size:.88rem; margin-bottom:2rem; }
    .form-group { margin-bottom: 1.3rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input {
        width:100%; padding:.85rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-size:1rem; outline:none;
    }
    .form-group input:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#e07070; margin-top:.4rem; }
    .auth-links { text-align:center; margin-top:1.5rem; font-size:.85rem; color:var(--muted); }
    .auth-links a { color:var(--gold); }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <h1>☕ Masuk sebagai Pelanggan</h1>
        <p class="sub">Masukkan nomor HP kamu, kami akan kirim kode verifikasi</p>

        <form method="POST" action="{{ route('customer.send-otp') }}">
            @csrf
            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                @error('phone')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Kirim Kode OTP</button>
        </form>

        <div class="auth-links">
            Karyawan? <a href="{{ route('employee.login') }}">Login di sini</a><br>
            Admin/Bos/IT? <a href="{{ route('staff.login') }}">Login di sini</a>
        </div>
    </div>
</div>
@endsection