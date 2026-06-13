<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk - {{ $order->order_code }}</title>
    <style>
        * { font-family: 'Courier New', monospace; box-sizing: border-box; }
        body { width: 280px; margin: 0 auto; padding: 10px; font-size: 12px; color: #000; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; font-size: 11px; }
        td { padding: 2px 0; }
        .right { text-align: right; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { margin: 2px 0; font-size: 10px; }
        .item-name { font-size: 11px; }
        .total-row td { font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>
    <div class="header center">
        <h1>☕ BREWNEST COFFEE</h1>
        <p>Lhokseumawe, Aceh</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="line"></div>

    <table>
        <tr><td>No. Pesanan</td><td class="right bold">{{ $order->order_code }}</td></tr>
        <tr><td>Tanggal</td><td class="right">{{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Nama</td><td class="right">{{ $order->customer_name }}</td></tr>
        <tr><td>Tipe</td><td class="right">{{ $order->order_type_label }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach($order->items as $item)
        <tr>
            <td colspan="2" class="item-name bold">{{ $item->product->name ?? 'Produk' }}</td>
        </tr>
        <tr>
            <td>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Subtotal</td><td class="right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td></tr>
        @if($order->delivery_fee > 0)
        <tr><td>Ongkir</td><td class="right">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td></tr>
        @endif
        <tr class="total-row"><td>TOTAL</td><td class="right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Pembayaran</td><td class="right">{{ $order->payment_method_label }}</td></tr>
        <tr><td>Status</td><td class="right">{{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}</td></tr>
    </table>

    <div class="line"></div>

    <div class="center" style="margin-top:10px;">
        <p>Terima kasih telah berkunjung!</p>
        <p>~ BrewNest Coffee ~</p>
    </div>
</body>
</html>