<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\DatabaseNotification;
use App\Notifications\ServiceRequestStatusUpdated;

echo "=== Testing Notification System ===" . PHP_EOL;

$serviceRequest = ServiceRequest::find(11);
$requester = $serviceRequest->requester;

echo "Requester: {$requester->hu_name} (ID: {$requester->hu_id})" . PHP_EOL;
echo "Requester class: " . get_class($requester) . PHP_EOL;
echo "Requester table: " . $requester->getTable() . PHP_EOL;
echo "Requester primary key: " . $requester->getKeyName() . PHP_EOL;

// Check the notifications relationship
echo PHP_EOL . "=== Testing Notifications Relationship ===" . PHP_EOL;
$notificationsQuery = $requester->notifications();
echo "Notifications query: " . $notificationsQuery->toSql() . PHP_EOL;
echo "Existing notifications count: " . $requester->notifications()->count() . PHP_EOL;

// Check the morph relationship settings
echo PHP_EOL . "=== Checking Morph Configuration ===" . PHP_EOL;
echo "User getMorphClass(): " . $requester->getMorphClass() . PHP_EOL;

// Check DatabaseNotification model
$notification = new DatabaseNotification();
echo "DatabaseNotification table: " . $notification->getTable() . PHP_EOL;
echo "DatabaseNotification primary key: " . $notification->getKeyName() . PHP_EOL;

// Test creating a notification manually
echo PHP_EOL . "=== Testing Direct Notification Creation ===" . PHP_EOL;
try {
    $notification = new DatabaseNotification();
    $notification->hn_id = \Illuminate\Support\Str::uuid();
    $notification->hn_type = ServiceRequestStatusUpdated::class;
    $notification->hn_notifiable_type = $requester->getMorphClass();
    $notification->hn_notifiable_id = $requester->getKey();
    $notification->hn_data = json_encode(['test' => 'data']);
    $notification->save();
    echo "✅ Manual notification created successfully" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Manual notification failed: " . $e->getMessage() . PHP_EOL;
}