@extends('layouts.app')
@section('title', 'Data Karyawan')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.88rem; color:var(--text); }
    .pos-badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; background:rgba(200,151,58,.12); color:var(--gold-soft); }
</style>
@endpush

@section('content')
<div class="wrap">
    <a href="{{ route('bos.dashboard') }}" style="color:var(--muted); font-size:.85rem;">← Kembali</a>
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin:1rem 0 1.5rem;">👥 Data Karyawan</h1>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Posisi</th>
                    <th>Kode</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Bergabung</th>
                    <th>Gaji Pokok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td><span class="pos-badge">{{ $emp->employeeProfile->position ?? '-' }}</span></td>
                    <td style="color:var(--muted); font-size:.8rem;">{{ $emp->employeeProfile->employee_code ?? '-' }}</td>
                    <td style="color:var(--muted);">{{ $emp->email }}</td>
                    <td style="color:var(--muted);">{{ $emp->phone ?? '-' }}</td>
                    <td style="color:var(--muted);">{{ $emp->employeeProfile->joined_at?->format('d M Y') ?? '-' }}</td>
                    <td style="color:var(--gold-soft);">Rp {{ number_format($emp->employeeProfile->base_salary ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.5rem;">{{ $employees->links('pagination::simple-bootstrap-4') }}</div>
</div>
@endsection