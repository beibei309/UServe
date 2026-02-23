<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #d97706; margin: 0; font-size: 24px; }
        .content { color: #333333; line-height: 1.6; }
        .warning-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; color: #92400e; }
        .footer { margin-top: 30px; font-size: 12px; color: #666666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Warning Issued</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>We are writing to inform you that your account has received a formal warning regarding a recent transaction or report.</p>
            
            <div class="warning-box">
                <strong>Reason for Warning:</strong><br>
                {{ $reason }}
            </div>

            <p>Please take this feedback seriously. Accumulating multiple warnings may lead to the suspension or permanent banning of your account.</p>
            
            <p>If you believe this is a mistake, please contact support immediately.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>