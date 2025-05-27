<?php

namespace App\Http\Controllers;

use App\Models\JitNotification;
use Illuminate\Http\Request;

class JitNotificationController extends Controller
{
    public function markAsReadAndRedirect(JitNotification $jitNotification)
    {
        if ($jitNotification && $jitNotification->status === 'unread') {
            $jitNotification->status = 'read';
            $jitNotification->resolved_at = now();
            $jitNotification->save();
        }

        if ($jitNotification->rawMaterial) {
            return redirect()->route('raw_materials.show', $jitNotification->rawMaterial->slug);
        }

        return redirect()->back()->with('info', 'Notification marked as read.');
    }
}