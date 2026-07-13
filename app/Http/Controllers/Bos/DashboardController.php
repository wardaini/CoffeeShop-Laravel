<?php

namespace App\Http\Controllers\Bos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue     = Order::where('payment_status', 'paid')->sum('total_price');
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        $totalOrders    = Order::count();
        $totalEmployees = User::where('role', 'karyawan')->count();

        return view('bos.dashboard', compact(
            'totalRevenue', 'revenueThisMonth', 'totalOrders', 'totalEmployees'
        ));
    }

    public function employees()
    {
        $employees = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('verification_status', 'verified'))
            ->with('employeeProfile')
            ->orderBy('name')
            ->paginate(20);

        return view('bos.employees', compact('employees'));
    }

    public function attendances(Request $request)
    {
        $query = Attendance::with('user')->orderByDesc('date')->orderBy('user_id');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->paginate(25)->withQueryString();
        $employees   = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('bos.attendances', compact('attendances', 'employees'));
    }
}