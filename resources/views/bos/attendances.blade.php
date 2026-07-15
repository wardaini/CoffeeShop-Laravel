@extends('layouts.app')
@section('title', 'Rekap Absensi')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center; }
    .filter-bar input, .filter-bar select {
        padding:.6rem 1rem; background:var(--card);
        border:1px solid rgba(200,151,58,.2); border-radius:7px;
        color:var(--text); font-size:.85rem; outline:none;
    }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); }
    .status-badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .status-hadir { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-telat { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-izin { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-alpha { background:rgba(192,57,43,.15); color:#e07070; }
    .summary-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .summary-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1rem; text-align:center; }
    .summary-card .num { font-family:'Playfair Display',serif; font-size:1.6rem; color:var(--gold); }
    .summary-card .lbl { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('bos.dashboard') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0 1.5rem;">📋 Rekap Absensi</h1>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="num" style="color:#6fcf97;">{{ $attendances->where('status', 'hadir')->count() }}</div>
            <div class="lbl">Hadir</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:var(--gold-soft);">{{ $attendances->where('status', 'telat')->count() }}</div>
            <div class="lbl">Telat</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:#74b9ff;">{{ $attendances->where('status', 'izin')->count() }}</div>
            <div class="lbl">Izin</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:#e07070;">{{ $attendances->where('status', 'alpha')->count() }}</div>
            <div class="lbl">Alpha</div>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        <select name="user_id" onchange="this.form.submit()">
            <option value="">Semua Karyawan</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['date', 'user_id']))
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