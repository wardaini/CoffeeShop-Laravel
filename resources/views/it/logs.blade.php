@extends('layouts.app')
@section('title', 'Log Aktivitas')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .filter-bar input, .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.85rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.7rem; border-bottom:1px solid rgba(200,151,58,.06); font-size:.82rem; color:var(--text); }
    .module-badge { padding:.15rem .6rem; border-radius:12px; font-size:.72rem; font-weight:600; }
    .module-kasir { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .module-barista { background:rgba(52,152,219,.15); color:#74b9ff; }
    .module-dapur { background:rgba(231,76,60,.15); color:#e07070; }
    .module-kurir { background:rgba(39,174,96,.15); color:#6fcf97; }
    .module-it { background:rgba(155,89,182,.15); color:#c39bd3; }
    .module-admin { background:rgba(200,151,58,.2); color:var(--gold); }
    .module-cleaning { background:rgba(26,188,156,.15); color:#55efc4; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">📊 Log Aktivitas Sistem</h1>

    <form method="GET" class="filter-bar">
        <select name="module" onchange="this.form.submit()">
            <option value="">Semua Modul</option>
            @foreach($modules as $mod)
            <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        @if(request()->hasAny(['module', 'date', 'user_id']))
        <a href="{{ route('it.logs') }}" style="color:var(--muted); font-size:.85rem; align-self:center;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Modul</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="color:var(--muted);">{{ $log->created_at->format('d/m H:i') }}</td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td><span class="module-badge module-{{ $log->module }}">{{ $log->module }}</span></td>
                    <td style="font-size:.78rem; color:var(--muted);">{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td style="color:var(--muted); font-size:.78rem;">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada log aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;">{{ $logs->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection