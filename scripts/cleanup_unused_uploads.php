<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$execute = in_array('--execute', $argv, true);

$pairs = [
    ['users', 'profile_photo_path'],
    ['users', 'work_experience_file'],
    ['users', 'verification_document_path'],
    ['users', 'selfie_media_path'],
    ['service_requests', 'payment_proof'],
    ['student_services', 'image_path'],
    ['categories', 'image_path'],
];

$used = [];
foreach ($pairs as $pair) {
    $table = $pair[0];
    $column = $pair[1];

    if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
        continue;
    }

    $values = DB::table($table)->whereNotNull($column)->pluck($column);
    foreach ($values as $value) {
        if (!is_string($value)) {
            continue;
        }

        $normalized = ltrim(str_replace('\\', '/', trim($value)), '/');
        if ($normalized !== '') {
            $used[$normalized] = true;
        }
    }
}

$targets = [
    ['label' => 'public/profile-photos', 'root' => public_path('profile-photos'), 'prefix' => 'profile-photos/'],
    ['label' => 'storage/app/public/payment_proofs', 'root' => storage_path('app/public/payment_proofs'), 'prefix' => 'payment_proofs/'],
    ['label' => 'storage/app/public/uploads/work_experience', 'root' => storage_path('app/public/uploads/work_experience'), 'prefix' => 'uploads/work_experience/'],
    ['label' => 'storage/app/private/verification_docs', 'root' => storage_path('app/private/verification_docs'), 'prefix' => 'verification_docs/'],
    ['label' => 'storage/app/private/uploads/verification', 'root' => storage_path('app/private/uploads/verification'), 'prefix' => 'uploads/verification/'],
    ['label' => 'public/storage/services', 'root' => public_path('storage/services'), 'prefix' => 'storage/services/'],
    ['label' => 'storage/app/public/services', 'root' => storage_path('app/public/services'), 'prefix' => 'services/'],
    ['label' => 'storage/app/public/uploads/profile', 'root' => storage_path('app/public/uploads/profile'), 'prefix' => 'uploads/profile/'],
    ['label' => 'storage/app/public/categories', 'root' => storage_path('app/public/categories'), 'prefix' => 'categories/'],
];

$totalFiles = 0;
$totalUnused = 0;
$totalDeleted = 0;

foreach ($targets as $target) {
    $root = $target['root'];
    $prefix = $target['prefix'];
    $label = $target['label'];

    $count = 0;
    $unused = 0;
    $deleted = 0;

    if (!is_dir($root)) {
        echo "{$label}: missing\n";
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }

        $count++;
        $relative = str_replace('\\', '/', substr($fileInfo->getPathname(), strlen($root) + 1));
        $key = ltrim($prefix . $relative, '/');

        if (!isset($used[$key])) {
            $unused++;
            if ($execute) {
                @unlink($fileInfo->getPathname());
                if (!file_exists($fileInfo->getPathname())) {
                    $deleted++;
                }
            }
        }
    }

    $totalFiles += $count;
    $totalUnused += $unused;
    $totalDeleted += $deleted;

    if ($execute) {
        echo "{$label}: total={$count}, unused={$unused}, deleted={$deleted}\n";
    } else {
        echo "{$label}: total={$count}, unused={$unused}\n";
    }
}

echo "Used DB paths: " . count($used) . "\n";
if ($execute) {
    echo "Summary: total_files={$totalFiles}, total_unused={$totalUnused}, total_deleted={$totalDeleted}\n";
} else {
    echo "Summary: total_files={$totalFiles}, total_unused={$totalUnused}\n";
    echo "Dry run complete. Re-run with --execute to delete.\n";
}
