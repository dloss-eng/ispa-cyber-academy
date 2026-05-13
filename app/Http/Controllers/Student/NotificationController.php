<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('dashboard.notifications', compact('notifications'));
    }

    public function markAsRead(UserNotification $notification)
    {
        // 🔐 Seul le propriétaire peut marquer comme lu
        abort_if($notification->user_id !== Auth::id(), 403);

        $notification->markAsRead();

        return back()->with('success', 'Notification lue.');
    }

    public function markAllAsRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }
}
