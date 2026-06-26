@extends('layouts.app')
@section('title', 'Dashboard Kurir')

@push('styles')
<style>
    .wrap { max-width:700px; margin:2rem auto; padding:0 5%; }
    .status-toggle { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.3rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; }
    .status-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:.4rem; }
    .dot-available { background:#6fcf97; }
    .dot-busy { background:#e07070; }
    .delivery-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.5rem; margin-bottom:1rem; }
    .delivery-code { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.2rem; }
    .delivery-info { font-size:.85rem; color:var(--muted); margin:.3rem 0; }
    .delivery-address { background:rgba(200,151,58,.06); border-radius:8px; padding:.8rem 1rem; font-size:.88rem; color:var(--text); margin:1rem 0; }
    .status-badge { padding:.3rem .9rem; border-radius:20px; font-size:.8rem; font-weight:600; }
    .status-assigned { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-on_the_way { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-delivered { background:rgba(39,174,96,.15); color:#6fcf97; }
    .history-card { background:var(--card); border:1px solid rgba(200,151,58,.07); border-radius:10px; padding:1rem 1.2rem; margin-bottom:.6rem; display:flex; justify-content:space-between; align-items:center; }
</style>
@endpush

@section('content')
<div class="wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">🛵 Dashboard Kurir</h1>

    {{-- Toggle Status Kurir --}}
    <div class="status-toggle">
        <div>
            <span class="status-dot {{ $courierStatus === 'available' ? 'dot-available' : 'dot-busy' }}"></span>
            <strong style="color:var(--cream);">Status Kamu:</strong>
            <span style="color: {{ $courierStatus === 'available' ? '#6fcf97' : '#e07070' }};">
                {{ $courierStatus === 'available' ? 'Kosong (Siap Antar)' : 'Sedang Sibuk' }}
            </span>
        </div>
        @if(!$activeDelivery)
        <form method="POST" action="{{ route('kurir.update-courier-status') }}">
            @csrf
            <input type="hidden" name="status" value="{{ $courierStatus === 'available' ? 'busy' : 'available' }}">
            <button type="submit" class="btn btn-outline btn-sm">
                {{ $courierStatus === 'available' ? '🚫 Set Sibuk' : '✅ Set Kosong' }}
            </button>
        </form>
        @endif
    </div>

    {{-- Stat Hari Ini --}}
    <div style="background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:2rem;">
        <div style="text-align:center;">
            <div style="font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--gold);">{{ $todayCount }}</div>
            <div style="font-size:.75rem; color:var(--muted); text-transform:uppercase; letter-spacing:.1em;">Delivery Hari Ini</div>
        </div>
    </div>

    {{-- Delivery Aktif --}}
    @if($activeDelivery)
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">📦 Delivery Aktif</div>
    <div class="delivery-card" style="border-color:rgba(200,151,58,.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.8rem;">
            <span class="delivery-code">{{ $activeDelivery->order->order_code }}</span>
            <span class="status-badge status-{{ $activeDelivery->status }}">
                {{ $activeDelivery->status === 'assigned' ? '⏳ Dijemput' : '🛵 Sedang Diantar' }}
            </span>
        </div>
        <div class="delivery-info"><strong style="color:var(--text);">Penerima:</strong> {{ $activeDelivery->order->customer_name }}</div>
        <div class="delivery-info"><strong style="color:var(--text);">No. HP:</strong> {{ $activeDelivery->order->customer_phone ?? '-' }}</div>
        <div class="delivery-address">
            📍 {{ $activeDelivery->order->delivery_address }}
        </div>
        <div class="delivery-info"><strong style="color:var(--text);">Total:</strong> {{ $activeDelivery->order->formatted_grand_total }} ({{ $activeDelivery->order->payment_method_label }})</div>

        {{-- Items --}}
        <div style="margin:.8rem 0; font-size:.82rem;">
            @foreach($activeDelivery->order->items as $item)
            <div style="color:var(--muted); padding:.2rem 0;">{{ $item->product->name ?? '-' }} ×{{ $item->quantity }}</div>
            @endforeach
        </div>

        <div style="display:flex; gap:.6rem; margin-top:1rem;">
            @if($activeDelivery->status === 'assigned')
            <form method="POST" action="{{ route('kurir.update-status', $activeDelivery) }}" style="flex:1;">
                @csrf
                <input type="hidden" name="status" value="on_the_way">
                <button type="submit" class="btn btn-gold" style="width:100%;">🛵 Mulai Antar</button>
            </form>
            @elseif($activeDelivery->status === 'on_the_way')
            <form method="POST" action="{{ route('kurir.update-status', $activeDelivery) }}" style="flex:1;">
                @csrf
                <input type="hidden" name="status" value="delivered">
                <button type="submit" class="btn btn-gold" style="width:100%;">✅ Selesai Diantar</button>
            </form>
            <form method="POST" action="{{ route('kurir.update-status', $activeDelivery) }}">
                @csrf
                <input type="hidden" name="status" value="failed">
                <button type="submit" class="btn btn-danger btn-sm">❌ Gagal</button>
            </form>
            @endif
        </div>
    </div>
    @else
    <div style="text-align:center; padding:2rem 0; color:var(--muted); margin-bottom:1.5rem;">
        <div style="font-size:2.5rem; margin-bottom:.5rem;">🛵</div>
        <p>Tidak ada delivery aktif saat ini.</p>
    </div>
    @endif

    {{-- Riwayat Hari Ini --}}
    @if($history->isNotEmpty())
    <div style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.1rem; margin-bottom:1rem;">📋 Riwayat Hari Ini</div>
    @foreach($history as $del)
    <div class="history-card">
        <div>
            <div style="color:var(--gold); font-size:.9rem;">{{ $del->order->order_code }}</div>
            <div style="font-size:.8rem; color:var(--muted);">{{ $del->order->customer_name }} · {{ $del->delivered_at?->format('H:i') }}</div>
        </div>
        <span style="color: {{ $del->status === 'delivered' ? '#6fcf97' : '#e07070' }}; font-size:.82rem;">
            {{ $del->status === 'delivered' ? '✅ Terkirim' : '❌ Gagal' }}
        </span>
    </div>
    @endforeach
    @endif
</div>

<script>
    // Auto refresh setiap 15 detik
    setTimeout(() => window.location.reload(), 15000);
</script>
@endsection