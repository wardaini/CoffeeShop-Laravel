@extends('layouts.app')
@section('title', 'Delivery Order')

@push('styles')
<style>
    .del-wrap { max-width: 900px; margin: 3rem auto; padding: 0 5%; }
    .del-card { background: var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; margin-bottom:1rem; }
    .del-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .del-code { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--gold); }
    .del-status { font-size:.78rem; padding:.3rem .9rem; border-radius:20px; }
    .status-assigned { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .status-on_the_way { background:rgba(52,152,219,.15); color:#74b9ff; }
    .status-delivered { background:rgba(39,174,96,.15); color:#6fcf97; }
    .status-failed { background:rgba(192,57,43,.15); color:#e07070; }
    .del-info { font-size:.88rem; color:var(--muted); margin-bottom:.3rem; }
    .del-info strong { color:var(--text); }
    .del-actions { margin-top:1rem; display:flex; gap:.6rem; flex-wrap:wrap; }
    .empty-state { text-align:center; padding:4rem 0; color:var(--muted); }
    .empty-state div { font-size:3rem; margin-bottom:1rem; }
    .section-title { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--cream); margin: 2rem 0 1rem; }
</style>
@endpush

@section('content')
<div class="del-wrap">
    <h1 style="font-family:'Playfair Display',serif; color:var(--cream); margin-bottom:1.5rem;">🛵 Delivery Order</h1>

    @forelse($deliveries as $delivery)
    <div class="del-card">
        <div class="del-header">
            <span class="del-code">{{ $delivery->order->order_code }}</span>
            <span class="del-status status-{{ $delivery->status }}">{{ $delivery->status_label }}</span>
        </div>
        <div class="del-info"><strong>Penerima:</strong> {{ $delivery->order->customer_name }}</div>
        <div class="del-info"><strong>No. HP:</strong> {{ $delivery->order->customer_phone ?? '-' }}</div>
        <div class="del-info"><strong>Alamat:</strong> {{ $delivery->order->delivery_address }}</div>
        <div class="del-info"><strong>Total:</strong> {{ $delivery->order->formatted_grand_total }} ({{ $delivery->order->payment_method_label }})</div>

        <div class="del-actions">
            @if($delivery->status === 'assigned')
            <form method="POST" action="{{ route('employee.delivery.update-status', $delivery) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="on_the_way">
                <button type="submit" class="btn btn-gold btn-sm">🛵 Mulai Antar</button>
            </form>
            @elseif($delivery->status === 'on_the_way')
            <form method="POST" action="{{ route('employee.delivery.update-status', $delivery) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="delivered">
                <button type="submit" class="btn btn-gold btn-sm">✅ Selesai Diantar</button>
            </form>
            <form method="POST" action="{{ route('employee.delivery.update-status', $delivery) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="failed">
                <button type="submit" class="btn btn-outline btn-sm">❌ Gagal Antar</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div>🛵</div>
        <p>Belum ada delivery yang ditugaskan ke kamu.</p>
    </div>
    @endforelse

    @if($history->isNotEmpty())
    <div class="section-title">Riwayat Delivery</div>
    @foreach($history as $delivery)
    <div class="del-card">
        <div class="del-header">
            <span class="del-code">{{ $delivery->order->order_code }}</span>
            <span class="del-status status-{{ $delivery->status }}">{{ $delivery->status_label }}</span>
        </div>
        <div class="del-info"><strong>Penerima:</strong> {{ $delivery->order->customer_name }}</div>
    </div>
    @endforeach
    @endif
</div>
@endsection