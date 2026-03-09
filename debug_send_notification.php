<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceRequest;
use App\Notifications\ServiceRequestStatusUpdated;

echo "=== Testing Notification Sending ===" . PHP_EOL;

$serviceRequest = ServiceRequest::find(11);
$requester = $serviceRequest->requester;

echo "About to send notification to: {$requester->hu_name} (ID: {$requester->hu_id})" . PHP_EOL;

try {
    echo "Creating notification instance..." . PHP_EOL;
    $notification = new ServiceRequestStatusUpdated($serviceRequest, 'accepted');
    
    echo "Sending notification..." . PHP_EOL;
    $requester->notify($notification);
    
    echo "✅ Notification sent successfully!" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Notification failed: " . $e->getMessage() . PHP_EOL;
    echo "Exception class: " . get_class($e) . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}