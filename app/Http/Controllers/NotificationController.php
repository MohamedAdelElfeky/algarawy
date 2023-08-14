<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllNotificationsAsRead()
    {
        dd('d');
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->update(['read_at' => now(), 'read' => true]);
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
