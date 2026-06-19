@extends('layouts.app')
@section('title', 'Edit Absensi')

@push('styles')
<style>
    .wrap { max-width:560px; margin:3rem auto; padding:0 5%; }
    .form-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:14px; padding:2rem; }
    .form-group { margin-bottom:1.2rem; }
    .form-group label { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; padding:.75rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-size:.95rem; outline:none;
    }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('it.attendance.index') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">Edit Absensi — {{ $attendance->user->name }}</h1>

    <div class="form-card">
        <form method="POST" action="{{ route('it.attendance.update', $attendance) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label>Tanggal</label>
                <input type="text" value="{{ $attendance->date->format('d M Y') }}" disabled style="opacity:.6;">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Masuk</label>
                    <input type="time" name="clock_in" value="{{ $attendance->clock_in?->format('H:i') }}">
                </div>
                <div class="form-group">
                    <label>Jam Keluar</label>
                    <input type="time" name="clock_out" value="{{ $attendance->clock_out?->format('H:i') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="hadir" {{ $attendance->status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="telat" {{ $attendance->status === 'telat' ? 'selected' : '' }}>Telat</option>
                    <option value="izin" {{ $attendance->status === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ $attendance->status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" rows="2">{{ $attendance->notes }}</textarea>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Update Absensi</button>
        </form>
    </div>
</div>
@endsection