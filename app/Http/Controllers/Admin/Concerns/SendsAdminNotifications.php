<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

trait SendsAdminNotifications
{
    private function sendAdminMailSafely(?string $recipient, object $mailable, string $context, array $meta = []): bool
    {
        $recipient = trim((string) $recipient);

        if ($recipient === '') {
            Log::warning('Admin mail delivery skipped because recipient is missing', array_merge($meta, [
                'context' => $context,
            ]));

            return false;
        }

        try {
            Mail::to($recipient)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Admin mail delivery failed', array_merge($meta, [
                'context' => $context,
                'recipient' => $recipient,
                'message' => $e->getMessage(),
            ]));

            return false;
        }
    }

    private function notifyAdminUserSafely(object $notifiable, object $notification, string $context, array $meta = []): bool
    {
        try {
            $notifiable->notify($notification);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Admin notification delivery failed', array_merge($meta, [
                'context' => $context,
                'message' => $e->getMessage(),
            ]));

            return false;
        }
    }
}
