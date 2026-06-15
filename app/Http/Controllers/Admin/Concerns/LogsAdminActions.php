<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsAdminActions
{
    private function logAdminAction(string $action, array $subject = [], array $meta = []): void
    {
        $admin = Auth::guard('admin')->user();

        Log::info('Admin action performed', [
            'action' => $action,
            'admin_id' => $admin?->ha_id,
            'admin_email' => $admin?->ha_email,
            'subject' => $subject,
            'meta' => $meta,
        ]);
    }
}
