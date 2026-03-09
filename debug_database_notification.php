<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceRequest;
use App\Notifications\ServiceRequestStatusUpdated;

echo "=== Testing Database-Only Notification ===" . PHP_EOL;

$serviceRequest = ServiceRequest::find(11);
$requester = $serviceRequest->requester;

echo "About to send database notification to: {$requester->hu_name} (ID: {$requester->hu_id})" . PHP_EOL;

try {
    echo "Creating notification instance..." . PHP_EOL;
    
    // Create a custom notification class that only uses database channel
    $notification = new class($serviceRequest, 'accepted') extends ServiceRequestStatusUpdated {
        public function via(object $notifiable): array
        {
            return ['database']; // Only database, no mail
        }
    };
    
    echo "Sending notification..." . PHP_EOL;
    $requester->notify($notification);
    
    echo "✅ Database notification sent successfully!" . PHP_EOL;
    
    // Check if notification was created
    $count = $requester->notifications()->count();
    echo "Total notifications for user: {$count}" . PHP_EOL;
    
} catch (Exception $e) {
    echo "❌ Notification failed: " . $e->getMessage() . PHP_EOL;
    echo "Exception class: " . get_class($e) . PHP_EOL;
}