@extends('layouts.app')
@section('title', 'Rekap Absensi')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; }
    .filter-bar input { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.88rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); }
    .status-badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .status-hadir { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-telat { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-izin { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-alpha { background:rgba(192,57,43,.15); color:#e07070; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('bos.dashboard') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0 1.5rem;">📋 Rekap Absensi</h1>

    <form method="GET" class="filter-bar">
        <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}" onchange="this.form.submit()">
        @if(request('date'))
        <a href="{{ route('bos.attendances') }}" style="color:var(--muted); font-size:.85rem; align-self:center;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ $att->date->format('d M Y') }}</td>
                    <td>{{ $att->user->name }}</td>
                    <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                    <td style="color:var(--muted);">{{ $att->work_duration ?? '-' }}</td>
                    <td><span class="status-badge status-{{ $att->status }}">{{ ucfirst($att->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada data absensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;">{{ $attendances->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection