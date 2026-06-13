@extends('layouts.app')
@section('title', 'Dashboard Karyawan')
@section('content')
<div style="max-width:1000px; margin:3rem auto; padding:0 5%;">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">Dashboard Karyawan</h1>
    <p style="color:var(--muted); margin-top:.5rem;">Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->employeeProfile->position ?? '-' }})</p>
    <p style="color:var(--gold); margin-top:1rem;">🚧 Fitur absensi, gaji, dan delivery akan ditambahkan di Fase 3.</p>
</div>
@endsection