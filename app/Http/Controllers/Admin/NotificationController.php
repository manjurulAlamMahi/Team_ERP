<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $notification = auth()->user()->notifications()->findOrFail($request->notification_id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function clearAllNotifications()
    {
        auth()->user()->notifications()->delete();

        return back()->with(['success', 'All notifications cleared']);
    }

    public function markAllNotificationsRead()
    {
        auth()->user()->notifications->markAsRead();
        return back()->with(['success', 'All notifications marked as read']);
    }
}
