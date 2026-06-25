<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeDelivery = Delivery::with('order.items.product')
            ->where('courier_id', auth()->id())
            ->whereIn('status', ['assigned', 'on_the_way'])
            ->first();

        $history = Delivery::with('order')
            ->where('courier_id', auth()->id())
            ->whereIn('status', ['delivered', 'failed'])
            ->whereDate('created_at', today())
            ->orderByDesc('delivered_at')
            ->get();

        $todayCount = Delivery::where('courier_id', auth()->id())
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();

        $courierStatus = auth()->user()->courier_status;

        return view('kurir.dashboard', compact(
            'activeDelivery', 'history', 'todayCount', 'courierStatus'
        ));
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        if ($delivery->courier_id !== auth()->id()) abort(403);

        $request->validate(['status' => 'required|in:on_the_way,delivered,failed']);

        $delivery->status = $request->status;

        if ($request->status === 'on_the_way') {
            $delivery->picked_up_at = now();
            auth()->user()->update(['courier_status' => 'busy']);
        } elseif (in_array($request->status, ['delivered', 'failed'])) {
            $delivery->delivered_at = now();
            auth()->user()->update(['courier_status' => 'available']);

            if ($request->status === 'delivered') {
                $delivery->order->update([
                    'status' => 'completed',
                    'payment_status' => $delivery->order->payment_method === 'cash' ? 'paid' : $delivery->order->payment_status,
                ]);
            }
        }

        $delivery->save();

        ActivityLog::record('UPDATE_DELIVERY', 'kurir',
            "Kurir update delivery {$delivery->order->order_code}: {$request->status}");

        return back()->with('success', 'Status delivery diperbarui.');
    }

    public function updateCourierStatus(Request $request)
    {
        $request->validate(['status' => 'required|in:available,busy']);
        auth()->user()->update(['courier_status' => $request->status]);

        return back()->with('success', 'Status kamu diperbarui: ' . ($request->status === 'available' ? 'Kosong' : 'Sibuk'));
    }
}