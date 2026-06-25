<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingOrders   = Order::where('status', 'pending')->count();
        $processingOrders= Order::where('status', 'processing')->count();
        $unpaidOrders    = Order::where('payment_status', 'unpaid')->count();
        $todayRevenue    = Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_price');
        $todayCount      = Order::whereDate('created_at', today())->count();

        $availableCouriers = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kurir'))
            ->where('courier_status', 'available')
            ->where('is_active', true)
            ->count();

        $recentOrders = Order::with('items.product')->latest()->take(10)->get();

        return view('kasir.dashboard', compact(
            'pendingOrders', 'processingOrders', 'unpaidOrders',
            'todayRevenue', 'todayCount', 'availableCouriers', 'recentOrders'
        ));
    }
}