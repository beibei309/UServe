<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceRequest;
use App\Models\User;

echo "=== Debugging ServiceRequest #11 Notification Issue ===" . PHP_EOL;

// Check if ServiceRequest 11 exists
$serviceRequest = ServiceRequest::find(11);

if (!$serviceRequest) {
    echo "❌ ServiceRequest 11 not found" . PHP_EOL;
    exit;
}

echo "✅ ServiceRequest 11 found" . PHP_EOL;
echo "   Requester ID: " . $serviceRequest->hsr_requester_id . PHP_EOL;
echo "   Provider ID: " . $serviceRequest->hsr_provider_id . PHP_EOL;
echo "   Status: " . $serviceRequest->hsr_status . PHP_EOL;

// Check requester
echo PHP_EOL . "=== Checking Requester ===" . PHP_EOL;
$requester = $serviceRequest->requester;

if (!$requester) {
    echo "❌ Requester is NULL" . PHP_EOL;
    
    // Check if user exists with that ID
    $userExists = User::find($serviceRequest->hsr_requester_id);
    if ($userExists) {
        echo "🔍 User with ID {$serviceRequest->hsr_requester_id} exists: {$userExists->hu_name}" . PHP_EOL;
    } else {
        echo "❌ No user found with ID {$serviceRequest->hsr_requester_id}" . PHP_EOL;
    }
} else {
    echo "✅ Requester found: " . $requester->hu_name . " (ID: " . $requester->hu_id . ")" . PHP_EOL;
    echo "   Email: " . $requester->hu_email . PHP_EOL;
    echo "   Role: " . $requester->hu_role . PHP_EOL;
}

// Check provider
echo PHP_EOL . "=== Checking Provider ===" . PHP_EOL;
$provider = $serviceRequest->provider;

if (!$provider) {
    echo "❌ Provider is NULL" . PHP_EOL;
} else {
    echo "✅ Provider found: " . $provider->hu_name . " (ID: " . $provider->hu_id . ")" . PHP_EOL;
}
