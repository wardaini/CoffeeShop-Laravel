@extends('layouts.app')
@section('title', 'Manajemen Absensi')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center; }
    .filter-bar input, .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.85rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); }
    .status-badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .status-hadir { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-telat { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-izin { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-alpha { background:rgba(192,57,43,.15); color:#e07070; }
    .action-group { display:flex; gap:.4rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">Manajemen Absensi</h1>
        <a href="{{ route('it.attendance.create') }}" class="btn btn-gold btn-sm">+ Input Manual</a>
    </div>

    <form method="GET" class="filter-bar">
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
        <select name="user_id" onchange="this.form.submit()">
            <option value="">Semua Karyawan</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
        @if(request('date') || request('user_id'))
        <a href="{{ route('it.attendance.index') }}" style="color:var(--muted); font-size:.85rem;">Reset Filter</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ $att->date->format('d M Y') }}</td>
                    <td>{{ $att->user->name }}</td>
                    <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                    <td><span class="status-badge status-{{ $att->status }}">{{ ucfirst($att->status) }}</span></td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('it.attendance.edit', $att) }}" class="btn btn-outline btn-sm">✏️</a>
                            <form method="POST" action="{{ route('it.attendance.destroy', $att) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">🗑</button>
                            </form>
                        </div>
                    </td>
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