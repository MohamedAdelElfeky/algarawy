<?php

namespace App\Http\Controllers;

use App\Domain\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications;

        return response()->json($notifications);
    }

    public function update(Notification $notification)
    {
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if (!$notification) {
            return response()->json(['message' => 'لم يتم العثور على الاشعار'], 404);
        }
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllNotificationsAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->update(['read_at' => now(), 'read' => true]);
        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function getNewNotifications()
    {
        $count_notification = Notification::where('user_id', Auth::id())
            ->where('read', false)->whereNull('read_at')->count();
        return response()->json(['countNewNotification' => $count_notification], 200);
    }
}
