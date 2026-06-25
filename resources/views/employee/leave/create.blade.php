@extends('layouts.app')
@section('title', 'Ajukan Cuti')

@push('styles')
<style>
    .wrap { max-width:560px; margin:3rem auto; padding:0 5%; }
    .form-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:14px; padding:2rem; }
    .form-group { margin-bottom:1.2rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; padding:.75rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-family:'DM Sans',sans-serif;
        font-size:.95rem; outline:none;
    }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-error { font-size:.78rem; color:#e07070; margin-top:.3rem; }
    .info-box { background:rgba(52,152,219,.08); border:1px solid rgba(52,152,219,.2); border-radius:8px; padding:.9rem 1rem; margin-bottom:1.5rem; font-size:.85rem; color:#74b9ff; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('employee.leave.index') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">Ajukan Cuti / Izin</h1>

    <div class="info-box">
        ℹ️ Pengajuan akan dikirim ke Admin untuk disetujui. Jika disetujui, absensi kamu di hari tersebut otomatis tercatat sebagai izin.
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('employee.leave.store') }}">
            @csrf

            <div class="form-group">
                <label>Jenis *</label>
                <select name="type" required>
                    <option value="cuti">🏖️ Cuti</option>
                    <option value="izin">📋 Izin</option>
                    <option value="sakit">🏥 Sakit</option>
                </select>
                @error('type')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required min="{{ now()->format('Y-m-d') }}">
                    @error('start_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Tanggal Selesai *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required min="{{ now()->format('Y-m-d') }}">
                    @error('end_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Alasan *</label>
                <textarea name="reason" rows="4" required placeholder="Jelaskan alasan pengajuan cuti/izin kamu...">{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Kirim Pengajuan</button>
        </form>
    </div>
</div>
@endsection