@extends('layouts.app')
@section('title', 'Scan Absensi')

@push('styles')
<style>
    .scan-wrap { max-width: 480px; margin: 3rem auto; padding: 0 5%; text-align: center; }
    .scan-card { background: var(--card); border:1px solid rgba(200,151,58,.15); border-radius: 14px; padding: 2rem; }
    .scan-card h1 { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--cream); margin-bottom:.5rem; }
    .scan-card p { color:var(--muted); font-size:.88rem; margin-bottom:1.5rem; }
    #reader { border-radius:10px; overflow:hidden; border:2px solid rgba(200,151,58,.3); }
    .result-box { margin-top:1.5rem; padding:1rem; border-radius:8px; font-size:.9rem; display:none; }
    .result-success { background: rgba(39,174,96,.1); border:1px solid rgba(39,174,96,.3); color:#6fcf97; }
    .result-error { background: rgba(192,57,43,.1); border:1px solid rgba(192,57,43,.3); color:#e07070; }
</style>
@endpush

@section('content')
<div class="scan-wrap">
    <div class="scan-card">
        <h1>📷 Scan Barcode Absensi</h1>
        <p>Arahkan kamera ke QR Code karyawan</p>

        <div id="reader"></div>

        <div id="result" class="result-box"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result');

    function showResult(message, isSuccess) {
        resultBox.style.display = 'block';
        resultBox.className = 'result-box ' + (isSuccess ? 'result-success' : 'result-error');
        resultBox.textContent = message;
    }

    const html5QrCode = new Html5Qrcode("reader");
    let isProcessing = false;

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        fetch('{{ route("attendance.verify-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ employee_code: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showResult('✅ ' + data.name + ' terdeteksi, mengarahkan ke verifikasi wajah...', true);
                html5QrCode.stop();
                setTimeout(() => window.location.href = data.redirect, 1000);
            } else {
                showResult('❌ ' + data.message, false);
                setTimeout(() => { isProcessing = false; }, 2000);
            }
        });
    }

    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 220 },
        onScanSuccess
    ).catch(err => {
        showResult('Gagal mengakses kamera: ' + err, false);
    });
</script>
@endsection