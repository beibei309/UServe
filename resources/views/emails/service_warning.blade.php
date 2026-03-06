<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #d97706; margin: 0; font-size: 24px; }
        .content { color: #333333; line-height: 1.6; }
        .notice-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; color: #92400e; }
        .footer { margin-top: 30px; font-size: 12px; color: #666666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Service Warning Issued</h1>
        </div>
        <div class="content">
            <p>Dear {{ $service->user->hu_name }},</p>
            <p>Your service has received an official warning:</p>
            <div class="notice-box">
                <p><strong>Service:</strong> {{ $service->hss_title }}</p>
                <p><strong>Reason:</strong> {{ $reason }}</p>
                <p><strong>Warning Count:</strong> {{ $service->hss_warning_count }} / {{ config('moderation.service_warning_limit', 3) }}</p>
            </div>
            <p>Please make the necessary corrections to avoid further action.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
