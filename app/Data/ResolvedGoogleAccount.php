<?php

namespace App\Data;

use Carbon\CarbonInterface;

class ResolvedGoogleAccount
{
    public function __construct(
        public readonly string $googleId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $role,
        public readonly ?string $studentId = null,
        public readonly ?string $staffEmail = null,
        public readonly ?CarbonInterface $staffVerifiedAt = null,
        public readonly ?CarbonInterface $publicVerifiedAt = null,
        public readonly string $verificationStatus = 'pending',
        public readonly ?object $studentSource = null,
    ) {
    }
}
