<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->paginate(20);

        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json(['message' => 'Notification marked as read.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();

            return redirect()->back()
                ->with('toast_success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'Failed to mark notifications: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->delete();

            return response()->json(['message' => 'Notification deleted.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
