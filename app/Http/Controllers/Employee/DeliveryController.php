<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with('order.items.product')
            ->where('courier_id', auth()->id())
            ->whereIn('status', ['assigned', 'on_the_way'])
            ->orderByDesc('created_at')
            ->get();

        $history = Delivery::with('order')
            ->where('courier_id', auth()->id())
            ->whereIn('status', ['delivered', 'failed'])
            ->orderByDesc('delivered_at')
            ->limit(10)
            ->get();

        return view('employee.delivery.index', compact('deliveries', 'history'));
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        if ($delivery->courier_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:on_the_way,delivered,failed']);

        $delivery->status = $request->status;

        if ($request->status === 'on_the_way') {
            $delivery->picked_up_at = now();
        } elseif (in_array($request->status, ['delivered', 'failed'])) {
            $delivery->delivered_at = now();

            if ($request->status === 'delivered') {
                $delivery->order->update(['status' => 'completed']);

                if (in_array($delivery->order->payment_method, ['cash'])) {
                    $delivery->order->update(['payment_status' => 'paid']);
                }
            }
        }

        $delivery->save();

        return back()->with('success', 'Status delivery diperbarui.');
    }
}