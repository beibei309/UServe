<!DOCTYPE html>
<html>
<head>
    <title>Service Rejected</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello {{ $service->user->name }},</h2>
    <p>We have reviewed your service listing: <strong>{{ $service->title }}</strong>.</p>
    <p>Unfortunately, it does not meet our current guidelines and has been <strong>rejected</strong>.</p>
    <p>Please review our terms or edit your service details before resubmitting.</p>
    <p>Regards,<br>The Admin Team</p>
</body>
</html>