<?php

namespace App\Services;

use App\Models\StudentService;
use InvalidArgumentException;

class ServicePackagePriceCalculator
{
    private const PACKAGE_KEYS = ['basic', 'standard', 'premium'];

    public function calculate(
        StudentService $service,
        string $packageKey,
        ?string $startTime,
        ?string $endTime,
    ): float {
        $packageKey = strtolower(trim($packageKey));
        if (! in_array($packageKey, self::PACKAGE_KEYS, true)) {
            throw new InvalidArgumentException('Invalid service package selected.');
        }

        $basePrice = (float) ($service->{"hss_{$packageKey}_price"} ?? 0);
        $frequency = strtolower(trim((string) ($service->{"hss_{$packageKey}_frequency"} ?? '')));

        if (! $service->hss_session_duration || ! $startTime || ! $endTime) {
            return round($basePrice, 2);
        }

        $bookedMinutes = $this->bookedMinutes($startTime, $endTime);
        if ($bookedMinutes <= 0) {
            return round($basePrice, 2);
        }

        if ($this->isHourlyBilling($frequency)) {
            return round($basePrice * ($bookedMinutes / 60), 2);
        }

        if ($this->isSlotBilling($frequency)) {
            $slotMinutes = max(1, (int) $service->hss_session_duration);

            return round($basePrice * ($bookedMinutes / $slotMinutes), 2);
        }

        return round($basePrice, 2);
    }

    private function bookedMinutes(string $startTime, string $endTime): int
    {
        $start = $this->timeToMinutes($startTime);
        $end = $this->timeToMinutes($endTime);

        return max(0, $end - $start);
    }

    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = array_pad(array_map('intval', explode(':', $time)), 2, 0);

        return ($hours * 60) + $minutes;
    }

    private function isHourlyBilling(string $frequency): bool
    {
        return preg_match('/\b(hourly|per\s*hours?|per\s*hrs?|\/\s*hours?|\/\s*hrs?)\b/i', $frequency) === 1;
    }

    private function isSlotBilling(string $frequency): bool
    {
        return preg_match('/\b(per\s*slots?|\/\s*slots?)\b/i', $frequency) === 1;
    }
}
