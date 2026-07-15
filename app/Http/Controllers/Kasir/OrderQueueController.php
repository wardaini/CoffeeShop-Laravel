<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Delivery;
use App\Models\ActivityLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class OrderQueueController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product.category', 'delivery.courier'])
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->orderBy('created_at')
            ->get();

        $couriers = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kurir'))
            ->where('is_active', true)
            ->with('employeeProfile')
            ->get();

        return view('kasir.queue', compact('orders', 'couriers'));
    }

    public function confirmPayment(Order $order)
    {
        $order->update(['payment_status' => 'paid']);

        ActivityLog::record('CONFIRM_PAYMENT', 'kasir',
            "Kasir konfirmasi pembayaran order {$order->order_code} ({$order->payment_method_label})");

        return back()->with('success', "Pembayaran {$order->order_code} dikonfirmasi lunas.");
    }

    public function sendToKitchen(Request $request, Order $order)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.assigned_to' => 'required|in:barista,dapur',
        ]);

        foreach ($request->items as $itemData) {
            OrderItem::where('id', $itemData['id'])->update([
                'assigned_to'    => $itemData['assigned_to'],
                'kitchen_status' => 'pending',
            ]);
        }

        $order->update(['status' => 'processing']);

        ActivityLog::record('SEND_TO_KITCHEN', 'kasir', "Kasir kirim order {$order->order_code} ke dapur/barista");

        return back()->with('success', "Pesanan {$order->order_code} dikirim ke dapur/barista.");
    }

    public function assignCourier(Request $request, Order $order)
    {
        $request->validate(['courier_id' => 'required|exists:users,id']);

        $courier = User::findOrFail($request->courier_id);

        $delivery = $order->delivery ?? $order->delivery()->create(['status' => 'waiting']);
        $delivery->update([
            'courier_id'  => $courier->id,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ]);

        $courier->update(['courier_status' => 'busy']);

        UserNotification::send(
            $courier->id,
            '🛵 Pesanan Baru Ditugaskan',
            "Kamu mendapat tugas delivery order {$order->order_code} ke {$order->delivery_address}.",
            '🛵',
            '/kurir/dashboard'
        );

        ActivityLog::record('ASSIGN_COURIER', 'kasir', "Kasir assign kurir {$courier->name} untuk order {$order->order_code}");

        return back()->with('success', "Kurir {$courier->name} berhasil ditugaskan.");
    }

    public function availableCouriers()
    {
        $couriers = User::where('role', 'karyawan')
            ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kurir'))
            ->where('courier_status', 'available')
            ->where('is_active', true)
            ->with('employeeProfile')
            ->get()
            ->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'code' => $c->employeeProfile->employee_code ?? '-',
            ]);

        return response()->json($couriers);
    }

    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);

        ActivityLog::record('COMPLETE_ORDER', 'kasir', "Kasir selesaikan order {$order->order_code}");

        return back()->with('success', "Order {$order->order_code} selesai.");
    }

    public function allOrders(Request $request)
    {
        $query = \App\Models\Order::with('items.product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders  = $query->paginate(20)->withQueryString();
        $todayRevenue = \App\Models\Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_price');
        $todayCount = \App\Models\Order::whereDate('created_at', today())->count();

        return view('kasir.orders', compact('orders', 'todayRevenue', 'todayCount'));
    }
}