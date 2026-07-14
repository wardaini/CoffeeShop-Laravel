<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ActivityLog;
use App\Models\UserNotification;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $items = OrderItem::with(['order', 'product'])
            ->where('assigned_to', 'barista')
            ->whereIn('kitchen_status', ['pending', 'processing'])
            ->orderBy('created_at')
            ->get();

        $doneToday = OrderItem::where('assigned_to', 'barista')
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

            // Notifikasi ke semua kasir
            $kasirs = User::where('role', 'karyawan')
                ->whereHas('employeeProfile', fn($q) => $q->where('position', 'Kasir'))
                ->where('is_active', true)
                ->get();

            foreach ($kasirs as $kasir) {
                UserNotification::send(
                    $kasir->id,
                    '✅ Pesanan Siap!',
                    "Order {$item->order->order_code} ({$item->order->customer_name}) sudah siap " .
                    ($item->order->take_away_method === 'delivery' ? 'untuk diantar.' : 'untuk disajikan.'),
                    '✅',
                    '/kasir/antrian'
                );
            }
        }

        ActivityLog::record(
            'UPDATE_KITCHEN_STATUS',
            'barista',
            "Barista update status {$item->product->name}: {$newStatus}"
        );

        return back()->with('success', "Status diperbarui: {$item->kitchen_status_label}");
    }
}