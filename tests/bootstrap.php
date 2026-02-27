<?php

$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/events.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/services.php',
];

foreach ($cacheFiles as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}

require __DIR__ . '/../vendor/autoload.php';
