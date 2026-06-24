<?php

namespace App\Services;

class ServicePackageDuration
{
    public function toMinutes(?string $duration): ?int
    {
        $value = trim((string) $duration);
        if ($value === '' || preg_match('/\b(days?|weeks?|months?)\b/i', $value)) {
            return null;
        }

        $minutes = 0.0;
        preg_match_all(
            '/(\d+(?:\.\d+)?)\s*(hours?|hrs?|hr|h|minutes?|mins?|min|m)\b/i',
            $value,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $amount = (float) $match[1];
            $unit = strtolower($match[2]);
            $minutes += str_starts_with($unit, 'h') ? $amount * 60 : $amount;
        }

        return $minutes > 0 ? (int) round($minutes) : null;
    }

    public function fitsAvailableDay(?string $duration, array $schedule): bool
    {
        $requiredMinutes = $this->toMinutes($duration);
        if ($requiredMinutes === null) {
            return false;
        }

        foreach ($schedule as $day) {
            if (! filter_var($day['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
                continue;
            }

            $availableMinutes = $this->timeToMinutes($day['end'] ?? '')
                - $this->timeToMinutes($day['start'] ?? '');

            if ($availableMinutes >= $requiredMinutes) {
                return true;
            }
        }

        return false;
    }

    private function timeToMinutes(string $time): int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', $time, $parts)) {
            return 0;
        }

        return ((int) $parts[1] * 60) + (int) $parts[2];
    }
}
