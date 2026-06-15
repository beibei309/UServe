<?php

namespace App\Services;

use App\Models\StudentStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpsiStudentStatusService
{
    public function refreshForUser(User $user, bool $force = false): ?StudentStatus
    {
        if (! in_array($user->hu_role, ['student', 'helper'], true)) {
            return $user->studentStatus()->first();
        }

        if (! (bool) config('upsi.live_refresh.enabled', true)) {
            return $user->studentStatus()->first();
        }

        if (! $this->isSourceConnectionConfigured()) {
            return $user->studentStatus()->first();
        }

        $cacheKey = 'upsi:status-refresh:user:'.$user->hu_id;
        $throttleMinutes = (int) config('upsi.live_refresh.ttl_minutes', 15);
        $throttleMinutes = $throttleMinutes > 0 ? $throttleMinutes : 15;

        if (! $force && Cache::has($cacheKey)) {
            return $user->studentStatus()->first();
        }

        try {
            $sourceView = (string) config('upsi.student_view', 'home2u.h2u_student');
            $connectionName = (string) config('upsi.connection', config('database.default', 'pgsql'));
            $connection = DB::connection($connectionName);

            $source = $this->findSourceRow(
                $connection,
                $sourceView,
                $this->cleanString($user->hu_student_id),
                $this->normalizeEmail($user->hu_email)
            );

            Cache::put($cacheKey, true, now()->addMinutes($throttleMinutes));

            if (! $source) {
                return $user->studentStatus()->first();
            }

            $payload = $this->buildPayloadFromSource($source, $user);

            StudentStatus::query()->updateOrCreate(
                ['hss_student_id' => $user->hu_id],
                $payload
            );

            return $user->studentStatus()->first();
        } catch (\Throwable $exception) {
            Cache::put($cacheKey, true, now()->addMinutes($throttleMinutes));

            Log::warning('UPSI live refresh failed; keeping local student status.', [
                'user_id' => $user->hu_id,
                'error' => $exception->getMessage(),
            ]);

            return $user->studentStatus()->first();
        }
    }

    private function findSourceRow($connection, string $sourceView, ?string $studentId, ?string $email): ?object
    {
        if ($studentId) {
            $record = $connection->table($sourceView)
                ->where('sm_student_id', $studentId)
                ->first();

            if ($record) {
                return $record;
            }
        }

        if ($email) {
            return $connection->table($sourceView)
                ->whereRaw('LOWER(sm_siswa_email) = ?', [$email])
                ->first();
        }

        return null;
    }

    private function buildPayloadFromSource(object $source, User $user): array
    {
        $sourceStatus = $this->cleanString($source->ss_status_desc ?? null);
        $sourceSemester = $this->cleanString($source->sm_level ?? null);

        return [
            'hss_matric_no' => $this->cleanString($source->sm_student_id ?? null) ?? $user->hu_student_id,
            'hss_program' => $this->cleanString($source->sm_program ?? null),
            'hss_program_desc' => $this->cleanString($source->pm_program_desc ?? null),
            'hss_source_student_name' => $this->cleanString($source->sm_student_name ?? null),
            'hss_source_email' => $this->normalizeEmail($source->sm_siswa_email ?? null),
            'hss_source_status_desc' => $sourceStatus,
            'hss_semester' => $sourceSemester,
            'hss_status' => $this->mapStatus($sourceStatus),
            'hss_graduation_date' => $this->normalizeDate($source->sm_graduation_date ?? null),
            'hss_effective_date' => now()->toDateString(),
        ];
    }

    private function isSourceConnectionConfigured(): bool
    {
        $connectionName = (string) config('upsi.connection', config('database.default', 'pgsql'));
        $connection = (array) config("database.connections.{$connectionName}", []);

        return ! empty($connection['host'])
            && ! empty($connection['database'])
            && ! empty($connection['username']);
    }

    private function mapStatus(?string $statusDesc): string
    {
        $raw = trim((string) $statusDesc);
        if ($raw === '') {
            return 'Active';
        }

        $normalized = mb_strtolower($raw);

        $mapping = [
            'aktif' => 'Active',
            'dibenarkan meneruskan pengajian' => 'Active',
            'digantung kerana tatatertib' => 'Probation',
            'tangguh - pjj' => 'Deferred',
            'tangguh pengajian (tidak kira semester)' => 'Deferred',
            'tangguh pengajian (kira semester)' => 'Deferred',
            'tangguh pengajian-accident (tidak kira)' => 'Deferred',
            'tamat' => 'Graduated',
            'layak konvo' => 'Graduated',
            'disertasi disemak' => 'Graduated',
            'telah melengkapkan struktur pengajian' => 'Graduated',
        ];

        return $mapping[$normalized] ?? $raw;
    }

    private function normalizeEmail(mixed $value): ?string
    {
        $clean = $this->cleanString($value);
        return $clean ? strtolower($clean) : null;
    }

    private function cleanString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
