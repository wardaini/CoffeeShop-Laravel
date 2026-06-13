@extends('layouts.app')
@section('title', 'Login Karyawan')

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
    .checkbox-row { display:flex; align-items:center; gap:.5rem; margin-bottom:1.3rem; font-size:.85rem; color:var(--muted); }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <h1>👨‍🍳 Login Karyawan</h1>
        <p class="sub">Masuk dengan email atau no HP yang terdaftar</p>

        @error('login')<div class="form-error" style="margin-bottom:1rem;">{{ $message }}</div>@enderror

        <form method="POST" action="{{ route('employee.login.store') }}">
            @csrf
            <div class="form-group">
                <label>Email / No HP</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="checkbox-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0; text-transform:none; letter-spacing:0;">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;">Masuk</button>
        </form>

        <div class="auth-links">
            Belum punya akun? <a href="{{ route('employee.register') }}">Daftar sebagai Karyawan</a><br>
            Pelanggan? <a href="{{ route('customer.login') }}">Login di sini</a>
        </div>
    </div>
</div>
@endsection