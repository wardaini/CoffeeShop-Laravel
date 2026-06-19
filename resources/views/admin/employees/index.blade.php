@extends('layouts.app')
@section('title', 'Data Karyawan')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .emp-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem; margin-bottom:.8rem; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
    .emp-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.05rem; }
    .emp-info { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .empty { text-align:center; padding:3rem 0; color:var(--muted); }
    .info-note { background:rgba(52,152,219,.1); border:1px solid rgba(52,152,219,.3); color:#74b9ff; padding:1rem; border-radius:8px; font-size:.85rem; margin-bottom:1.5rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">Data Karyawan</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Kelola gaji pokok karyawan yang sudah terverifikasi</p>

    <div class="info-note">
        ℹ️ Verifikasi karyawan baru ditangani oleh tim <strong>IT</strong>. Halaman ini hanya menampilkan karyawan yang sudah aktif.
    </div>

    @forelse($verified as $profile)
    <div class="emp-card">
        <div>
            <div class="emp-name">{{ $profile->user->name }}</div>
            <div class="emp-info">{{ $profile->position }} · {{ $profile->employee_code }}</div>
            <div class="emp-info">Gaji Pokok: Rp {{ number_format($profile->base_salary, 0, ',', '.') }}</div>
        </div>
        <div>
            <form method="POST" action="{{ route('admin.employees.salary', $profile) }}" style="display:flex; gap:.5rem; align-items:center;">
                @csrf
                <input type="number" name="base_salary" value="{{ $profile->base_salary }}" min="0" step="50000"
                       style="width:140px; padding:.4rem .6rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.85rem;">
                <button type="submit" class="btn btn-outline btn-sm">💾 Update Gaji</button>
            </form>
        </div>
    </div>
    @empty
    <div class="empty">Belum ada karyawan terverifikasi. Hubungi tim IT untuk verifikasi.</div>
    @endforelse
</div>
@endsection