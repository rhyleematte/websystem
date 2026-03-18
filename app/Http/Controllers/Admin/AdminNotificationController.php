<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\AdminNotificationRead;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $me = Auth::guard('admin')->user();
        $notifications = AdminNotification::latest()->paginate(20);

        // Mark all as read
        foreach ($notifications as $n) {
            if (!$n->isReadBy($me->id)) {
                AdminNotificationRead::firstOrCreate([
                    'admin_notification_id' => $n->id,
                    'admin_id'              => $me->id,
                ], ['read_at' => now()]);
            }
        }

        return view('admin.notifications.index', compact('notifications', 'me'));
    }

    public function markRead($id)
    {
        $me = Auth::guard('admin')->user();
        AdminNotificationRead::firstOrCreate([
            'admin_notification_id' => $id,
            'admin_id'              => $me->id,
        ], ['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function unreadCount()
    {
        $me = Auth::guard('admin')->user();
        return response()->json([
            'count' => AdminNotification::unreadCountFor($me->id)
        ]);
    }
}
