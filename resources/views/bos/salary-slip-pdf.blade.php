<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: Arial, sans-serif; box-sizing: border-box; }
        body { padding: 30px; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #8B6914; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #8B6914; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; margin: 2px 0; }
        .slip-title { text-align: center; font-size: 14px; font-weight: bold; margin: 15px 0; background: #f9f6f0; padding: 8px; border-radius: 4px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px 8px; font-size: 11px; }
        .info-table .key { color: #666; width: 40%; }
        .info-table .val { font-weight: bold; }
        .salary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .salary-table th { background: #8B6914; color: white; padding: 8px; text-align: left; font-size: 11px; }
        .salary-table td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .salary-table .amount { text-align: right; }
        .total-row td { font-weight: bold; background: #f9f6f0; border-top: 2px solid #8B6914; font-size: 13px; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #999; }
        .status-paid { background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BREWNEST COFFEE</h1>
        <p>Lhokseumawe, Aceh · Telp: 0812-3456-7890</p>
    </div>

    <div class="slip-title">SLIP GAJI KARYAWAN — {{ strtoupper($salary->period_label) }}</div>

    <table class="info-table">
        <tr>
            <td class="key">Nama Karyawan</td>
            <td class="val">{{ $salary->user->name }}</td>
            <td class="key">Periode</td>
            <td class="val">{{ $salary->period_label }}</td>
        </tr>
        <tr>
            <td class="key">Posisi</td>
            <td class="val">{{ $salary->user->employeeProfile->position ?? '-' }}</td>
            <td class="key">Tanggal Cetak</td>
            <td class="val">{{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="key">Kode Karyawan</td>
            <td class="val">{{ $salary->user->employeeProfile->employee_code ?? '-' }}</td>
            <td class="key">Status</td>
            <td class="val"><span class="status-paid">LUNAS</span></td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th class="amount">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Hari Hadir ({{ $salary->total_present }} hari)</td>
                <td class="amount">—</td>
            </tr>
            @if($salary->bonus > 0)
            <tr>
                <td>Bonus {{ $salary->notes ? '(' . $salary->notes . ')' : '' }}</td>
                <td class="amount" style="color:green;">+ Rp {{ number_format($salary->bonus, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($salary->deduction > 0)
            <tr>
                <td>Potongan (Alpha/Telat)</td>
                <td class="amount" style="color:red;">- Rp {{ number_format($salary->deduction, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL GAJI DITERIMA</td>
                <td class="amount">Rp {{ number_format($salary->total_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; margin-top:40px; font-size:11px;">
        <div style="text-align:center;">
            <p>Diterima oleh,</p>
            <br><br><br>
            <p>{{ $salary->user->name }}</p>
        </div>
        <div style="text-align:center;">
            <p>Disetujui oleh,</p>
            <br><br><br>
            <p>Pemilik BrewNest Coffee</p>
        </div>
    </div>

    <div class="footer">Slip gaji ini dicetak otomatis oleh sistem BrewNest Coffee</div>
</body>
</html>