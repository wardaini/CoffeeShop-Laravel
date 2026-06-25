<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $items = OrderItem::with(['order', 'product'])
            ->where('assigned_to', 'barista')
            ->whereIn('kitchen_status', ['pending', 'processing'])
            ->orderBy('created_at')
            ->get();

        $doneToday = OrderItem::with(['order', 'product'])
            ->where('assigned_to', 'barista')
            ->where('kitchen_status', 'ready')
            ->whereDate('updated_at', today())
            ->count();

        return view('barista.dashboard', compact('items', 'doneToday'));
    }

    public function updateStatus(OrderItem $item)
    {
        $newStatus = match($item->kitchen_status) {
            'pending'    => 'processing',
            'processing' => 'ready',
            default      => 'ready',
        };

        $item->update(['kitchen_status' => $newStatus]);

        // Cek apakah semua item dalam order sudah ready
        $allReady = $item->order->items()
            ->where('kitchen_status', '!=', 'ready')
            ->doesntExist();

        if ($allReady) {
            $item->order->update(['status' => 'ready']);
        }

        ActivityLog::record('UPDATE_KITCHEN_STATUS', 'barista',
            "Barista update status {$item->product->name}: {$newStatus}");

        return back()->with('success', "Status diperbarui: {$item->kitchen_status_label}");
    }
}