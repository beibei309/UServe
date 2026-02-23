<!DOCTYPE html>
<html>
<head>
    <title>Service Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Great News, {{ $service->user->name }}!</h2>
    
    <p>We are pleased to inform you that your service listing, <strong>{{ $service->title }}</strong>, has been approved by our admin team.</p>
    
    <p>It is now visible to all students on the platform.</p>
    
    <p>
        {{-- Check if you have a named route for 'services.show', otherwise remove the link --}}
        <a href="{{ url('/services/' . $service->id) }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            View Your Service
        </a>
    </p>
    
    <p>Good luck with your sales!<br>The S2U Team</p>
</body>
</html>