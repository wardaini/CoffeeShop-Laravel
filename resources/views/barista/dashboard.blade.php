@extends('layouts.app')
@section('title', 'Dashboard Barista')

@push('styles')
<style>
    .wrap { max-width:900px; margin:2rem auto; padding:0 5%; }
    .stats { display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap; }
    .stat-box { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem 1.5rem; flex:1; min-width:140px; text-align:center; }
    .stat-box .num { font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold); }
    .stat-box .lbl { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }
    .item-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.3rem; margin-bottom:.8rem; }
    .item-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
    .item-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; }
    .item-meta { font-size:.82rem; color:var(--muted); }
    .status-pending { color:#E8B860; }
    .status-processing { color:#74b9ff; }
    .status-ready { color:#6fcf97; }
    .notes-box { background:rgba(200,151,58,.06); border-radius:6px; padding:.5rem .8rem; font-size:.82rem; color:var(--muted); margin:.5rem 0; }
    .order-info { font-size:.8rem; color:var(--muted); background:var(--surface); padding:.4rem .8rem; border-radius:6px; display:inline-block; margin-bottom:.5rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">☕ Dashboard Barista</h1>

    <div class="stats">
        <div class="stat-box">
            <div class="num">{{ $items->count() }}</div>
            <div class="lbl">Antrian</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $items->where('kitchen_status', 'pending')->count() }}</div>
            <div class="lbl">Menunggu</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $items->where('kitchen_status', 'processing')->count() }}</div>
            <div class="lbl">Dibuat</div>
        </div>
        <div class="stat-box">
            <div class="num">{{ $doneToday }}</div>
            <div class="lbl">Selesai Hari Ini</div>
        </div>
    </div>

    @forelse($items as $item)
    <div class="item-card">
        <div class="item-header">
            <div>
                <div class="order-info">Order: {{ $item->order->order_code }} · {{ $item->order->order_type_label }}</div>
                <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                <div class="item-meta">× {{ $item->quantity }} · {{ $item->order->customer_name }}</div>
                @if($item->order->notes)
                <div class="notes-box">📝 {{ $item->order->notes }}</div>
                @endif
            </div>
            <div style="text-align:right;">
                <div class="status-{{ $item->kitchen_status }}" style="font-size:1.5rem;">
                    {{ $item->kitchen_status === 'pending' ? '⏳' : ($item->kitchen_status === 'processing' ? '🔥' : '✅') }}
                </div>
                <div style="font-size:.78rem; color:var(--muted); margin-top:.2rem;">{{ $item->kitchen_status_label }}</div>
            </div>
        </div>

        @if($item->kitchen_status !== 'ready')
        <form method="POST" action="{{ route('barista.update-status', $item) }}" style="margin-top:.8rem;">
            @csrf
            <button type="submit" class="btn btn-gold btn-sm" style="width:100%;">
                @if($item->kitchen_status === 'pending')
                    🔥 Mulai Buat
                @else
                    ✅ Tandai Siap
                @endif
            </button>
        </form>
        @else
        <div style="text-align:center; padding:.5rem; color:#6fcf97; font-size:.85rem;">✅ Sudah siap disajikan</div>
        @endif
    </div>
    @empty
    <div style="text-align:center; padding:4rem 0; color:var(--muted);">
        <div style="font-size:3rem; margin-bottom:1rem;">☕</div>
        <p>Tidak ada antrian minuman.</p>
    </div>
    @endforelse
</div>

<script>
    // Auto refresh setiap 20 detik
    setTimeout(() => window.location.reload(), 20000);
</script>

{{-- Menu Karyawan --}}
<div style="max-width:900px; margin:2rem auto; padding:0 5%; border-top:1px solid rgba(200,151,58,.1); padding-top:1.5rem;">
    <div style="font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:1rem;">Menu Karyawan</div>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:.8rem;">
        <a href="{{ route('employee.barcode') }}" class="btn btn-outline btn-sm">🪪 Barcode Absensi</a>
        <a href="{{ route('employee.attendance.history') }}" class="btn btn-outline btn-sm">📋 Riwayat Absensi</a>
        <a href="{{ route('employee.salary') }}" class="btn btn-outline btn-sm">💰 Slip Gaji</a>
        <a href="{{ route('employee.leave.index') }}" class="btn btn-outline btn-sm">🏖️ Cuti / Izin</a>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline btn-sm">🔔 Notifikasi</a>
    </div>
</div>
@endsection