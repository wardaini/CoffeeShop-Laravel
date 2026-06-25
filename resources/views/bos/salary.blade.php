@extends('layouts.app')
@section('title', 'Persetujuan Gaji')

@push('styles')
<style>
    .wrap { max-width:1000px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center; }
    .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.88rem; outline:none; }
    .summary-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.9rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); vertical-align:middle; }
    .badge-draft { background:rgba(200,151,58,.15); color:var(--gold-soft); padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">Persetujuan Gaji Karyawan</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Review dan setujui pembayaran gaji yang sudah disiapkan Admin</p>

    <form method="GET" class="filter-bar">
        @php $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
        <select name="month" onchange="this.form.submit()">
            @foreach($bulan as $num => $nama)
            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>
        <select name="year" onchange="this.form.submit()">
            @for($y = now()->year; $y >= now()->year - 2; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>

    <div class="summary-card">
        <div>
            <div style="font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted);">Total Pengeluaran Gaji</div>
            <div style="font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold);">
                Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </div>
            <div style="font-size:.82rem; color:var(--muted);">{{ $bulan[$month] }} {{ $year }} · {{ $salaries->count() }} karyawan</div>
        </div>
        @if($salaries->where('status', 'draft')->count() > 0)
        <form method="POST" action="{{ route('bos.salary.approve-all') }}">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-gold" onclick="return confirm('Setujui semua gaji dan kirim notifikasi ke karyawan?')">
                ✅ Setujui Semua Gaji
            </button>
        </form>
        @endif
    </div>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Posisi</th>
                    <th>Hadir</th>
                    <th>Gaji Pokok</th>
                    <th>Bonus</th>
                    <th>Potongan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salaries as $salary)
                <tr>
                    <td>{{ $salary->user->name }}</td>
                    <td style="color:var(--muted);">{{ $salary->user->employeeProfile->position ?? '-' }}</td>
                    <td>{{ $salary->total_present }} hari</td>
                    <td>{{ $salary->formatted_base }}</td>
                    <td style="color:#6fcf97;">{{ $salary->formatted_bonus }}</td>
                    <td style="color:#e07070;">{{ $salary->formatted_deduction }}</td>
                    <td style="font-weight:700; color:var(--gold-soft);">{{ $salary->formatted_total }}</td>
                    <td><span class="badge-{{ $salary->status }}">{{ $salary->status === 'paid' ? '✅ Lunas' : '⏳ Draft' }}</span></td>
                    <td>
                        <div style="display:flex; gap:.4rem;">
                            @if($salary->status === 'draft')
                            <form method="POST" action="{{ route('bos.salary.approve', $salary) }}">
                                @csrf
                                <button type="submit" class="btn btn-gold btn-sm">✅ Approve</button>
                            </form>
                            @endif
                            <a href="{{ route('bos.salary.slip', $salary) }}" class="btn btn-outline btn-sm">📄 Slip</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada data gaji untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection