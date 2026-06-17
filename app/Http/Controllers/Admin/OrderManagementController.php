<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function confirmPayment(Order $order)
    {
        $order->update(['payment_status' => 'paid']);

        return back()->with('success', 'Pembayaran order ' . $order->order_code . ' dikonfirmasi.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status order diperbarui.');
    }

    public function assignDelivery(Request $request, Order $order)
    {
        $request->validate(['courier_id' => 'required|exists:users,id']);

        $delivery = $order->delivery;

        if (!$delivery) {
            $delivery = $order->delivery()->create(['status' => 'waiting']);
        }

        $delivery->update([
            'courier_id'  => $request->courier_id,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Kurir berhasil ditugaskan.');
    }

    public function couriers()
    {
        return User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kurir'))
            ->get();
    }
}