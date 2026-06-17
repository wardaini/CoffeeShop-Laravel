<?php

namespace App\Http\Controllers\Bos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue     = Order::where('payment_status', 'paid')->sum('total_price');
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        $totalOrders      = Order::count();
        $totalEmployees   = User::where('role', 'karyawan')->count();

        return view('bos.dashboard', compact(
            'totalRevenue', 'revenueThisMonth', 'totalOrders', 'totalEmployees'
        ));
    }
}