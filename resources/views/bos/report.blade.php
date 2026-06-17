@extends('layouts.app')
@section('title', 'Laporan Keuangan')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:3rem auto; padding:0 5%; }
    .filter-bar { display:flex; gap:.8rem; margin-bottom:2rem; align-items:center; flex-wrap:wrap; }
    .filter-bar select { padding:.6rem 1rem; background:var(--card); border:1px solid rgba(200,151,58,.2); border-radius:7px; color:var(--text); font-size:.9rem; outline:none; }
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.2rem; margin-bottom:2rem; }
    .stat-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; }
    .stat-card .num { font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold); }
    .stat-card .label { font-size:.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-top:.2rem; }
    .chart-wrap { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; margin-bottom:2rem; }
    .chart-bars { display:flex; align-items:flex-end; gap:.5rem; height:150px; }
    .bar-wrap { flex:1; display:flex; flex-direction:column; align-items:center; gap:.3rem; }
    .bar { background:var(--gold); border-radius:4px 4px 0 0; width:100%; min-height:4px; transition:.3s; }
    .bar-label { font-size:.65rem; color:var(--muted); }
    table { width:100%; border-collapse:collapse; }
    th { padding:.7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.15); }
    td { padding:.8rem .7rem; border-bottom:1px solid rgba(200,151,58,.07); font-size:.85rem; color:var(--text); }
    .download-bar { display:flex; gap:.8rem; margin-bottom:1.5rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">Laporan Keuangan</h1>

    <form method="GET" class="filter-bar">
        <select name="month" onchange="this.form.submit()">
            @php $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
            @foreach($bulan as $num => $nama)
            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>
        <select name="year" onchange="this.form.submit()">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <span style="color:var(--muted); font-size:.85rem; align-self:center;">{{ $bulan[$month] }} {{ $year }}</span>
    </form>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="num">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
            <div class="label">Pendapatan Bulan Ini</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $monthlyCount }}</div>
            <div class="label">Total Transaksi</div>
        </div>
        <div class="stat-card">
            <div class="num">Rp {{ number_format($yearlyRevenue, 0, ',', '.') }}</div>
            <div class="label">Pendapatan {{ $year }}</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $yearlyCount }}</div>
            <div class="label">Total Transaksi {{ $year }}</div>
        </div>
    </div>

    {{-- Chart Batang Sederhana --}}
    <div class="chart-wrap">
        <div style="font-size:.85rem; color:var(--muted); margin-bottom:1rem; text-transform:uppercase; letter-spacing:.1em;">Pendapatan per Bulan {{ $year }}</div>
        @php $maxRev = max(array_column($yearlyData, 'revenue') ?: [1]); @endphp
        <div class="chart-bars">
            @foreach($yearlyData as $m => $data)
            <div class="bar-wrap">
                <div class="bar" style="height: {{ $maxRev > 0 ? round(($data['revenue'] / $maxRev) * 140) : 4 }}px;"
                     title="Rp {{ number_format($data['revenue'], 0, ',', '.') }}"></div>
                <span class="bar-label">{{ substr($bulan[$m], 0, 3) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Download --}}
    <div class="download-bar">
        <a href="{{ route('bos.report.pdf', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline btn-sm">📄 Download PDF</a>
        <a href="{{ route('bos.report.excel', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline btn-sm">📊 Download Excel (CSV)</a>
    </div>

    {{-- Top Produk --}}
    @if($topProducts->isNotEmpty())
    <div style="margin-bottom:2rem;">
        <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">Top 5 Produk Bulan Ini</div>
        <table>
            <thead><tr><th>Produk</th><th>Qty Terjual</th><th>Pendapatan</th></tr></thead>
            <tbody>
                @foreach($topProducts as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->total_qty }}</td>
                    <td>Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Detail Order --}}
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">Detail Transaksi</div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Pembayaran</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyOrders as $order)
                <tr>
                    <td style="color:var(--gold); font-size:.8rem;">{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td style="font-size:.8rem;">{{ $order->order_type_label }}</td>
                    <td style="font-size:.8rem;">{{ $order->payment_method_label }}</td>
                    <td>{{ $order->formatted_grand_total }}</td>
                    <td style="font-size:.8rem;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; color:var(--muted); padding:2rem;">Belum ada transaksi bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection