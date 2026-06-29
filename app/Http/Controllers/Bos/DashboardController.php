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

    public function employees()
{
    $employees = \App\Models\User::where('role', 'karyawan')
        ->whereHas('employeeProfile', fn($q) => $q->where('verification_status', 'verified'))
        ->with('employeeProfile')
        ->orderBy('name')
        ->paginate(20);

    return view('bos.employees', compact('employees'));
}

    public function attendances(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Attendance::with('user')->orderByDesc('date');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $attendances = $query->paginate(20)->withQueryString();

        return view('bos.attendances', compact('attendances'));
    }
}