@extends('layouts.app')
@section('title', 'Dashboard Kasir')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:2rem auto; padding:0 5%; }
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem; margin-bottom:2rem; }
    .stat-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1.2rem; text-align:center; }
    .stat-card .num { font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold); }
    .stat-card .lbl { font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em; margin-top:.2rem; }
    .order-card { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.3rem; margin-bottom:1rem; }
    .order-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.8rem; flex-wrap:wrap; gap:.5rem; }
    .order-code { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.1rem; }
    .order-meta { font-size:.82rem; color:var(--muted); }
    .items-list { margin:.8rem 0; }
    .item-row { display:flex; justify-content:space-between; font-size:.85rem; padding:.3rem 0; border-bottom:1px solid rgba(200,151,58,.06); }
    .badge { padding:.2rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-unpaid { background:rgba(192,57,43,.15); color:#e07070; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-processing { background:rgba(52,152,219,.15); color:#74b9ff; }
    .action-group { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.8rem; }
    .courier-status { display:flex; gap:.5rem; flex-wrap:wrap; }
    .courier-item { padding:.3rem .8rem; border-radius:20px; font-size:.78rem; }
    .courier-available { background:rgba(39,174,96,.15); color:#6fcf97; }
    .courier-busy { background:rgba(192,57,43,.15); color:#e07070; }
    .refresh-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .section-title { font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div class="refresh-bar">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.6rem;">Dashboard Kasir</h1>
        <div style="display:flex; gap:.8rem; align-items:center;">
            <span style="font-size:.8rem; color:var(--muted);" id="lastRefresh">Auto-refresh: 30 detik</span>
            <a href="{{ route('kasir.queue') }}" class="btn btn-gold btn-sm">📋 Antrian Lengkap</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="num">{{ $pendingOrders }}</div>
            <div class="lbl">Pesanan Masuk</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $processingOrders }}</div>
            <div class="lbl">Sedang Diproses</div>
        </div>
        <div class="stat-card">
            <div class="num" style="{{ $unpaidOrders > 0 ? 'color:#e07070' : '' }}">{{ $unpaidOrders }}</div>
            <div class="lbl">Belum Bayar</div>
        </div>
        <div class="stat-card">
            <div class="num" style="font-size:1.2rem;">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="lbl">Pendapatan Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $todayCount }}</div>
            <div class="lbl">Total Transaksi</div>
        </div>
        <div class="stat-card">
            <div class="num" style="{{ $availableCouriers === 0 ? 'color:#e07070' : 'color:#6fcf97' }}">{{ $availableCouriers }}</div>
            <div class="lbl">Kurir Tersedia</div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="section-title">📦 Pesanan Terbaru</div>

    @forelse($recentOrders as $order)
    <div class="order-card">
        <div class="order-header">
            <div>
                <span class="order-code">{{ $order->order_code }}</span>
                <div class="order-meta">
                    {{ $order->customer_name }} · {{ $order->order_type_label }} · {{ $order->created_at->format('H:i') }}
                </div>
            </div>
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <span class="badge badge-{{ $order->payment_status }}">
                    {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Bayar' }}
                </span>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <div class="items-list">
            @foreach($order->items as $item)
            <div class="item-row">
                <span>{{ $item->product->name ?? '-' }} ×{{ $item->quantity }}</span>
                <span style="color:var(--muted);">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div style="display:flex; justify-content:space-between; padding:.5rem 0; font-weight:700; color:var(--gold-soft);">
            <span>Total</span>
            <span>{{ $order->formatted_grand_total }}</span>
        </div>

        <div class="action-group">
            @if($order->payment_status === 'unpaid' && in_array($order->payment_method, ['cash', 'qris']))
            <form method="POST" action="{{ route('kasir.confirm-payment', $order) }}">
                @csrf
                <button type="submit" class="btn btn-gold btn-sm">💵 Konfirmasi Bayar</button>
            </form>
            @endif

            @if($order->payment_status === 'paid' && $order->status === 'pending')
            <a href="{{ route('kasir.queue') }}#order-{{ $order->id }}" class="btn btn-outline btn-sm">🍳 Kirim ke Dapur</a>
            @endif

            @if($order->status === 'processing')
            <form method="POST" action="{{ route('kasir.complete', $order) }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">✅ Selesai</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding:3rem 0; color:var(--muted);">Belum ada pesanan hari ini.</div>
    @endforelse
</div>

<script>
    // Auto refresh setiap 30 detik
    let countdown = 30;
    const refreshEl = document.getElementById('lastRefresh');

    setInterval(() => {
        countdown--;
        if (refreshEl) refreshEl.textContent = `Auto-refresh: ${countdown} detik`;
        if (countdown <= 0) window.location.reload();
    }, 1000);
</script>
@endsection