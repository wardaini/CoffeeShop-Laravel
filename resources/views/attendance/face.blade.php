@extends('layouts.app')
@section('title', 'Konfirmasi Absensi')

@push('styles')
<style>
    .face-wrap { max-width: 480px; margin: 4rem auto; padding: 0 5%; text-align: center; }
    .face-card { background: var(--card); border:1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2.5rem 2rem; }
    .face-card h1 { font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--cream); margin-bottom:.3rem; }
    .face-card .name { color:var(--gold); font-size:1.2rem; margin-bottom:.3rem; }
    .face-card .mode-badge { display:inline-block; background: rgba(200,151,58,.15); color:var(--gold); padding:.4rem 1.2rem; border-radius:20px; font-size:.85rem; margin-bottom:2rem; }
    .icon-big { font-size:4rem; margin-bottom:1.5rem; }
    .result-box { margin-top:1.5rem; padding:1rem; border-radius:8px; font-size:.9rem; display:none; }
    .result-success { background: rgba(39,174,96,.1); border:1px solid rgba(39,174,96,.3); color:#6fcf97; }
    .result-error { background: rgba(192,57,43,.1); border:1px solid rgba(192,57,43,.3); color:#e07070; }
</style>
@endpush

@section('content')
<div class="face-wrap">
    <div class="face-card">
        <div class="icon-big">{{ $mode === 'in' ? '🟢' : '🔴' }}</div>
        <h1>Konfirmasi Absensi</h1>
        <div class="name">{{ $profile->user->name }}</div>
        <div class="mode-badge">{{ $mode === 'in' ? 'Absen Masuk' : 'Absen Keluar' }}</div>

        <p style="color:var(--muted); font-size:.88rem; margin-bottom:1.5rem;">
            Tekan tombol di bawah untuk mencatat {{ $mode === 'in' ? 'jam masuk' : 'jam keluar' }} kamu hari ini.
        </p>

        <button id="confirmBtn" class="btn btn-gold" style="width:100%;">
            ✅ Konfirmasi {{ $mode === 'in' ? 'Absen Masuk' : 'Absen Keluar' }}
        </button>

        <div id="result" class="result-box"></div>
    </div>
</div>

<script>
    const resultBox = document.getElementById('result');
    const confirmBtn = document.getElementById('confirmBtn');

    confirmBtn.addEventListener('click', () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Memproses...';

        fetch('{{ route("attendance.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                employee_code: '{{ $profile->employee_code }}',
                mode: '{{ $mode }}',
            })
        })
        .then(res => res.json())
        .then(data => {
            resultBox.style.display = 'block';
            resultBox.className = 'result-box ' + (data.success ? 'result-success' : 'result-error');
            resultBox.textContent = data.message;

            if (data.success) {
                confirmBtn.style.display = 'none';
                setTimeout(() => window.location.href = '{{ route("home") }}', 2000);
            } else {
                confirmBtn.disabled = false;
                confirmBtn.textContent = '✅ Konfirmasi {{ $mode === "in" ? "Absen Masuk" : "Absen Keluar" }}';
            }
        });
    });
</script>
@endsection