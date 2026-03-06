<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index()
    {
        $user = Auth::user();
        
        // distinct types of notifications might need different parsing in view,
        // but for now we pass them all.
        $notifications = $user->notifications()->paginate(15);
        $notifications->setCollection(
            $notifications->getCollection()->map(function ($notification) {
                $isUnread = !$notification->read_at;
                $iconClass = 'bg-gray-100 text-gray-500';
                $icon = '<i class="fa-solid fa-bell"></i>';
                $message = strtolower($notification->data['message'] ?? '');

                if ($notification->type === 'App\\Notifications\\NewServiceRequest') {
                    $iconClass = 'bg-blue-100 text-blue-600';
                    $icon = '<i class="fa-solid fa-calendar-plus"></i>';
                } elseif ($notification->type === 'App\\Notifications\\ServiceRequestStatusUpdated') {
                    if (str_contains($message, 'accepted')) {
                        $iconClass = 'bg-green-100 text-green-600';
                        $icon = '<i class="fa-solid fa-check"></i>';
                    } elseif (str_contains($message, 'rejected')) {
                        $iconClass = 'bg-red-100 text-red-600';
                        $icon = '<i class="fa-solid fa-xmark"></i>';
                    } else {
                        $iconClass = 'bg-indigo-100 text-indigo-600';
                        $icon = '<i class="fa-solid fa-info"></i>';
                    }
                } elseif ($notification->type === 'App\\Notifications\\AdminWarningNotification') {
                    $iconClass = 'bg-red-50 text-red-600 ring-4 ring-red-50';
                    $icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
                }

                $notification->ui_is_unread = $isUnread;
                $notification->ui_icon_class = $iconClass;
                $notification->ui_icon = $icon;
                return $notification;
            })
        );

        $hasUnreadNotifications = $user->unreadNotifications()->exists();
        return view('notifications.index', compact('notifications', 'hasUnreadNotifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // If notification has an action URL, redirect there
        if (isset($notification->data['action_url']) && $notification->data['action_url'] !== '#') {
            return redirect($notification->data['action_url']);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
