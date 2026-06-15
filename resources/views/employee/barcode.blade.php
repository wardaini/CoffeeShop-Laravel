@extends('layouts.app')
@section('title', 'Barcode Absensi')

@push('styles')
<style>
    .barcode-wrap { max-width: 420px; margin: 4rem auto; padding: 0 5%; text-align: center; }
    .barcode-card { background: var(--card); border:1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; }
    .barcode-card h1 { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--cream); margin-bottom:.5rem; }
    .barcode-card p { color:var(--muted); font-size:.88rem; margin-bottom:1.5rem; }
    .qr-box { background:#fff; border-radius:12px; padding:1.5rem; display:inline-block; margin-bottom:1.5rem; }
    .emp-code { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--gold); letter-spacing:.1em; }
    .emp-name { color:var(--cream); font-size:1rem; margin-top:.3rem; }
</style>
@endpush

@section('content')
<div class="barcode-wrap">
    <div class="barcode-card">
        <h1>🪪 Barcode Absensi</h1>
        <p>Tunjukkan kode ini ke kamera scanner saat absen masuk/keluar</p>

        <div class="qr-box">
            {!! QrCode::size(200)->generate($profile->employee_code) !!}
        </div>

        <div class="emp-code">{{ $profile->employee_code }}</div>
        <div class="emp-name">{{ auth()->user()->name }} — {{ $profile->position }}</div>

        <div style="margin-top:2rem; display:flex; gap:.8rem; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('attendance.scan') }}" class="btn btn-outline btn-sm">🔍 Buka Halaman Scan</a>
            <a href="{{ route('employee.attendance.history') }}" class="btn btn-outline btn-sm">📋 Riwayat Absensi</a>
        </div>
    </div>
</div>
@endsection