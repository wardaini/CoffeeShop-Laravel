@extends('layouts.app')
@section('title', 'Dashboard Dapur')

@push('styles')
<style>
    .wrap { max-width:900px; margin:2rem auto; padding:0 5%; }
    .stats { display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap; }
    .stat-box { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem 1.5rem; flex:1; min-width:140px; text-align:center; }
    .stat-box .num { font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold); }
    .stat-box .lbl { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; }

    .item-card { border-radius:12px; padding:1.3rem; margin-bottom:.8rem; border:2px solid; transition:.2s; }
    .item-card.status-pending { background:rgba(200,151,58,.05); border-color:rgba(200,151,58,.25); }
    .item-card.status-processing { background:rgba(52,152,219,.05); border-color:rgba(52,152,219,.35); animation: pulse 2s infinite; }
    .item-card.status-ready { background:rgba(39,174,96,.05); border-color:rgba(39,174,96,.25); opacity:.7; }

    @keyframes pulse {
        0%, 100% { border-color:rgba(52,152,219,.35); }
        50% { border-color:rgba(52,152,219,.7); }
    }

    .item-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.8rem; }
    .item-name { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.15rem; }
    .item-qty { font-size:1.5rem; font-weight:700; color:var(--gold); }
    .item-meta { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .order-info { font-size:.78rem; color:var(--muted); background:var(--surface); padding:.3rem .7rem; border-radius:6px; display:inline-block; margin-bottom:.5rem; }
    .notes-box { background:rgba(200,151,58,.08); border-radius:6px; padding:.5rem .8rem; font-size:.82rem; color:var(--gold-soft); margin:.5rem 0; }

    .status-indicator { display:flex; align-items:center; gap:.5rem; margin-bottom:1rem; }
    .status-dot { width:10px; height:10px; border-radius:50%; }
    .dot-pending { background:#E8B860; }
    .dot-processing { background:#74b9ff; }
    .dot-ready { background:#6fcf97; }

    .btn-process { background:rgba(52,152,219,.2); border:1px solid rgba(52,152,219,.5); color:#74b9ff; padding:.7rem 1.5rem; border-radius:8px; cursor:pointer; font-size:.95rem; font-family:'DM Sans',sans-serif; width:100%; transition:.2s; }
    .btn-process:hover { background:rgba(52,152,219,.35); }
    .btn-ready { background:rgba(39,174,96,.2); border:1px solid rgba(39,174,96,.5); color:#6fcf97; padding:.7rem 1.5rem; border-radius:8px; cursor:pointer; font-size:.95rem; font-family:'DM Sans',sans-serif; width:100%; transition:.2s; }
    .btn-ready:hover { background:rgba(39,174,96,.35); }

    .divider { border:none; border-top:1px solid rgba(200,151,58,.1); margin:2rem 0; }
    .section-label { font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.8rem;">🍳 Dapur</h1>
        <span id="countdown" style="font-size:.8rem; color:var(--muted); background:var(--card); padding:.4rem .9rem; border-radius:20px;">Auto: 20s</span>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="num">{{ $items->count() }}</div>
            <div class="lbl">Antrian</div>
        </div>
        <div class="stat-box" style="border-color:rgba(200,151,58,.3);">
            <div class="num" style="color:#E8B860;">{{ $items->where('kitchen_status', 'pending')->count() }}</div>
            <div class="lbl">⏳ Menunggu</div>
        </div>
        <div class="stat-box" style="border-color:rgba(52,152,219,.3);">
            <div class="num" style="color:#74b9ff;">{{ $items->where('kitchen_status', 'processing')->count() }}</div>
            <div class="lbl">🔥 Dimasak</div>
        </div>
        <div class="stat-box" style="border-color:rgba(39,174,96,.3);">
            <div class="num" style="color:#6fcf97;">{{ $doneToday }}</div>
            <div class="lbl">✅ Selesai Hari Ini</div>
        </div>
    </div>

    {{-- Antrian Aktif --}}
    @php
        $activeItems = $items->whereIn('kitchen_status', ['pending', 'processing'])->sortBy(fn($i) => $i->kitchen_status === 'pending' ? 0 : 1);
        $readyItems  = $items->where('kitchen_status', 'ready');
    @endphp

    @if($activeItems->isNotEmpty())
    <div class="section-label">🔥 Antrian Masak ({{ $activeItems->count() }})</div>

    @foreach($activeItems as $item)
    <div class="item-card status-{{ $item->kitchen_status }}">
        <div class="order-info">
            📦 {{ $item->order->order_code }} ·
            {{ $item->order->order_type_label }} ·
            {{ $item->order->created_at->format('H:i') }}
        </div>

        <div class="item-header">
            <div style="flex:1;">
                <div class="item-name">{{ $item->product->name ?? '-' }}</div>
                <div class="item-meta">Untuk: {{ $item->order->customer_name }}</div>
                @if($item->order->notes)
                <div class="notes-box">📝 {{ $item->order->notes }}</div>
                @endif
            </div>
            <div class="item-qty">×{{ $item->quantity }}</div>
        </div>

        <div class="status-indicator">
            <span class="status-dot dot-{{ $item->kitchen_status }}"></span>
            <span style="font-size:.85rem; color:{{ $item->kitchen_status_color }};">{{ $item->kitchen_status_label }}</span>
        </div>

        <form method="POST" action="{{ route('dapur.update-status', $item) }}">
            @csrf
            @if($item->kitchen_status === 'pending')
            <button type="submit" class="btn-process">🔥 Mulai Masak Sekarang</button>
            @else
            <button type="submit" class="btn-ready">✅ Masakan Siap Disajikan</button>
            @endif
        </form>
    </div>
    @endforeach
    @else
    <div style="text-align:center; padding:3rem 0; color:var(--muted);">
        <div style="font-size:3rem; margin-bottom:1rem;">🍳</div>
        <p>Tidak ada antrian masakan saat ini.</p>
    </div>
    @endif

    {{-- Selesai Hari Ini --}}
    @if($readyItems->isNotEmpty())
    <hr class="divider">
    <div class="section-label">✅ Sudah Siap ({{ $readyItems->count() }})</div>
    @foreach($readyItems as $item)
    <div class="item-card status-ready">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="item-name" style="font-size:1rem;">{{ $item->product->name ?? '-' }}</div>
                <div class="item-meta">{{ $item->order->order_code }} · ×{{ $item->quantity }}</div>
            </div>
            <span style="color:#6fcf97; font-size:1.5rem;">✅</span>
        </div>
    </div>
    @endforeach
    @endif

    {{-- Menu Karyawan --}}
    <hr class="divider">
    <div class="section-label">Menu Karyawan</div>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:.8rem;">
        <a href="{{ route('employee.barcode') }}" class="btn btn-outline btn-sm">🪪 Barcode Absensi</a>
        <a href="{{ route('employee.attendance.history') }}" class="btn btn-outline btn-sm">📋 Riwayat Absensi</a>
        <a href="{{ route('employee.salary') }}" class="btn btn-outline btn-sm">💰 Slip Gaji</a>
        <a href="{{ route('employee.leave.index') }}" class="btn btn-outline btn-sm">🏖️ Cuti / Izin</a>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline btn-sm">🔔 Notifikasi</a>
    </div>
</div>

<script>
    let s = 20;
    const el = document.getElementById('countdown');
    setInterval(() => {
        s--;
        if (el) el.textContent = `Auto: ${s}s`;
        if (s <= 0) window.location.reload();
    }, 1000);
</script>
@endsection