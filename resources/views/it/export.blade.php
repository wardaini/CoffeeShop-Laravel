@extends('layouts.app')
@section('title', 'Export Data')

@push('styles')
<style>
    .wrap { max-width:700px; margin:3rem auto; padding:0 5%; }
    .export-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.2rem; }
    .export-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; }
    .export-card .icon { font-size:2.2rem; margin-bottom:.8rem; }
    .export-card h3 { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:.4rem; }
    .export-card p { font-size:.82rem; color:var(--muted); margin-bottom:1.2rem; line-height:1.5; }
    .warning-box { background:rgba(200,151,58,.08); border:1px solid rgba(200,151,58,.2); border-radius:10px; padding:1rem 1.2rem; margin-bottom:1.5rem; font-size:.85rem; color:var(--gold-soft); }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">💾 Export Data</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Download semua data sistem dalam format CSV untuk backup atau analisis</p>

    <div class="warning-box">
        ⚠️ Data yang diexport bersifat <strong>rahasia</strong>. Pastikan file disimpan dengan aman dan tidak disebarluaskan.
    </div>

    <div class="export-grid">
        <div class="export-card">
            <div class="icon">👥</div>
            <h3>Data User</h3>
            <p>Semua akun user: nama, email, role, posisi, status aktif</p>
            <a href="{{ route('it.export.users') }}" class="btn btn-outline btn-sm">📥 Download CSV</a>
        </div>

        <div class="export-card">
            <div class="icon">📦</div>
            <h3>Data Order</h3>
            <p>Semua pesanan: kode, nama, tipe, pembayaran, total, tanggal</p>
            <a href="{{ route('it.export.orders') }}" class="btn btn-outline btn-sm">📥 Download CSV</a>
        </div>

        <div class="export-card">
            <div class="icon">📋</div>
            <h3>Data Absensi</h3>
            <p>Semua riwayat absensi karyawan: jam masuk, keluar, status</p>
            <a href="{{ route('it.export.attendances') }}" class="btn btn-outline btn-sm">📥 Download CSV</a>
        </div>

        <div class="export-card">
            <div class="icon">💰</div>
            <h3>Data Gaji</h3>
            <p>Semua slip gaji karyawan: periode, pokok, bonus, potongan, total</p>
            <a href="{{ route('it.export.salaries') }}" class="btn btn-outline btn-sm">📥 Download CSV</a>
        </div>
    </div>
</div>
@endsection