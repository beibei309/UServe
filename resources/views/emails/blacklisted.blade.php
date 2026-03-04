<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #7f1d1d; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #991b1b; margin: 0; font-size: 24px; }
        .content { color: #333333; line-height: 1.6; }
        .notice-box { background-color: #fef2f2; border-left: 4px solid #991b1b; padding: 15px; margin: 20px 0; color: #7f1d1d; }
        .footer { margin-top: 30px; font-size: 12px; color: #666666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Blacklisted</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user->hu_name }},</p>
            <p>Your account on S2U has been permanently blacklisted.</p>
            <div class="notice-box">
                <strong>Reason:</strong><br>
                {{ $reason }}
            </div>
            <p>If you believe this action is incorrect, please contact support.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
