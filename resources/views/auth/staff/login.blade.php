@extends('layouts.app')
@section('title', 'Login Staff')

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
    .form-error { font-size:.8rem; color:#e07070; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <h1>🔧 Login Internal</h1>
        <p class="sub">Khusus Admin, Bos & IT</p>

        @error('email')<div class="form-error">{{ $message }}</div>@enderror

        <form method="POST" action="{{ route('staff.login.store') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;">Masuk</button>
        </form>
    </div>
</div>
@endsection