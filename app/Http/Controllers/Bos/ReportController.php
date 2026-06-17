<?php

namespace App\Http\Controllers\Bos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Laporan bulanan
        $monthlyOrders = Order::where('payment_status', 'paid')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $monthlyRevenue  = $monthlyOrders->sum(fn($o) => $o->total_price + $o->delivery_fee);
        $monthlyCount    = $monthlyOrders->count();

        // Laporan per bulan dalam setahun (untuk chart)
        $yearlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $orders = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->get();

            $yearlyData[$m] = [
                'revenue' => $orders->sum(fn($o) => $o->total_price + $o->delivery_fee),
                'count'   => $orders->count(),
            ];
        }

        $yearlyRevenue = collect($yearlyData)->sum('revenue');
        $yearlyCount   = collect($yearlyData)->sum('count');

        // Top produk bulan ini
        $topProducts = \App\Models\OrderItem::with('product')
            ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month))
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('bos.report', compact(
            'year', 'month', 'monthlyOrders', 'monthlyRevenue', 'monthlyCount',
            'yearlyData', 'yearlyRevenue', 'yearlyCount', 'topProducts'
        ));
    }

    public function downloadPdf(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $orders = Order::where('payment_status', 'paid')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('items.product')
            ->get();

        $revenue = $orders->sum(fn($o) => $o->total_price + $o->delivery_fee);

        $pdf = \PDF::loadView('bos.report-pdf', compact('orders', 'revenue', 'year', 'month'));

        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        return $pdf->download('laporan-' . $bulan[$month] . '-' . $year . '.pdf');
    }

    public function downloadExcel(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $orders = Order::where('payment_status', 'paid')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('items.product')
            ->get();

        $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $filename = 'laporan-' . $bulan[$month] . '-' . $year . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders, $year, $month, $bulan) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['LAPORAN KEUANGAN BREWNEST COFFEE']);
            fputcsv($file, ['Periode: ' . $bulan[$month] . ' ' . $year]);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Kode Order', 'Nama', 'Tipe', 'Pembayaran', 'Subtotal', 'Ongkir', 'Total', 'Tanggal']);

            $no = 1;
            foreach ($orders as $order) {
                fputcsv($file, [
                    $no++,
                    $order->order_code,
                    $order->customer_name,
                    $order->order_type_label,
                    $order->payment_method_label,
                    $order->total_price,
                    $order->delivery_fee,
                    $order->grand_total,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL', '', '', $orders->sum(fn($o) => $o->grand_total), '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}