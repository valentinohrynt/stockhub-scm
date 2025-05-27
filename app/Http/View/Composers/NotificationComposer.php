<?php
namespace App\Http\View\Composers;

use App\Models\JitNotification;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view)
    {
        $unreadJitNotificationsForNavbar = JitNotification::where('status', 'unread')
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();

        $totalUnreadJitNotificationCount = JitNotification::where('status', 'unread')->count();

        $view->with('unreadJitNotificationsGlobal_navbar', $unreadJitNotificationsForNavbar);
        $view->with('unreadJitNotificationCountGlobal', $totalUnreadJitNotificationCount);
    }
}