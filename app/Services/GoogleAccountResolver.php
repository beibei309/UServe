<?php

namespace App\Services;

use App\Data\ResolvedGoogleAccount;
use App\Support\UpsiStaffEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleAccountResolver
{
    public function resolve(string $googleId, string $name, string $email): ResolvedGoogleAccount
    {
        $normalizedEmail = $this->normalizeEmail($email);

        if ($this->isStudentEmail($normalizedEmail)) {
            $source = $this->findUpsiSourceRecord($normalizedEmail);
            $studentId = $this->cleanString($source->sm_student_id ?? null)
                ?? $this->detectStudentIdFromEmail($normalizedEmail);

            if (! $studentId) {
                throw new RuntimeException('Student ID could not be verified from your UPSI student email. Please use manual registration or contact admin.');
            }

            return new ResolvedGoogleAccount(
                googleId: $googleId,
                name: $name,
                email: $normalizedEmail,
                role: 'student',
                studentId: $studentId,
                verificationStatus: 'pending',
                studentSource: $source,
            );
        }

        if (UpsiStaffEmail::isValid($normalizedEmail)) {
            return new ResolvedGoogleAccount(
                googleId: $googleId,
                name: $name,
                email: $normalizedEmail,
                role: 'community',
                staffEmail: $normalizedEmail,
                staffVerifiedAt: now(),
                publicVerifiedAt: now(),
                verificationStatus: 'approved',
            );
        }

        return new ResolvedGoogleAccount(
            googleId: $googleId,
            name: $name,
            email: $normalizedEmail,
            role: 'community',
            verificationStatus: 'pending',
        );
    }

    private function findUpsiSourceRecord(string $email): ?object
    {
        if (! $this->isUpsiSourceConfigured()) {
            return null;
        }

        try {
            $connectionName = (string) config('upsi.connection', config('database.default', 'pgsql'));
            $sourceView = (string) config('upsi.student_view', 'home2u.h2u_student');

            return DB::connection($connectionName)
                ->table($sourceView)
                ->whereRaw('LOWER(sm_siswa_email) = ?', [$email])
                ->first();
        } catch (\Throwable $exception) {
            Log::warning('UPSI source lookup failed during Google account resolution.', [
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function isUpsiSourceConfigured(): bool
    {
        $connectionName = (string) config('upsi.connection', config('database.default', 'pgsql'));
        $connection = (array) config("database.connections.{$connectionName}", []);

        return ! empty($connection['database']);
    }

    private function detectStudentIdFromEmail(string $email): ?string
    {
        $prefix = strtok($email, '@') ?: '';
        $normalized = strtoupper(trim($prefix));

        return preg_match('/^D\d{10}$/', $normalized) === 1 ? $normalized : null;
    }

    private function isStudentEmail(string $email): bool
    {
        return str_ends_with($email, '@siswa.upsi.edu.my');
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function cleanString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
