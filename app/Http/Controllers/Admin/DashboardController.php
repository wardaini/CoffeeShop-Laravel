<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders    = Order::count();
        $pendingOrders  = Order::where('status', 'pending')->count();
        $unpaidOrders   = Order::where('payment_status', 'unpaid')->count();
        $totalRevenue   = Order::where('payment_status', 'paid')->sum('total_price');
        $totalEmployees = User::where('role', 'karyawan')->count();
        $pendingVerif   = \App\Models\EmployeeProfile::where('verification_status', 'pending')->count();
        $recentOrders   = Order::with('items')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'pendingOrders', 'unpaidOrders',
            'totalRevenue', 'totalEmployees', 'pendingVerif', 'recentOrders'
        ));
    }
}