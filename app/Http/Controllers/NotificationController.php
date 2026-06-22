<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Mark all as read
        UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = auth()->check()
            ? UserNotification::where('user_id', auth()->id())->where('is_read', false)->count()
            : 0;

        return response()->json(['count' => $count]);
    }
}