@extends('layouts.app')
@section('title', 'Riwayat Absensi')

@push('styles')
<style>
    .att-wrap { max-width: 900px; margin: 3rem auto; padding: 0 5%; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.8rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.9rem .8rem; border-bottom:1px solid rgba(200,151,58,.08); font-size:.9rem; color:var(--text); }
    .status-badge { padding:.25rem .8rem; border-radius:20px; font-size:.78rem; font-weight:600; }
    .status-hadir { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-telat { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-izin { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-alpha { background:rgba(192,57,43,.15); color:#e07070; }
</style>
@endpush

@section('content')
<div class="att-wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Riwayat Absensi</h1>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ $att->date->format('d M Y') }}</td>
                    <td>{{ $att->clock_in?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->clock_out?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->work_duration ?? '-' }}</td>
                    <td><span class="status-badge status-{{ $att->status }}">{{ ucfirst($att->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada data absensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1.5rem;">
        {{ $attendances->links('pagination::simple-bootstrap-4') }}
    </div>
</div>
@endsection