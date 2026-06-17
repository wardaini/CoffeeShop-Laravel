@extends('layouts.app')
@section('title', 'Checkout — BrewNest')

@push('styles')
<style>
    .checkout-wrap { max-width: 1000px; margin: 0 auto; padding: 4rem 5%; display: grid; grid-template-columns: 1fr 340px; gap: 2.5rem; }
    .form-title { font-family:'Playfair Display',serif; font-size:1.4rem; color:var(--cream); margin-bottom:1.2rem; }
    .section-block { background: var(--card); border:1px solid rgba(200,151,58,.1); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; }
    .form-group { margin-bottom: 1.3rem; }
    .form-group label { display:block; font-size:.82rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.5rem; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; padding:.75rem 1rem;
        background:var(--surface);
        border:1px solid rgba(200,151,58,.2);
        border-radius:8px;
        color:var(--text);
        font-family:'DM Sans',sans-serif;
        font-size:.95rem;
        outline:none; transition:border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#e07070; margin-top:.35rem; }

    /* Radio Cards */
    .radio-group { display:grid; grid-template-columns:repeat(2,1fr); gap:.8rem; }
    .radio-card {
        position:relative; cursor:pointer;
        border:1px solid rgba(200,151,58,.2);
        border-radius:8px;
        padding:1rem;
        text-align:center;
        transition: border-color .2s, background .2s;
    }
    .radio-card input { position:absolute; opacity:0; }
    .radio-card .icon { font-size:1.6rem; display:block; margin-bottom:.4rem; }
    .radio-card .label { font-size:.85rem; color:var(--text); }
    .radio-card:has(input:checked) { border-color: var(--gold); background: rgba(200,151,58,.08); }

    .payment-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.8rem; }
    @media(max-width:600px){ .payment-grid{ grid-template-columns:repeat(2,1fr); } }

    .conditional-field { display:none; }
    .conditional-field.show { display:block; }

    .order-summary { background:var(--card); border:1px solid rgba(200,151,58,.15); border-radius:12px; padding:1.6rem; height:fit-content; position:sticky; top:90px; }
    .sum-title { font-family:'Playfair Display',serif; font-size:1.2rem; color:var(--cream); margin-bottom:1.2rem; }
    .sum-item { display:flex; justify-content:space-between; font-size:.88rem; margin-bottom:.7rem; color:var(--muted); }
    .sum-row { display:flex; justify-content:space-between; font-size:.9rem; margin-bottom:.5rem; color:var(--text); padding-top:.5rem; border-top:1px solid rgba(200,151,58,.08); }
    .sum-total { display:flex; justify-content:space-between; font-weight:700; font-size:1.15rem; color:var(--gold-soft); border-top:1px solid rgba(200,151,58,.2); padding-top:.9rem; margin-top:.5rem; }
    @media(max-width:768px){ .checkout-wrap{ grid-template-columns:1fr; } }
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
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                        placeholder="Masukkan nama kamu" required>
                    @error('customer_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>No. Telepon <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional — untuk konfirmasi pesanan)</span></label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label>Email <span style="color:var(--muted); font-size:.75rem; text-transform:none;">(opsional — untuk bukti pesanan)</span></label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                        placeholder="email@contoh.com">
                </div>
            </div>

            {{-- Tipe Pesanan --}}
            <div class="section-block">
                <div class="form-title">🍽️ Tipe Pesanan</div>

                <div class="radio-group">
                    <label class="radio-card">
                        <input type="radio" name="order_type" value="dine_in" {{ old('order_type') === 'dine_in' ? 'checked' : '' }} onchange="toggleOrderType()">
                        <span class="icon">🪑</span>
                        <span class="label">Dine In</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="order_type" value="take_away" {{ old('order_type', 'take_away') === 'take_away' ? 'checked' : '' }} onchange="toggleOrderType()">
                        <span class="icon">🥤</span>
                        <span class="label">Take Away</span>
                    </label>
                </div>
                @error('order_type')<div class="form-error">{{ $message }}</div>@enderror

                {{-- Dine In: Nomor Meja --}}
                <div class="form-group conditional-field" id="tableNumberField" style="margin-top:1.2rem;">
                    <label>Nomor Meja *</label>
                    <input type="text" name="table_number" value="{{ old('table_number') }}" placeholder="Contoh: A1, B3">
                    @error('table_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Take Away: Metode --}}
                <div class="conditional-field" id="takeAwayMethodField" style="margin-top:1.2rem;">
                    <label style="display:block; font-size:.82rem; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); margin-bottom:.6rem;">Metode Take Away *</label>
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="take_away_method" value="pickup" {{ old('take_away_method', 'pickup') === 'pickup' ? 'checked' : '' }} onchange="toggleDeliveryAddress()">
                            <span class="icon">🏃</span>
                            <span class="label">Ambil Sendiri</span>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="take_away_method" value="delivery" {{ old('take_away_method') === 'delivery' ? 'checked' : '' }} onchange="toggleDeliveryAddress()">
                            <span class="icon">🛵</span>
                            <span class="label">Delivery (+Rp 8.000)</span>
                        </label>
                    </div>
                    @error('take_away_method')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Alamat Delivery --}}
                <div class="form-group conditional-field" id="deliveryAddressField" style="margin-top:1.2rem;">
                    <label>Alamat Pengantaran *</label>
                    <textarea name="delivery_address" rows="3" placeholder="Tulis alamat lengkap untuk pengantaran...">{{ old('delivery_address') }}</textarea>
                    @error('delivery_address')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="section-block">
                <div class="form-title">💳 Metode Pembayaran</div>

                <div class="payment-grid">
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                        <span class="icon">💵</span>
                        <span class="label">Cash</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="qris" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                        <span class="icon">📱</span>
                        <span class="label">QRIS</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="dana" {{ old('payment_method') === 'dana' ? 'checked' : '' }}>
                        <span class="icon">💙</span>
                        <span class="label">DANA</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="ovo" {{ old('payment_method') === 'ovo' ? 'checked' : '' }}>
                        <span class="icon">💜</span>
                        <span class="label">OVO</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="bsi" {{ old('payment_method') === 'bsi' ? 'checked' : '' }}>
                        <span class="icon">🏦</span>
                        <span class="label">Bank BSI</span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="payment_method" value="bank_aceh" {{ old('payment_method') === 'bank_aceh' ? 'checked' : '' }}>
                        <span class="icon">🏛️</span>
                        <span class="label">Bank Aceh</span>
                    </label>
                </div>
                @error('payment_method')<div class="form-error">{{ $message }}</div>@enderror

                {{-- Info Transfer --}}
                <div id="transferInfo" style="display:none; margin-top:1.2rem; padding:1rem; background:var(--surface); border-radius:8px; font-size:.85rem; color:var(--muted);">
                    <strong style="color:var(--gold-soft);">Detail Transfer:</strong>
                    <div id="transferDetails" style="margin-top:.5rem;"></div>
                    <p style="margin-top:.6rem; font-size:.78rem;">*Lakukan transfer setelah konfirmasi pesanan, lalu tunjukkan bukti transfer ke kasir.</p>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="section-block">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Catatan Pesanan</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: less sugar, no ice...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-gold" style="width:100%;">
                Konfirmasi Pesanan →
            </button>
        </form>
    </div>

    <div>
        <div class="order-summary">
            <div class="sum-title">Pesananmu</div>
            @foreach($cart as $item)
            <div class="sum-item">
                <span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div class="sum-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="sum-row" id="deliveryFeeRow" style="display:none;">
                <span>Ongkir</span>
                <span>Rp 8.000</span>
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
    const deliveryFee = 8000;

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function toggleOrderType() {
        const orderType = document.querySelector('input[name="order_type"]:checked')?.value;

        document.getElementById('tableNumberField').classList.toggle('show', orderType === 'dine_in');
        document.getElementById('takeAwayMethodField').classList.toggle('show', orderType === 'take_away');

        if (orderType === 'take_away') {
            toggleDeliveryAddress();
        } else {
            document.getElementById('deliveryAddressField').classList.remove('show');
            updateTotal(false);
        }
    }

    function toggleDeliveryAddress() {
        const method = document.querySelector('input[name="take_away_method"]:checked')?.value;
        document.getElementById('deliveryAddressField').classList.toggle('show', method === 'delivery');
        updateTotal(method === 'delivery');
    }

    function updateTotal(isDelivery) {
        document.getElementById('deliveryFeeRow').style.display = isDelivery ? 'flex' : 'none';
        const total = isDelivery ? subtotal + deliveryFee : subtotal;
        document.getElementById('grandTotal').textContent = formatRupiah(total);
    }

    // Transfer info
    const transferData = {
        dana: { name: 'DANA', number: '0812-3456-7890', account: 'BrewNest Coffee' },
        ovo: { name: 'OVO', number: '0812-3456-7890', account: 'BrewNest Coffee' },
        bsi: { name: 'Bank BSI', number: '7123456789', account: 'BrewNest Coffee' },
        bank_aceh: { name: 'Bank Aceh', number: '0301234567', account: 'BrewNest Coffee' },
    };

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const info = document.getElementById('transferInfo');
            const details = document.getElementById('transferDetails');
            const data = transferData[this.value];

            if (data) {
                details.innerHTML = `${data.name}: <strong style="color:var(--gold-soft)">${data.number}</strong><br>a.n. ${data.account}`;
                info.style.display = 'block';
            } else {
                info.style.display = 'none';
            }
        });
    });

    // Init on load
    toggleOrderType();
</script>

@endsection