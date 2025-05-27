<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JitNotification;

class HomeController extends Controller
{
    //
    public function index()
    {
        $dashboardUnreadJitNotifications = JitNotification::where('status', 'unread')
                                                ->orderBy('created_at', 'desc')
                                                ->get(); 

        return view('content.home.index', [ 
            'dashboardUnreadJitNotifications' => $dashboardUnreadJitNotifications
        ]);
    }
}
