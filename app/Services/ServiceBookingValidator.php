<?php

namespace App\Services;

use App\Models\StudentService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ServiceBookingValidator
{
    public function __construct(private readonly ServicePackageDuration $packageDuration)
    {
    }

    public function validate(
        StudentService $service,
        string $packageKey,
        string $date,
        string $startTime,
        string $endTime,
    ): void {
        $schedule = $service->hss_operating_hours ?? [];
        $dayKey = strtolower(Carbon::parse($date)->format('D'));
        $day = $schedule[$dayKey] ?? null;

        if (! is_array($day) || ! filter_var($day['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
            $this->fail('selected_dates', 'The provider is not available on the selected day.');
        }

        if (in_array($date, $service->hss_unavailable_dates ?? [], true)) {
            $this->fail('selected_dates', 'The provider is unavailable on the selected date.');
        }

        $start = $this->timeToMinutes($startTime);
        $end = $this->timeToMinutes($endTime);
        $workingStart = $this->timeToMinutes((string) ($day['start'] ?? ''));
        $workingEnd = $this->timeToMinutes((string) ($day['end'] ?? ''));

        if ($start < $workingStart || $end > $workingEnd) {
            $this->fail('start_time', "Choose a time within the provider's working hours.");
        }

        $interval = max(1, (int) $service->hss_session_duration);
        if (($start - $workingStart) % $interval !== 0) {
            $this->fail('start_time', 'Choose one of the available start times.');
        }

        $bookedMinutes = $end - $start;
        if ($bookedMinutes <= 0 || $bookedMinutes % $interval !== 0) {
            $this->fail('end_time', 'The booking time must follow the available time interval.');
        }

        $frequency = strtolower(trim((string) $service->{"hss_{$packageKey}_frequency"}));
        if ($this->canAdjustDuration($frequency)) {
            return;
        }

        $packageMinutes = $this->packageDuration->toMinutes(
            $service->{"hss_{$packageKey}_duration"}
        );
        if ($packageMinutes === null) {
            return;
        }

        $requiredMinutes = (int) (ceil($packageMinutes / $interval) * $interval);
        if ($bookedMinutes !== $requiredMinutes) {
            $this->fail(
                'end_time',
                'The selected package requires a '.$this->formatDuration($requiredMinutes).' booking.'
            );
        }
    }

    private function canAdjustDuration(string $frequency): bool
    {
        return preg_match('/\b(hourly|per\s*hours?|per\s*hrs?|\/\s*hours?|\/\s*hrs?|per\s*slots?|\/\s*slots?)\b/i', $frequency) === 1;
    }

    private function timeToMinutes(string $time): int
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', $time, $parts)) {
            return 0;
        }

        return ((int) $parts[1] * 60) + (int) $parts[2];
    }

    private function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.'m';
        }

        $hours = $minutes / 60;

        return rtrim(rtrim(number_format($hours, 1), '0'), '.').'h';
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
