<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #4338ca; margin: 0; font-size: 24px; }
        .content { color: #333333; line-height: 1.6; }
        .notice-box { background-color: #eef2ff; border-left: 4px solid #4f46e5; padding: 15px; margin: 20px 0; color: #3730a3; }
        .button { display: inline-block; background-color: #4F46E5; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 6px; margin-top: 8px; }
        .footer { margin-top: 30px; font-size: 12px; color: #666666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Graduation Reminder</h1>
        </div>
        <div class="content">
            <p>Hello {{ $user->hu_name }},</p>
            <div class="notice-box">
                Your graduation date is approaching on <strong>{{ \Carbon\Carbon::parse($user->studentStatus->graduation_date)->format('d M Y') }}</strong>.
            </div>
            <p>Please wrap up active services on the platform to ensure a smooth transition for your clients.</p>
            <a class="button" href="{{ route('dashboard') }}">Manage My Services</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
