<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationController extends ApiResponseController
{
    public function getUnreadNotifications(Request $request)
    {
        $doctorId = $this->getEmployee()->id; // Giả định bác sĩ đã đăng nhập và có id
        $notifications = Notification::where('idEmployee', $doctorId)
                                    ->orderBy('created_at', 'desc') // Order by newest first
                                    ->take(10) // Limit to 10 notifications
                                    ->get();

        Log::info('Fetched notifications for doctor', ['doctorId' => $doctorId, 'count' => $notifications->count()]);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationsAsRead(Request $request)
    {
        $doctorId = $this->getEmployee()->id;
        $updated = Notification::where('idEmployee', $doctorId)
                              ->whereNull('read_at')
                              ->update(['read_at' => now()]);

        Log::info('Marked notifications as read', ['doctorId' => $doctorId, 'updated_count' => $updated]);

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read',
        ]);
    }
}