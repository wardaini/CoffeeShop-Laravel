@extends('layouts.app')
@section('title', 'Manajemen Gaji')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center; }
    .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.88rem; outline:none; }
    .action-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.9rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); vertical-align:middle; }
    .badge-draft { background:rgba(200,151,58,.15); color:var(--gold-soft); padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; padding:.2rem .7rem; border-radius:20px; font-size:.75rem; }
    .info-box { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem; margin-bottom:1.5rem; font-size:.88rem; color:var(--muted); }
    .inline-form { display:flex; gap:.4rem; align-items:center; }
    .inline-form input { width:110px; padding:.35rem .5rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.8rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:.5rem;">Manajemen Gaji</h1>
    <p style="color:var(--muted); margin-bottom:1.5rem;">Generate dan kelola gaji karyawan sebelum dikirim ke Bos untuk disetujui</p>

    {{-- Filter Periode --}}
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
        <span style="color:var(--muted); font-size:.85rem; align-self:center;">{{ $bulan[$month] }} {{ $year }}</span>
    </form>

    {{-- Action Bar --}}
    <div class="action-bar">
        <form method="POST" action="{{ route('admin.salary.generate') }}">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-gold btn-sm" onclick="return confirm('Generate gaji otomatis untuk semua karyawan?')">
                ⚡ Generate Gaji Otomatis
            </button>
        </form>
        <form method="POST" action="{{ route('admin.salary.submit') }}">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Kirim semua gaji ke Bos untuk disetujui?')">
                📤 Kirim ke Bos
            </button>
        </form>
    </div>

    <div class="info-box">
        💡 <strong>Cara kerja:</strong> Klik <strong>Generate Gaji Otomatis</strong> untuk menghitung gaji berdasarkan data absensi. Setelah itu kamu bisa tambah/kurangi bonus atau potongan manual. Terakhir klik <strong>Kirim ke Bos</strong> untuk minta persetujuan pembayaran.
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
                @forelse($employees as $emp)
                @php $salary = $emp->salaries->first(); @endphp
                <tr>
                    <td>{{ $emp->name }}</td>
                    <td style="color:var(--muted);">{{ $emp->employeeProfile->position ?? '-' }}</td>
                    <td>{{ $salary?->total_present ?? '-' }} hari</td>
                    <td>Rp {{ number_format($emp->employeeProfile->base_salary ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($salary)
                        Rp {{ number_format($salary->bonus, 0, ',', '.') }}
                        @else - @endif
                    </td>
                    <td>
                        @if($salary)
                        Rp {{ number_format($salary->deduction, 0, ',', '.') }}
                        @else - @endif
                    </td>
                    <td style="font-weight:600; color:var(--gold-soft);">
                        @if($salary)
                        {{ $salary->formatted_total }}
                        @else - @endif
                    </td>
                    <td>
                        @if($salary)
                        <span class="badge-{{ $salary->status }}">{{ $salary->status === 'paid' ? '✅ Lunas' : '⏳ Draft' }}</span>
                        @else
                        <span style="color:var(--muted); font-size:.78rem;">Belum digenerate</span>
                        @endif
                    </td>
                    <td>
                        @if($salary && $salary->status === 'draft')
                        <button onclick="toggleBonus({{ $salary->id }})" class="btn btn-outline btn-sm">✏️ Bonus/Potongan</button>
                        <div id="bonus-form-{{ $salary->id }}" style="display:none; margin-top:.5rem;">
                            <form method="POST" action="{{ route('admin.salary.bonus', $salary) }}">
                                @csrf
                                <div class="inline-form" style="flex-wrap:wrap; gap:.4rem;">
                                    <input type="number" name="bonus" placeholder="Bonus" value="{{ $salary->bonus }}" min="0" step="5000">
                                    <input type="number" name="deduction" placeholder="Potongan" value="{{ $salary->deduction }}" min="0" step="5000">
                                    <input type="text" name="notes" placeholder="Catatan" value="{{ $salary->notes }}" style="width:120px;">
                                    <button type="submit" class="btn btn-gold btn-sm">Simpan</button>
                                </div>
                            </form>
                        </div>
                        @elseif(!$salary)
                        <span style="color:var(--muted); font-size:.78rem;">—</span>
                        @else
                        <span style="color:#6fcf97; font-size:.8rem;">Sudah Lunas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada karyawan terverifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleBonus(id) {
    const el = document.getElementById('bonus-form-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection