<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

/**
 * Log user activity.
 *
 * @param int $userId
 * @param string $action
 * @param string|null $description
 * @param string|null $ipAddress
 * @return ActivityLog
 */
function activity_log(int $userId, string $action, ?string $description = null, ?string $ipAddress = null): ActivityLog
{
    return ActivityLog::create([
        'user_id' => $userId,
        'action' => $action,
        'description' => $description,
        'ip_address' => $ipAddress ?? request()->ip(),
    ]);
}

/**
 * Send a notification to a user.
 *
 * @param int $userId
 * @param string $title
 * @param string $message
 * @return bool
 */
function send_notification(int $userId, string $title, string $message): bool
{
    // For now using log driver as specified in requirements
    // In production, this could be expanded to send emails, push notifications, etc.
    
    try {
        // Log to file for demonstration
        \Log::info("Notification for User {$userId}", [
            'title' => $title,
            'message' => $message,
        ]);

        // If mail driver is not 'log', send email
        if (config('mail.driver') !== 'log') {
            $user = \App\Models\User::find($userId);
            if ($user) {
                Mail::to($user)->send(new NotificationMail($title, $message));
            }
        }

        return true;
    } catch (\Exception $e) {
        \Log::error("Failed to send notification: " . $e->getMessage());
        return false;
    }
}
