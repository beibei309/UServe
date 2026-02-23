<!DOCTYPE html>
<html>
<head>
    <title>New Service Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    
    <h2>Hi {{ $serviceRequest->provider->name }},</h2>

    <p>You have received a new service request!</p>

    <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Service:</strong> {{ $serviceRequest->studentService->title }}</p>
        <p><strong>Customer:</strong> {{ $serviceRequest->requester->name }}</p>
        <p><strong>Price Offered:</strong> RM{{ number_format($serviceRequest->offered_price, 2) }}</p>
        
        <hr style="border-top: 1px solid #ddd;">
        
        <p><strong>Message/Details:</strong><br>
        {!! nl2br(e($serviceRequest->message)) !!}</p>
    </div>

    <p>Please login to your dashboard to Accept or Reject this request.</p>

    <a href="{{ route('dashboard') }}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Go to Dashboard</a>

    <p style="margin-top: 30px; font-size: 12px; color: #888;">Thank you,<br>S2U Team</p>

</body>
</html>