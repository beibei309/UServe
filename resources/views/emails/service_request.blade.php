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
            <h1>New Service Request</h1>
        </div>
        <div class="content">
            <p>Hi {{ $serviceRequest->provider->hu_name }},</p>
            <p>You have received a new request for your service.</p>
            <div class="notice-box">
                <p><strong>Service:</strong> {{ $serviceRequest->studentService->hss_title }}</p>
                <p><strong>Customer:</strong> {{ $serviceRequest->requester->hu_name }}</p>
                <p><strong>Price Offered:</strong> RM{{ number_format($serviceRequest->hsr_offered_price, 2) }}</p>
                <p><strong>Message:</strong><br>{!! nl2br(e($serviceRequest->hsr_message)) !!}</p>
            </div>
            <a class="button" href="{{ route('dashboard') }}">Go to Dashboard</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UPSI Connect. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
