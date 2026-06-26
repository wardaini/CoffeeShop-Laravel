@extends('layouts.app')
@section('title', 'Antrian Pesanan')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:2rem auto; padding:0 5%; }
    .order-card { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.5rem; margin-bottom:1.2rem; }
    .order-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; flex-wrap:wrap; gap:.5rem; }
    .order-code { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.2rem; }
    .order-meta { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .badge { padding:.25rem .8rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-unpaid { background:rgba(192,57,43,.15); color:#e07070; }
    .badge-paid { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-pending { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-processing { background:rgba(52,152,219,.15); color:#74b9ff; }
    .items-table { width:100%; border-collapse:collapse; margin:1rem 0; }
    .items-table th { padding:.5rem .7rem; text-align:left; font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.12); }
    .items-table td { padding:.6rem .7rem; border-bottom:1px solid rgba(200,151,58,.06); font-size:.85rem; }
    .assign-select { padding:.35rem .6rem; background:var(--surface); border:1px solid rgba(200,151,58,.2); border-radius:6px; color:var(--text); font-size:.82rem; outline:none; }
    .action-group { display:flex; gap:.6rem; flex-wrap:wrap; margin-top:1rem; padding-top:1rem; border-top:1px solid rgba(200,151,58,.08); }
    .courier-card { background:var(--surface); border-radius:8px; padding:.6rem 1rem; font-size:.82rem; display:flex; justify-content:space-between; align-items:center; }
    .status-available { color:#6fcf97; }
    .status-busy { color:#e07070; }
    .notes-box { background:rgba(200,151,58,.06); border-radius:6px; padding:.6rem .9rem; font-size:.82rem; color:var(--muted); margin:.5rem 0; }
</style>
@endpush

@section('content')
<div class="wrap">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-family:'Playfair Display',serif; color:var(--cream);">📋 Antrian Pesanan</h1>
        <div style="display:flex; gap:.8rem; align-items:center;">
            <span id="countdown" style="font-size:.8rem; color:var(--muted);">Refresh: 30s</span>
            <button onclick="window.location.reload()" class="btn btn-outline btn-sm">🔄 Refresh</button>
        </div>
    </div>

    {{-- Status Kurir --}}
    <div style="margin-bottom:1.5rem;">
        <div style="font-size:.78rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.8rem;">Status Kurir</div>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:.6rem;">
            @foreach($couriers as $courier)
            <div class="courier-card">
                <span>{{ $courier->name }}</span>
                <span class="status-{{ $courier->courier_status }}">
                    {{ $courier->courier_status === 'available' ? '● Kosong' : '● Sibuk' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    @forelse($orders as $order)
    <div class="order-card" id="order-{{ $order->id }}">
        <div class="order-header">
            <div>
                <div class="order-code">{{ $order->order_code }}</div>
                <div class="order-meta">
                    {{ $order->customer_name }} · {{ $order->order_type_label }} · {{ $order->created_at->format('H:i') }}
                    @if($order->table_number) · Meja {{ $order->table_number }} @endif
                </div>
                @if($order->notes)
                <div class="notes-box">📝 {{ $order->notes }}</div>
                @endif
            </div>
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <span class="badge badge-{{ $order->payment_status }}">
                    {{ $order->payment_status === 'paid' ? '✅ Lunas' : '❌ Belum Bayar' }}
                </span>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>

        {{-- Items dengan assignment ke barista/dapur --}}
        @if($order->payment_status === 'paid' && $order->status === 'pending')
        <form method="POST" action="{{ route('kasir.send-to-kitchen', $order) }}">
            @csrf
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Kirim ke</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                            <select name="items[{{ $loop->index }}][assigned_to]" class="assign-select" required>
                                <option value="barista" {{ $item->product->category->name === 'Kopi' || $item->product->category->name === 'Non-Kopi' ? 'selected' : '' }}>☕ Barista</option>
                                <option value="dapur" {{ $item->product->category->name === 'Makanan' || $item->product->category->name === 'Snack' ? 'selected' : '' }}>🍳 Dapur</option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-gold btn-sm">🍳 Kirim ke Dapur/Barista</button>
        </form>
        @else
        <table class="items-table">
            <thead>
                <tr><th>Menu</th><th>Qty</th><th>Dikirim ke</th><th>Status Dapur</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="color:var(--muted);">{{ $item->assigned_to ? ucfirst($item->assigned_to) : '-' }}</td>
                    <td>
                        @if($item->assigned_to)
                        <span style="color:{{ $item->kitchen_status_color }}; font-size:.82rem;">{{ $item->kitchen_status_label }}</span>
                        @else
                        <span style="color:var(--muted); font-size:.8rem;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="action-group">
            {{-- Konfirmasi bayar cash/QRIS --}}
            @if($order->payment_status === 'unpaid' && in_array($order->payment_method, ['cash', 'qris']))
            <form method="POST" action="{{ route('kasir.confirm-payment', $order) }}">
                @csrf
                <button type="submit" class="btn btn-gold btn-sm">💵 Konfirmasi Bayar</button>
            </form>
            @endif

            {{-- Assign kurir --}}
            @if($order->take_away_method === 'delivery' && !$order->delivery?->courier_id)
            <div style="display:flex; gap:.5rem; align-items:center;">
                <form method="POST" action="{{ route('kasir.assign-courier', $order) }}" style="display:flex; gap:.5rem;">
                    @csrf
                    <select name="courier_id" class="assign-select" required>
                        <option value="">Pilih Kurir...</option>
                        @foreach($couriers->where('courier_status', 'available') as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline btn-sm">🛵 Assign Kurir</button>
                </form>
            </div>
            @endif

            {{-- Selesaikan order --}}
            @if($order->status === 'processing')
            <form method="POST" action="{{ route('kasir.complete', $order) }}">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">✅ Tandai Selesai</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding:4rem 0; color:var(--muted);">
        <div style="font-size:3rem; margin-bottom:1rem;">📭</div>
        <p>Tidak ada pesanan dalam antrian.</p>
    </div>
    @endforelse
</div>

<script>
    let s = 30;
    const el = document.getElementById('countdown');
    setInterval(() => {
        s--;
        if (el) el.textContent = `Refresh: ${s}s`;
        if (s <= 0) window.location.reload();
    }, 1000);
</script>
@endsection