@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<div style="max-width:1000px; margin:3rem auto; padding:0 5%;">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">Dashboard Admin</h1>
    <p style="color:var(--muted); margin-top:.5rem;">Selamat datang, {{ auth()->user()->name }}</p>
    <p style="color:var(--gold); margin-top:1rem;">🚧 Fitur manajemen stok & verifikasi karyawan akan ditambahkan di Fase 4.</p>
</div>
@endsection