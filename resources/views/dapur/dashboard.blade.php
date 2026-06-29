@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div style="max-width:600px; margin:4rem auto; padding:0 5%; text-align:center;">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">
        Halo, {{ auth()->user()->name }} 👋
    </h1>
    <p style="color:var(--muted); margin-bottom:2rem;">
        {{ auth()->user()->employeeProfile->position ?? 'Karyawan' }}
    </p>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
        <a href="{{ route('employee.barcode') }}" class="btn btn-outline">🪪 Barcode Absensi</a>
        <a href="{{ route('employee.attendance.history') }}" class="btn btn-outline">📋 Riwayat Absensi</a>
        <a href="{{ route('employee.salary') }}" class="btn btn-outline">💰 Slip Gaji</a>
        <a href="{{ route('employee.leave.index') }}" class="btn btn-outline">🏖️ Cuti / Izin</a>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline">🔔 Notifikasi</a>
    </div>
</div>
@endsection