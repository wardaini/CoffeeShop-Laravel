<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: Arial, sans-serif; box-sizing: border-box; }
        body { padding: 20px; font-size: 12px; color: #333; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; font-size: 11px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-box { background: #f9f6f0; padding: 12px 20px; border-radius: 8px; flex: 1; text-align: center; }
        .stat-box .num { font-size: 16px; font-weight: bold; color: #8B6914; }
        .stat-box .lbl { font-size: 10px; color: #666; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #8B6914; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        tr:nth-child(even) { background: #fafafa; }
        .total-row td { font-weight: bold; background: #f9f6f0; border-top: 2px solid #8B6914; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <h1>LAPORAN KEUANGAN BREWNEST COFFEE</h1>
    @php $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
    <div class="subtitle">Periode: {{ $bulan[$month] }} {{ $year }} · Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    <table style="width:100%; border:none; margin-bottom:20px;">
        <tr>
            <td style="border:none; background:#f9f6f0; padding:12px 20px; border-radius:8px; text-align:center;">
                <div style="font-size:16px; font-weight:bold; color:#8B6914;">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                <div style="font-size:10px; color:#666; margin-top:3px;">Total Pendapatan</div>
            </td>
            <td style="border:none; width:20px;"></td>
            <td style="border:none; background:#f9f6f0; padding:12px 20px; border-radius:8px; text-align:center;">
                <div style="font-size:16px; font-weight:bold; color:#8B6914;">{{ $orders->count() }}</div>
                <div style="font-size:10px; color:#666; margin-top:3px;">Total Transaksi</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Order</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Pembayaran</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $order->order_code }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->order_type_label }}</td>
                <td>{{ $order->payment_method_label }}</td>
                <td>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5">TOTAL</td>
                <td>Rp {{ number_format($revenue, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">Laporan ini dibuat otomatis oleh sistem BrewNest Coffee</div>
</body>
</html>