@extends('layouts.app')
@section('title', 'Verifikasi Wajah')

@push('styles')
<style>
    .face-wrap { max-width: 480px; margin: 3rem auto; padding: 0 5%; text-align: center; }
    .face-card { background: var(--card); border:1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2rem; }
    .face-card h1 { font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--cream); margin-bottom:.3rem; }
    .face-card .name { color:var(--gold); font-size:1.1rem; margin-bottom:.3rem; }
    .face-card .mode-badge { display:inline-block; background: rgba(200,151,58,.15); color:var(--gold); padding:.3rem 1rem; border-radius:20px; font-size:.8rem; margin-bottom:1.5rem; }
    #video { width:100%; border-radius:10px; border:2px solid rgba(200,151,58,.3); transform: scaleX(-1); }
    #canvas { display:none; }
    .result-box { margin-top:1.5rem; padding:1rem; border-radius:8px; font-size:.9rem; display:none; }
    .result-success { background: rgba(39,174,96,.1); border:1px solid rgba(39,174,96,.3); color:#6fcf97; }
    .result-error { background: rgba(192,57,43,.1); border:1px solid rgba(192,57,43,.3); color:#e07070; }
</style>
@endpush

@section('content')
<div class="face-wrap">
    <div class="face-card">
        <h1>🤳 Verifikasi Wajah</h1>
        <div class="name">{{ $profile->user->name }}</div>
        <div class="mode-badge">{{ $mode === 'in' ? '🟢 Absen Masuk' : '🔴 Absen Keluar' }}</div>

        <video id="video" autoplay playsinline></video>
        <canvas id="canvas"></canvas>

        <div style="margin-top:1.5rem;">
            <button id="captureBtn" class="btn btn-gold" style="width:100%;">📸 Ambil Foto & Absen</button>
        </div>

        <div id="result" class="result-box"></div>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const resultBox = document.getElementById('result');
    const captureBtn = document.getElementById('captureBtn');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
        .then(stream => { video.srcObject = stream; })
        .catch(err => {
            resultBox.style.display = 'block';
            resultBox.className = 'result-box result-error';
            resultBox.textContent = 'Gagal mengakses kamera: ' + err;
        });

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        // Mirror agar sesuai tampilan video
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const photoData = canvas.toDataURL('image/png');

        captureBtn.disabled = true;
        captureBtn.textContent = 'Memproses...';

        fetch('{{ route("attendance.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                employee_code: '{{ $profile->employee_code }}',
                photo: photoData,
                mode: '{{ $mode }}',
            })
        })
        .then(res => res.json())
        .then(data => {
            resultBox.style.display = 'block';
            resultBox.className = 'result-box ' + (data.success ? 'result-success' : 'result-error');
            resultBox.textContent = data.message;

            if (data.success) {
                const stream = video.srcObject;
                stream.getTracks().forEach(track => track.stop());
                captureBtn.style.display = 'none';

                setTimeout(() => window.location.href = '{{ route("home") }}', 2500);
            } else {
                captureBtn.disabled = false;
                captureBtn.textContent = '📸 Ambil Foto & Absen';
            }
        });
    });
</script>
@endsection