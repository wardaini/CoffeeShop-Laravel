@extends('layouts.app')
@section('title', 'Detail Karyawan')

@push('styles')
<style>
    .wrap { max-width:700px; margin:3rem auto; padding:0 5%; }
    .detail-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:14px; padding:2rem; }
    .detail-row { display:flex; justify-content:space-between; padding:.7rem 0; border-bottom:1px solid rgba(200,151,58,.07); font-size:.9rem; }
    .detail-row .key { color:var(--muted); }
    .detail-row .val { color:var(--cream); text-align:right; }
    .photo-wrap { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin:1.5rem 0; }
    .photo-wrap img { width:100%; border-radius:8px; border:1px solid rgba(200,151,58,.2); }
    .photo-wrap .ph { background:var(--surface); border-radius:8px; height:160px; display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:.85rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('admin.employees.index') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0;">{{ $profile->user->name }}</h1>

    <div class="detail-card">
        <div class="detail-row"><span class="key">Posisi</span><span class="val">{{ $profile->position }}</span></div>
        <div class="detail-row"><span class="key">Email</span><span class="val">{{ $profile->user->email }}</span></div>
        <div class="detail-row"><span class="key">No. HP</span><span class="val">{{ $profile->user->phone }}</span></div>
        <div class="detail-row"><span class="key">No. KTP</span><span class="val">{{ $profile->ktp_number }}</span></div>
        <div class="detail-row"><span class="key">Kode Karyawan</span><span class="val">{{ $profile->employee_code }}</span></div>
        <div class="detail-row"><span class="key">Status</span><span class="val">{{ ucfirst($profile->verification_status) }}</span></div>
        <div class="detail-row"><span class="key">Bergabung</span><span class="val">{{ $profile->joined_at?->format('d M Y') ?? '-' }}</span></div>

        <div class="photo-wrap" style="margin-top:1.5rem;">
            <div>
                <div style="font-size:.78rem; color:var(--muted); margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.1em;">Foto KTP</div>
                @if($profile->ktp_photo_url)
                    <img src="{{ $profile->ktp_photo_url }}" alt="Foto KTP">
                @else
                    <div class="ph">Tidak ada foto</div>
                @endif
            </div>
            <div>
                <div style="font-size:.78rem; color:var(--muted); margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.1em;">Foto Wajah</div>
                @if($profile->face_photo_url)
                    <img src="{{ $profile->face_photo_url }}" alt="Foto Wajah">
                @else
                    <div class="ph">Tidak ada foto</div>
                @endif
            </div>
        </div>

        @if($profile->verification_status === 'pending')
        <div style="display:flex; gap:.8rem; margin-top:1.5rem;">
            <form method="POST" action="{{ route('admin.employees.verify', $profile) }}" style="flex:1;">
                @csrf
                <button type="submit" class="btn btn-gold" style="width:100%;">✅ Verifikasi</button>
            </form>
            <form method="POST" action="{{ route('admin.employees.reject', $profile) }}" style="flex:1;">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:100%;">❌ Tolak</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection