<?php

use App\Models\StudentService;

require __DIR__.'/../vendor/autoload.php';

$services = StudentService::all(['hss_id', 'hss_title']);
foreach ($services as $service) {
    echo "ID: {$service->hss_id} | Title: {$service->hss_title}\n";
}
