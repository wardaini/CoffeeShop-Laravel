@extends('layouts.app')
@section('title', 'Jadwal Kebersihan')

@push('styles')
<style>
    .wrap { max-width:900px; margin:3rem auto; padding:0 5%; }
    .form-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.5rem; margin-bottom:2rem; }
    .form-row { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; }
    .form-group { margin-bottom:0; }
    .form-group label { display:block; font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.4rem; }
    .form-group input, .form-group select { width:100%; padding:.65rem .9rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.88rem; outline:none; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .badge-done { background:rgba(39,174,96,.15); color:#6fcf97; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">🧹 Jadwal Kebersihan</h1>

    {{-- Form Tambah Jadwal --}}
    <div class="form-card">
        <div style="font-size:.85rem; color:var(--muted); margin-bottom:1rem; font-weight:600; text-transform:uppercase; letter-spacing:.1em;">+ Tambah Jadwal</div>
        <form method="POST" action="{{ route('admin.cleaning.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Area *</label>
                    <input type="text" name="area" placeholder="Meja Area, Toilet, dll" required>
                </div>
                <div class="form-group">
                    <label>Petugas *</label>
                    <select name="assigned_to" required>
                        <option value="">-- Pilih --</option>
                        @foreach($cleaners as $cleaner)
                        <option value="{{ $cleaner->id }}">{{ $cleaner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Frekuensi *</label>
                    <select name="frequency">
                        <option value="daily">Harian</option>
                        <option value="per_shift">Per Shift</option>
                        <option value="weekly">Mingguan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal *</label>
                    <input type="date" name="scheduled_date" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-gold btn-sm" style="margin-top:1rem;">Tambah Jadwal</button>
        </form>
    </div>

    {{-- Daftar Jadwal Hari Ini --}}
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">Jadwal Hari Ini</div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Area</th>
                    <th>Petugas</th>
                    <th>Frekuensi</th>
                    <th>Status</th>
                    <th>Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->area }}</td>
                    <td>{{ $schedule->assignedTo?->name ?? '-' }}</td>
                    <td style="color:var(--muted);">{{ $schedule->frequency_label }}</td>
                    <td><span class="badge-{{ $schedule->status }}">{{ $schedule->status === 'done' ? '✅ Selesai' : '⏳ Pending' }}</span></td>
                    <td style="color:var(--muted);">{{ $schedule->completed_at?->format('H:i') ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.cleaning.destroy', $schedule) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus jadwal ini?')">🗑</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada jadwal hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection