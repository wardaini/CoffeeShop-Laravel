@extends('layouts.app')
@section('title', 'Checkout — BrewNest')

@push('styles')
<style>
    .checkout-wrap { max-width:1000px; margin:0 auto; padding:4rem 5%; display:grid; grid-template-columns:1fr 340px; gap:2.5rem; }
    .form-title { font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--cream); margin-bottom:1.2rem; }
    .section-block { background:var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; }
    .form-group { margin-bottom:1.3rem; }
    .form-group label { display:block; font-size:.82rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; padding:.75rem 1rem;
        background:var(--surface); border:1px solid rgba(200,151,58,.2);
        border-radius:8px; color:var(--text); font-family:'DM Sans',sans-serif;
        font-size:.95rem; outline:none; transition:border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#e07070; margin-top:.35rem; }

    /* Item type selector */
    .item-row { display:flex; justify-content:space-between; align-items:center; padding:.9rem 0; border-bottom:1px solid rgba(200,151,58,.07); gap:1rem; flex-wrap:wrap; }
    .item-row:last-child { border-bottom:none; }
    .item-info { flex:1; }
    .item-name { font-size:.95rem; color:var(--cream); }
    .item-price { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
    .item-type-selector { display:flex; gap:.4rem; }
    .type-btn { padding:.35rem .85rem; border-radius:20px; font-size:.78rem; cursor:pointer; border:1px solid rgba(200,151,58,.25); background:transparent; color:var(--muted); font-family:'DM Sans',sans-serif; transition:.2s; }
    .type-btn.active-dinein { background:rgba(200,151,58,.15); border-color:var(--gold); color:var(--gold); font-weight:600; }
    .type-btn.active-takeaway { background:rgba(52,152,219,.15); border-color:#74b9ff; color:#74b9ff; font-weight:600; }

    /* Table number - muncul kalau ada item dine in */
    .table-field { display:none; margin-top:.8rem; }
    .table-field.show { display:block; }

    /* Delivery address - muncul kalau ada item take away dengan delivery */
    .delivery-field { display:none; margin-top:.8rem; }
    .delivery-field.show { display:block; }

    /* Take away method per item */
    .takeaway-method { display:none; margin-top:.4rem; }
    .takeaway-method.show { display:flex; gap:.4rem; flex-wrap:wrap; }
    .method-btn { padding:.3rem .7rem; border-radius:20px; font-size:.75rem; cursor:pointer; border:1px solid rgba(200,151,58,.2); background:transparent; color:var(--muted); font-family:'DM Sans',sans-serif; transition:.2s; }
    .method-btn.active { background:rgba(39,174,96,.15); border-color:#6fcf97; color:#6fcf97; font-weight:600; }

    /* Payment */
    .payment-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.8rem; }
    .radio-card { position:relative; cursor:pointer; border:1px solid rgba(200,151,58,.2); border-radius:8px; padding:1rem; text-align:center; transition:.2s; }
    .radio-card input { position:absolute; opacity:0; }
    .radio-card .icon { font-size:1.6rem; display:block; margin-bottom:.4rem; }
    .radio-card .label { font-size:.85rem; color:var(--text); }
    .radio-card:has(input:checked) { border-color:var(--gold); background:rgba(200,151,58,.08); }

    .order-summary { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.6rem; height:fit-content; position:sticky; top:90px; }
    .sum-title { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--cream); margin-bottom:1.2rem; }
    .sum-item { display:flex; justify-content:space-between; font-size:.88rem; margin-bottom:.7rem; color:var(--muted); }
    .sum-total { display:flex; justify-content:space-between; font-weight:700; font-size:1.15rem; color:var(--gold-soft); border-top:1px solid rgba(200,151,58,.2); padding-top:.9rem; margin-top:.5rem; }

    @media(max-width:768px){ .checkout-wrap{ grid-template-columns:1fr; } .payment-grid{ grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')

<div style="padding:3rem 5% 1rem; max-width:1000px; margin:0 auto;">
    <h1 style="font-size:2rem; color:var(--cream);">Checkout</h1>
</div>

<div class="checkout-wrap">
    <div>
        <form method="POST" action="{{ route('order.store') }}" id="checkoutForm">
            @csrf

            {{-- Data Pemesan --}}
            <div class="section-block">
                <div class="form-title">📋 Data Pemesan</div>
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Masukkan nama kamu" required>
                    @error('customer_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. Telepon <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional)</span></label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Email <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional)</span></label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="email@contoh.com">
                </div>
            </div>

            {{-- Tipe Per Item --}}
            <div class="section-block">
                <div class="form-title">🍽️ Pilih Tipe per Menu</div>
                <p style="font-size:.85rem; color:var(--muted); margin-bottom:1.2rem;">
                    Pilih apakah setiap item dimakan di tempat atau dibawa pulang.
                </p>

                @foreach($cart as $id => $item)
                <div class="item-row" id="item-row-{{ $loop->index }}">
                    <div class="item-info">
                        <div class="item-name">{{ $item['name'] }}</div>
                        <div class="item-price">×{{ $item['quantity'] }} · Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>

                        {{-- Take away method (pickup/delivery) - hidden by default --}}
                        <div class="takeaway-method" id="method-{{ $loop->index }}">
                            <input type="hidden" name="items[{{ $loop->index }}][take_away_method]" id="method-val-{{ $loop->index }}" value="pickup">
                            <button type="button" class="method-btn active" onclick="setMethod({{ $loop->index }}, 'pickup', this)">🏃 Ambil Sendiri</button>
                            <button type="button" class="method-btn" onclick="setMethod({{ $loop->index }}, 'delivery', this)">🛵 Delivery (+Rp 8.000)</button>
                        </div>
                    </div>
                    <div>
                        <input type="hidden" name="items[{{ $loop->index }}][cart_key]" value="{{ $id }}">
                        <input type="hidden" name="items[{{ $loop->index }}][item_order_type]" id="type-val-{{ $loop->index }}" value="dine_in">
                        <div class="item-type-selector">
                            <button type="button" class="type-btn active-dinein" id="btn-dinein-{{ $loop->index }}"
                                onclick="setType({{ $loop->index }}, 'dine_in')">🪑 Dine In</button>
                            <button type="button" class="type-btn" id="btn-takeaway-{{ $loop->index }}"
                                onclick="setType({{ $loop->index }}, 'take_away')">🥤 Take Away</button>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Nomor Meja (muncul kalau ada item dine in) --}}
                <div class="table-field show" id="tableNumberField">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Nomor Meja <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(untuk item Dine In)</span></label>
                        <input type="text" name="table_number" value="{{ old('table_number') }}" placeholder="Contoh: A1, B3">
                        @error('table_number')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Alamat Delivery (muncul kalau ada item take away delivery) --}}
                <div class="delivery-field" id="deliveryAddressField" style="margin-top:.8rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Alamat Pengantaran <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(untuk item Take Away - Delivery)</span></label>
                        <textarea name="delivery_address" rows="2" placeholder="Alamat lengkap untuk delivery...">{{ old('delivery_address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="section-block">
                <div class="form-title">💳 Metode Pembayaran</div>
                <div class="payment-grid">
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="cash" checked>
                        <span class="icon">💵</span><span class="label">Cash</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="qris">
                        <span class="icon">📱</span><span class="label">QRIS</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="dana">
                        <span class="icon">💙</span><span class="label">DANA</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="ovo">
                        <span class="icon">💜</span><span class="label">OVO</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="bsi">
                        <span class="icon">🏦</span><span class="label">Bank BSI</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="bank_aceh">
                        <span class="icon">🏛️</span><span class="label">Bank Aceh</span>
                    </label>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="section-block">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Catatan <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional)</span></label>
                    <textarea name="notes" rows="2" placeholder="less sugar, no ice, dll...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">Konfirmasi Pesanan →</button>
        </form>
    </div>

    {{-- Summary --}}
    <div>
        <div class="order-summary">
            <div class="sum-title">Pesananmu</div>
            @foreach($cart as $item)
            <div class="sum-item">
                <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div style="display:flex; justify-content:space-between; font-size:.9rem; padding:.5rem 0; color:var(--muted);" id="deliveryFeeRow" style="display:none;">
                <span>Ongkir</span>
                <span id="deliveryFeeAmount">Rp 0</span>
            </div>
            <div class="sum-total">
                <span>Total</span>
                <span id="grandTotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    const subtotal = {{ $total }};
    let deliveryItems = 0;
    const deliveryFeePerItem = 8000;

    function setType(index, type) {
        document.getElementById('type-val-' + index).value = type;

        const btnDineIn   = document.getElementById('btn-dinein-' + index);
        const btnTakeAway = document.getElementById('btn-takeaway-' + index);
        const methodRow   = document.getElementById('method-' + index);

        if (type === 'dine_in') {
            btnDineIn.className   = 'type-btn active-dinein';
            btnTakeAway.className = 'type-btn';
            methodRow.classList.remove('show');
            // Reset take away method
            document.getElementById('method-val-' + index).value = 'pickup';
        } else {
            btnTakeAway.className = 'type-btn active-takeaway';
            btnDineIn.className   = 'type-btn';
            methodRow.classList.add('show');
        }

        updateUI();
    }

    function setMethod(index, method, btn) {
        document.getElementById('method-val-' + index).value = method;
        document.querySelectorAll('#method-' + index + ' .method-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateUI();
    }

    function updateUI() {
        // Cek apakah ada item dine in
        const hasDineIn = Array.from(document.querySelectorAll('[id^="type-val-"]'))
            .some(el => el.value === 'dine_in');

        // Cek apakah ada item take away delivery
        const hasDelivery = Array.from(document.querySelectorAll('[id^="method-val-"]'))
            .some((el, i) => {
                const typeEl = document.getElementById('type-val-' + i);
                return typeEl && typeEl.value === 'take_away' && el.value === 'delivery';
            });

        // Toggle nomor meja
        const tableField = document.getElementById('tableNumberField');
        tableField.classList.toggle('show', hasDineIn);
        document.querySelector('input[name="table_number"]').required = hasDineIn;

        // Toggle alamat delivery
        const deliveryField = document.getElementById('deliveryAddressField');
        deliveryField.classList.toggle('show', hasDelivery);
        document.querySelector('textarea[name="delivery_address"]').required = hasDelivery;

        // Hitung ongkir (1 kali flat kalau ada delivery)
        const deliveryFee = hasDelivery ? 8000 : 0;
        const grandTotal  = subtotal + deliveryFee;

        document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');

        const feeRow = document.getElementById('deliveryFeeRow');
        if (feeRow) {
            feeRow.style.display = hasDelivery ? 'flex' : 'none';
            document.getElementById('deliveryFeeAmount').textContent = hasDelivery ? 'Rp 8.000' : 'Rp 0';
        }
    }

    // Init
    updateUI();
</script>
@endsection