@extends('layouts.app')
@section('title', 'Verifikasi OTP')

@push('styles')
<style>
    .auth-wrap { max-width: 420px; margin: 5rem auto; padding: 0 5%; }
    .auth-card { background: var(--card); border: 1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; text-align:center; }
    .auth-card h1 { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--cream); margin-bottom:.5rem; }
    .auth-card p.sub { color:var(--muted); font-size:.88rem; margin-bottom:2rem; }
    .otp-input {
        width:100%; padding:1rem; text-align:center; letter-spacing:1.5rem; font-size:1.6rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--gold); outline:none; margin-bottom:1.3rem;
    }
    .otp-input:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#e07070; margin-bottom:1rem; }
    .debug-otp { background: rgba(39,174,96,.1); border:1px solid rgba(39,174,96,.3); color:#6fcf97; padding:.8rem; border-radius:8px; font-size:.85rem; margin-bottom:1.5rem; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <h1>🔐 Verifikasi OTP</h1>
        <p class="sub">Masukkan 4 digit kode yang dikirim ke HP kamu</p>

        @if(session('otp_debug'))
            <div class="debug-otp">{{ session('otp_debug') }}</div>
        @endif

        @error('otp')<div class="form-error">{{ $message }}</div>@enderror

        <form method="POST" action="{{ route('customer.verify-otp') }}">
            @csrf
            <input type="text" name="otp" maxlength="4" inputmode="numeric" class="otp-input" placeholder="0000" autofocus required>
            <button type="submit" class="btn btn-gold" style="width:100%;">Verifikasi & Masuk</button>
        </form>
    </div>
</div>
@endsection