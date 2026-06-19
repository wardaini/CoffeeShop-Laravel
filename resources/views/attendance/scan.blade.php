@extends('layouts.app')
@section('title', 'Scan Absensi')

@push('styles')
<style>
    .scan-wrap { max-width: 480px; margin: 3rem auto; padding: 0 5%; text-align: center; }
    .scan-card { background: var(--card); border:1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; }
    .scan-card h1 { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--cream); margin-bottom:.5rem; }
    .scan-card p { color:var(--muted); font-size:.9rem; margin-bottom:2rem; }
    .qr-box { background:#fff; border-radius:12px; padding:1.5rem; display:inline-block; margin-bottom:1.5rem; }
</style>
@endpush

@section('content')
<div class="scan-wrap">
    <div class="scan-card">
        <h1>🪪 Absensi Karyawan</h1>
        <p>Scan QR ini dengan HP kamu, atau klik tombol di bawah untuk lanjut absen</p>

        <div class="qr-box">
            {!! QrCode::size(220)->generate(route('attendance.select-employee')) !!}
        </div>

        <a href="{{ route('attendance.select-employee') }}" class="btn btn-gold" style="width:100%;">
            ➡️ Lanjut Absen Sekarang
        </a>
    </div>
</div>
@endsection