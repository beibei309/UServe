<?php

namespace App\Services;

class ServicePackageDisplay
{
    public function frequencyLabel(?string $frequency): string
    {
        $value = trim((string) $frequency);
        if ($value === '') {
            return 'One-time';
        }

        $value = preg_replace('/^(?:per\s+)+/i', '', $value) ?? $value;

        return 'Per '.ucwords(strtolower(trim($value)));
    }

    public function durationLabel(?string $duration): string
    {
        $value = trim((string) $duration);

        return $value !== '' ? $value : 'Time not specified';
    }

    public function summary(?string $duration, ?string $frequency): string
    {
        return $this->durationLabel($duration).' | '.$this->frequencyLabel($frequency);
    }
}
