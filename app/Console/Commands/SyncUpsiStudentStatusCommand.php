<?php

namespace App\Console\Commands;

use App\Models\StudentStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUpsiStudentStatusCommand extends Command
{
    protected $signature = 'upsi:sync-student-status
        {--apply : Persist changes to local h2u_student_statuses (default is dry-run)}
        {--limit=0 : Limit number of source rows processed}
        {--connection= : Source database connection name (defaults to config upsi.connection)}';

    protected $description = 'Sync UPSI student status view into local student statuses (dry-run by default).';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $limit = max(0, (int) $this->option('limit'));
        $connectionName = (string) ($this->option('connection') ?: config('upsi.connection', config('database.default', 'pgsql')));
        $sourceView = (string) config('upsi.student_view', 'home2u.h2u_student');

        if (! $this->isSourceConnectionConfigured($connectionName)) {
            $this->error("UPSI source connection [{$connectionName}] is not fully configured.");
            return self::FAILURE;
        }

        $modeLabel = $apply ? 'APPLY' : 'DRY-RUN';
        $this->info("Starting UPSI status sync in {$modeLabel} mode...");
        $this->line("Source connection: {$connectionName}");
        $this->line("Source view: {$sourceView}");

        $stats = [
            'processed' => 0,
            'matched_by_student_id' => 0,
            'matched_by_email' => 0,
            'not_matched' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'invalid_rows' => 0,
        ];

        $query = DB::connection($connectionName)
            ->table($sourceView)
            ->select([
                'ss_status_desc',
                'sm_program',
                'pm_program_desc',
                'sm_student_id',
                'sm_student_name',
                'sm_level',
                'sm_siswa_email',
                'sm_graduation_date',
            ]);

        if ($limit > 0) {
            $query->limit($limit);
        }

        foreach ($query->cursor() as $row) {
            $stats['processed']++;

            $studentId = $this->cleanString($row->sm_student_id ?? null);
            $email = strtolower($this->cleanString($row->sm_siswa_email ?? null) ?? '');
            $semester = $this->cleanString($row->sm_level ?? null);
            $status = $this->mapStatus($this->cleanString($row->ss_status_desc ?? null));
            $graduationDate = $this->normalizeDate($row->sm_graduation_date ?? null);

            if (! $studentId && ! $email) {
                $stats['invalid_rows']++;
                continue;
            }

            $user = null;
            if ($studentId) {
                $user = User::query()->where('hu_student_id', $studentId)->first();
                if ($user) {
                    $stats['matched_by_student_id']++;
                }
            }

            if (! $user && $email !== '') {
                $user = User::query()->whereRaw('LOWER(hu_email) = ?', [$email])->first();
                if ($user) {
                    $stats['matched_by_email']++;
                }
            }

            if (! $user) {
                $stats['not_matched']++;
                continue;
            }

            $payload = [
                'hss_matric_no' => $studentId ?: $user->hu_student_id,
                'hss_semester' => $semester,
                'hss_status' => $status,
                'hss_graduation_date' => $graduationDate,
                'hss_effective_date' => now()->toDateString(),
            ];

            $existing = StudentStatus::query()->where('hss_student_id', $user->hu_id)->first();

            if (! $existing) {
                $stats['created']++;

                if ($apply) {
                    StudentStatus::query()->create(array_merge($payload, [
                        'hss_student_id' => $user->hu_id,
                    ]));
                }

                continue;
            }

            $hasChanges =
                (string) ($existing->hss_matric_no ?? '') !== (string) ($payload['hss_matric_no'] ?? '') ||
                (string) ($existing->hss_semester ?? '') !== (string) ($payload['hss_semester'] ?? '') ||
                (string) ($existing->hss_status ?? '') !== (string) ($payload['hss_status'] ?? '') ||
                (string) ($existing->hss_graduation_date ?? '') !== (string) ($payload['hss_graduation_date'] ?? '');

            if (! $hasChanges) {
                $stats['unchanged']++;
                continue;
            }

            $stats['updated']++;

            if ($apply) {
                $existing->fill($payload);
                $existing->save();
            }
        }

        $this->newLine();
        $this->info('Sync summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed rows', $stats['processed']],
                ['Matched by student ID', $stats['matched_by_student_id']],
                ['Matched by email fallback', $stats['matched_by_email']],
                ['Not matched locally', $stats['not_matched']],
                ['Invalid source rows', $stats['invalid_rows']],
                ['Would create / Created', $stats['created']],
                ['Would update / Updated', $stats['updated']],
                ['Unchanged', $stats['unchanged']],
            ]
        );

        if (! $apply) {
            $this->comment('Dry-run completed. No local data was written. Re-run with --apply to persist changes.');
        }

        return self::SUCCESS;
    }

    private function isSourceConnectionConfigured(string $connectionName): bool
    {
        $connection = (array) config("database.connections.{$connectionName}", []);

        return ! empty($connection['host'])
            && ! empty($connection['database'])
            && ! empty($connection['username'])
            && (! array_key_exists('password', $connection) || ! empty($connection['password']));
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
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
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
}
