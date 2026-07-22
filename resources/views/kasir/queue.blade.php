@extends('layouts.app')
@section('title', 'Antrian Pesanan')

@push('styles')
<style>
    .wrap { max-width:1100px; margin:2rem auto; padding:0 5%; }
    .order-card { background:var(--card); border-radius:14px; margin-bottom:1.5rem; overflow:hidden; border:1px solid rgba(200,151,58,.15); }
    .order-card.urgent { border-color:rgba(192,57,43,.4); }
    .order-card.ready  { border-color:rgba(39,174,96,.3); }
    .step-bar { padding:.8rem 1.5rem; background:rgba(0,0,0,.2); display:flex; gap:.5rem; align-items:center; font-size:.78rem; border-bottom:1px solid rgba(200,151,58,.06); flex-wrap:wrap; }
    .step { padding:.25rem .7rem; border-radius:20px; }
    .step.done   { background:rgba(39,174,96,.15); color:#6fcf97; }
    .step.active { background:rgba(200,151,58,.2); color:var(--gold); font-weight:700; }
    .step.todo   { background:rgba(255,255,255,.05); color:var(--muted); }
    .step-arrow  { color:var(--muted); }
    .order-top { padding:1.2rem 1.5rem; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:.8rem; border-bottom:1px solid rgba(200,151,58,.08); }
    .order-code { font-family:'Playfair Display',serif; color:var(--gold); font-size:1.2rem; }
    .order-meta { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .badge { padding:.25rem .8rem; border-radius:20px; font-size:.75rem; font-weight:600; }
    .badge-unpaid     { background:rgba(192,57,43,.2); color:#e07070; border:1px solid rgba(192,57,43,.3); }
    .badge-paid       { background:rgba(39,174,96,.15); color:#6fcf97; }
    .badge-pending    { background:rgba(200,151,58,.15); color:var(--gold-soft); }
    .badge-processing { background:rgba(52,152,219,.15); color:#74b9ff; }
    .badge-ready      { background:rgba(155,89,182,.15); color:#c39bd3; }
    .badge-completed  { background:rgba(39,174,96,.2); color:#6fcf97; }
    .order-body { padding:1.2rem 1.5rem; }
    .notes-box { background:rgba(200,151,58,.06); border-radius:6px; padding:.6rem .9rem; font-size:.82rem; color:var(--gold-soft); margin-bottom:1rem; }
    .main-action { padding:1.2rem 1.5rem; background:rgba(0,0,0,.15); border-top:1px solid rgba(200,151,58,.08); }
    .btn-confirm-pay { width:100%; padding:1rem; border-radius:10px; background:linear-gradient(135deg, var(--gold), var(--gold-soft)); color:var(--bg); font-size:1rem; font-weight:700; border:none; cursor:pointer; font-family:'DM Sans',sans-serif; transition:.2s; }
    .btn-confirm-pay:hover { opacity:.9; transform:translateY(-1px); }
    .btn-send-kitchen { width:100%; padding:.85rem; border-radius:10px; background:rgba(52,152,219,.2); border:1px solid rgba(52,152,219,.4); color:#74b9ff; font-size:.95rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:.2s; }
    .btn-send-kitchen:hover { background:rgba(52,152,219,.35); }
    .btn-complete { width:100%; padding:.85rem; border-radius:10px; background:rgba(39,174,96,.2); border:1px solid rgba(39,174,96,.4); color:#6fcf97; font-size:.95rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:.2s; }
    .btn-complete:hover { background:rgba(39,174,96,.35); }
    .items-table { width:100%; border-collapse:collapse; margin-bottom:1rem; }
    .items-table th { padding:.5rem .7rem; text-align:left; font-size:.72rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); border-bottom:1px solid rgba(200,151,58,.1); }
    .items-table td { padding:.65rem .7rem; border-bottom:1px solid rgba(200,151,58,.05); font-size:.85rem; color:var(--text); }
    .assign-select { padding:.4rem .7rem; background:var(--surface); border:1px solid rgba(200,151,58,.25); border-radius:7px; color:var(--text); font-size:.82rem; outline:none; }
    .ks-pending    { background:rgba(200,151,58,.1); color:#E8B860; padding:.2rem .6rem; border-radius:12px; font-size:.78rem; }
    .ks-processing { background:rgba(52,152,219,.1); color:#74b9ff; padding:.2rem .6rem; border-radius:12px; font-size:.78rem; }
    .ks-ready      { background:rgba(39,174,96,.1); color:#6fcf97; padding:.2rem .6rem; border-radius:12px; font-size:.78rem; }
    .courier-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:.5rem; margin-bottom:.8rem; }
    .courier-item { background:var(--surface); border-radius:7px; padding:.5rem .8rem; font-size:.8rem; display:flex; justify-content:space-between; }
    .c-available { color:#6fcf97; }
    .c-busy { color:#e07070; }
    .empty-state { text-align:center; padding:5rem 0; color:var(--muted); }
    .empty-state div { font-size:4rem; margin-bottom:1rem; }
    .timer { font-size:.75rem; background:var(--surface); padding:.3rem .7rem; border-radius:20px; color:var(--muted); }
    .info-transfer { background:rgba(200,151,58,.06); border:1px solid rgba(200,151,58,.2); border-radius:8px; padding:.8rem; font-size:.82rem; color:var(--gold-soft); margin-bottom:.8rem; }
</style>
@endpush

@section('content')
<div class="wrap">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:.8rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; color:var(--cream); font-size:1.6rem;">📋 Antrian Pesanan</h1>
            <p style="color:var(--muted); font-size:.85rem; margin-top:.2rem;">{{ $orders->count() }} pesanan aktif</p>
        </div>
        <div style="display:flex; gap:.8rem; align-items:center;">
            <span id="countdown" class="timer">Refresh: 30s</span>
            <button onclick="window.location.reload()" class="btn btn-outline btn-sm">🔄 Refresh</button>
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-outline btn-sm">← Dashboard</a>
        </div>
    </div>

    {{-- Status Kurir --}}
    @if($couriers->isNotEmpty())
    <div style="margin-bottom:1.5rem; background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:10px; padding:1rem 1.2rem;">
        <div style="font-size:.72rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.7rem;">🛵 Status Kurir</div>
        <div class="courier-grid">
            @foreach($couriers as $courier)
            <div class="courier-item">
                <span>{{ $courier->name }}</span>
                <span class="{{ $courier->courier_status === 'available' ? 'c-available' : 'c-busy' }}">
                    {{ $courier->courier_status === 'available' ? '● Kosong' : '● Sibuk' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Order List --}}
    @forelse($orders as $order)
    @php
        $isPaid       = $order->payment_status === 'paid';
        $isPending    = $order->status === 'pending';
        $isReady      = $order->status === 'ready';
        $isCompleted  = $order->status === 'completed';
        $isDelivery   = $order->take_away_method === 'delivery';
        $hasCourier   = !empty($order->delivery?->courier_id);
        $anySent      = $order->items->contains(fn($i) => $i->assigned_to !== null);
        $allReady     = $anySent && $order->items->every(fn($i) => $i->kitchen_status === 'ready');
        $isCash       = in_array($order->payment_method, ['cash', 'qris']);
    @endphp

    <div class="order-card {{ !$isPaid ? 'urgent' : ($allReady || $isReady ? 'ready' : '') }}">

        {{-- Step Bar --}}
        <div class="step-bar">
            <span class="step {{ $isPaid ? 'done' : 'active' }}">
                {{ $isPaid ? '✅' : '⏳' }} Bayar
            </span>
            <span class="step-arrow">→</span>
            <span class="step {{ $allReady ? 'done' : ($anySent ? 'active' : 'todo') }}">
                {{ $allReady ? '✅' : ($anySent ? '🔥' : '⏳') }} Dapur/Barista
            </span>
            <span class="step-arrow">→</span>
            @if($isDelivery)
            <span class="step {{ $hasCourier ? 'done' : (($allReady || $isReady) && $isPaid ? 'active' : 'todo') }}">
                {{ $hasCourier ? '✅' : '⏳' }} Kurir
            </span>
            <span class="step-arrow">→</span>
            @endif
            <span class="step {{ $isCompleted ? 'done' : 'todo' }}">
                {{ $isCompleted ? '✅' : '⏳' }} Selesai
            </span>
        </div>

        {{-- Header --}}
        <div class="order-top">
            <div>
                <div class="order-code">{{ $order->order_code }}</div>
                <div class="order-meta">
                    <strong style="color:var(--text);">{{ $order->customer_name }}</strong> ·
                    {{ $order->order_type_label }} ·
                    {{ $order->created_at->format('H:i') }}
                    @if($order->table_number) · <strong>Meja {{ $order->table_number }}</strong> @endif
                </div>
                @if($order->notes)
                <div class="notes-box" style="margin-top:.5rem;">📝 {{ $order->notes }}</div>
                @endif
            </div>
            <div style="display:flex; gap:.4rem; flex-wrap:wrap; align-items:flex-start;">
                <span class="badge badge-{{ $order->payment_status }}">
                    {{ $order->payment_status === 'paid' ? '✅ Lunas' : '❌ Belum Bayar' }}
                </span>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                <span class="badge" style="background:rgba(255,255,255,.05); color:var(--muted);">
                    {{ $order->payment_method_label }}
                </span>
            </div>
        </div>

        {{-- Body --}}
        <div class="order-body">

            @if(!$isPaid)
                {{-- Belum Bayar --}}
                @if(!$isCash)
                <div class="info-transfer">
                    📋 Transfer — pastikan sudah cek mutasi rekening sebelum konfirmasi
                </div>
                @endif
                <table class="items-table">
                    <thead><tr><th>Menu</th><th>Qty</th><th>Harga</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            @elseif($isPaid && $isPending && !$anySent)
                {{-- Sudah bayar, belum kirim ke dapur --}}
                <div style="font-size:.85rem; color:var(--muted); margin-bottom:1rem;">
                    ✅ Pembayaran diterima. Pilih tujuan tiap menu lalu kirim ke dapur/barista.
                </div>
                <form method="POST" action="{{ route('kasir.send-to-kitchen', $order) }}">
                    @csrf
                    <table class="items-table">
                        <thead>
                            <tr><th>Menu</th><th>Kategori</th><th>Qty</th><th>Kirim ke</th></tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? '-' }}</td>
                                <td style="color:var(--muted); font-size:.8rem;">{{ $item->product->category->name ?? '' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                    <select name="items[{{ $loop->index }}][assigned_to]" class="assign-select" required>
                                        <option value="barista" {{ in_array($item->product->category->name ?? '', ['Kopi','Non-Kopi']) ? 'selected' : '' }}>☕ Barista</option>
                                        <option value="dapur" {{ in_array($item->product->category->name ?? '', ['Makanan','Snack']) ? 'selected' : '' }}>🍳 Dapur</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn-send-kitchen">🍳 Kirim ke Dapur / Barista</button>
                </form>

            @else
                {{-- Sudah dikirim ke dapur - tampilkan progress --}}
                <table class="items-table">
                    <thead>
                        <tr><th>Menu</th><th>Qty</th><th>Dikirim ke</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                <span style="font-size:.78rem; color:{{ $item->item_order_type === 'dine_in' ? 'var(--gold-soft)' : '#74b9ff' }};">
                                    {{ $item->item_order_type === 'dine_in' ? '🪑 Dine In' : '🥤 Take Away' }}
                                </span>
                            </td>
                            <td style="color:var(--muted);">
                                {{ $item->assigned_to === 'barista' ? '☕ Barista' : '🍳 Dapur' }}
                            </td>
                            <td>
                                <span class="ks-{{ $item->kitchen_status }}">
                                    {{ $item->kitchen_status_label }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(!$allReady && !$isReady)
                {{-- Masih dalam proses --}}
                <div style="text-align:center; color:var(--muted); font-size:.85rem; padding:.5rem 0;">
                    ⏳ Menunggu dapur/barista menyelesaikan pesanan...
                </div>
                @endif

                @if(($allReady || $isReady) && $isDelivery && !$hasCourier)
                {{-- Siap + Delivery + Belum ada kurir --}}
                <div style="background:rgba(39,174,96,.06); border:1px solid rgba(39,174,96,.25); border-radius:10px; padding:1rem; margin-top:.8rem;">
                    <div style="color:#6fcf97; font-size:.88rem; font-weight:600; margin-bottom:.8rem;">
                        ✅ Semua pesanan siap! Assign kurir untuk pengantaran:
                    </div>
                    <form method="POST" action="{{ route('kasir.assign-courier', $order) }}" style="display:flex; gap:.6rem; flex-wrap:wrap;">
                        @csrf
                        <select name="courier_id" class="assign-select" required style="flex:1; min-width:150px;">
                            <option value="">Pilih kurir yang kosong...</option>
                            @foreach($couriers->where('courier_status', 'available') as $courier)
                            <option value="{{ $courier->id }}">🟢 {{ $courier->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-gold btn-sm">🛵 Assign Kurir</button>
                    </form>
                    @if($couriers->where('courier_status', 'available')->isEmpty())
                    <div style="color:#e07070; font-size:.82rem; margin-top:.5rem;">
                        ⚠️ Tidak ada kurir yang kosong saat ini.
                    </div>
                    @endif
                </div>
                @endif

                @if($isDelivery && $hasCourier)
                {{-- Kurir sudah di-assign --}}
                <div style="background:rgba(39,174,96,.06); border:1px solid rgba(39,174,96,.2); border-radius:8px; padding:.8rem 1rem; margin-top:.8rem; font-size:.85rem;">
                    🛵 Kurir: <strong style="color:#6fcf97;">{{ $order->delivery->courier->name }}</strong>
                    · {{ $order->delivery->status_label }}
                </div>
                @endif

                @if(($allReady || $isReady) && !$isDelivery)
                {{-- Siap + Dine in / Pickup --}}
                <div style="text-align:center; color:#6fcf97; font-size:.88rem; padding:.5rem 0; font-weight:600;">
                    ✅ Semua pesanan siap! Silakan sajikan ke pelanggan.
                </div>
                @endif

            @endif

        </div>

        {{-- Main Action --}}
        <div class="main-action">
            @if(!$isPaid)
            {{-- Konfirmasi Bayar --}}
            <form method="POST" action="{{ route('kasir.confirm-payment', $order) }}">
                @csrf
                <button type="submit" class="btn-confirm-pay">
                    @if($order->payment_method === 'cash') 💵
                    @elseif($order->payment_method === 'qris') 📱
                    @else 🏦
                    @endif
                    Konfirmasi Pembayaran {{ $order->payment_method_label }} — {{ $order->formatted_grand_total }}
                </button>
            </form>

            @elseif(($allReady || $isReady) && (!$isDelivery || $hasCourier) && !$isCompleted)
            {{-- Tandai Selesai --}}
            <form method="POST" action="{{ route('kasir.complete', $order) }}">
                @csrf
                <button type="submit" class="btn-complete">✅ Tandai Pesanan Selesai</button>
            </form>

            @elseif($isPaid && $isPending && !$anySent)
            {{-- Info: pilih tujuan dulu --}}
            <div style="text-align:center; color:var(--muted); font-size:.82rem;">
                Pilih tujuan menu di atas lalu kirim ke dapur/barista
            </div>

            @elseif($anySent && !$allReady && !$isReady)
            {{-- Info: tunggu dapur --}}
            <div style="text-align:center; color:var(--muted); font-size:.82rem;">
                ⏳ Menunggu dapur/barista menyelesaikan pesanan...
            </div>

            @elseif(($allReady || $isReady) && $isDelivery && !$hasCourier)
            {{-- Info: assign kurir dulu --}}
            <div style="text-align:center; color:var(--gold-soft); font-size:.82rem;">
                🛵 Assign kurir di atas sebelum menyelesaikan pesanan
            </div>

            @else
            <div style="text-align:center; color:var(--muted); font-size:.82rem;">
                Pesanan sedang diproses
            </div>
            @endif
        </div>

    </div>
    @empty
    <div class="empty-state">
        <div>📭</div>
        <p>Tidak ada pesanan dalam antrian.</p>
        <p style="font-size:.82rem; margin-top:.5rem;">Pesanan baru akan muncul otomatis.</p>
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