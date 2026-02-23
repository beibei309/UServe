<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Warning Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        
        <div style="text-align: center; border-bottom: 2px solid #ff4444; padding-bottom: 20px; margin-bottom: 20px;">
            <h2 style="color: #cc0000; margin: 0;">⚠️ Official Service Warning</h2>
            <p style="color: #666; font-size: 14px; margin-top: 5px;">UPSI Connect (S2U) Notification</p>
        </div>

        <div style="color: #333; line-height: 1.6;">
            <p>Hi <strong>{{ $emailData['student_name'] }}</strong>,</p>

            <p>This is an official notification regarding your service listing: <br>
            <strong style="color: #0056b3;">{{ $emailData['service_name'] }}</strong></p>

            <p>Your service has received a warning from the administrator due to the following reason:</p>

            <div style="background-color: #fff3f3; border-left: 4px solid #cc0000; padding: 15px; margin: 20px 0; font-style: italic;">
                "{{ $emailData['reason'] }}"
            </div>

            <p><strong>Warning Count:</strong> <span style="color: #cc0000; font-weight: bold; font-size: 18px;">{{ $emailData['count'] }} / 3</span></p>

            <p style="font-size: 13px; color: #666;">
                Please take immediate action to rectify this issue. If you receive 3 warnings, your service will be automatically <strong>suspended</strong>.
            </p>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999;">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
            <p>Please do not reply to this automated email.</p>
        </div>

    </div>

</body>
</html>