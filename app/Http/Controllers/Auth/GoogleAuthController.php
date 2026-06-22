<?php

namespace App\Http\Controllers\Auth;

use App\Data\ResolvedGoogleAccount;
use App\Http\Controllers\Controller;
use App\Models\StudentStatus;
use App\Models\User;
use App\Services\GoogleAccountResolver;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, GoogleAccountResolver $resolver): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google authentication failed.', ['error' => $exception->getMessage()]);

            return redirect()->route('login')->with('error', 'Google sign in could not be completed. Please try again.');
        }

        $email = $this->normalizeEmail($googleUser->getEmail());
        if (! $email) {
            return redirect()->route('login')->with('error', 'Google did not provide an email address for this account.');
        }

        $googlePayload = [
            'id' => (string) $googleUser->getId(),
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $email,
            'email' => $email,
            'avatar' => $googleUser->getAvatar(),
        ];

        $existingUser = User::query()
            ->where('hu_google_id', $googlePayload['id'])
            ->orWhere('hu_email', $email)
            ->first();

        if ($existingUser) {
            $existingUser->forceFill([
                'hu_google_id' => $existingUser->hu_google_id ?: $googlePayload['id'],
                'hu_google_avatar' => $googlePayload['avatar'],
                'hu_auth_provider' => 'google',
                'hu_email_verified_at' => $existingUser->hu_email_verified_at ?: now(),
            ])->save();

            if (! $existingUser->hu_phone || ! $existingUser->hu_terms_accepted_at) {
                $request->session()->put('google_auth_user', $googlePayload);
                $request->session()->put('google_existing_user_id', $existingUser->hu_id);

                return redirect()->route('auth.google.complete');
            }

            Auth::login($existingUser, remember: true);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $resolver->resolve($googlePayload['id'], $googlePayload['name'], $email);
        } catch (Throwable $exception) {
            return redirect()->route('register')->withErrors([
                'registration' => $exception->getMessage(),
            ]);
        }

        $request->session()->put('google_auth_user', $googlePayload);
        $request->session()->forget('google_existing_user_id');

        return redirect()->route('auth.google.complete');
    }

    public function complete(Request $request): View|RedirectResponse
    {
        $googlePayload = $request->session()->get('google_auth_user');

        if (! is_array($googlePayload) || empty($googlePayload['email'])) {
            return redirect()->route('login')->with('error', 'Please start again with Google sign in.');
        }

        return view('auth.google-complete', [
            'googleUser' => $googlePayload,
        ]);
    }

    public function storeComplete(Request $request, GoogleAccountResolver $resolver): RedirectResponse
    {
        $googlePayload = $request->session()->get('google_auth_user');
        if (! is_array($googlePayload) || empty($googlePayload['id']) || empty($googlePayload['email'])) {
            return redirect()->route('login')->with('error', 'Please start again with Google sign in.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'terms' => ['accepted'],
        ]);

        $email = $this->normalizeEmail($googlePayload['email']);
        if (! $email) {
            throw ValidationException::withMessages([
                'email' => 'Google did not provide a valid email address.',
            ]);
        }

        $existingUserId = $request->session()->get('google_existing_user_id');

        try {
            $resolved = $resolver->resolve(
                (string) $googlePayload['id'],
                (string) ($googlePayload['name'] ?? $email),
                $email
            );

            $user = $existingUserId
                ? $this->completeExistingGoogleUser((int) $existingUserId, $resolved, $validated['phone'], $googlePayload)
                : $this->createGoogleUserWithFallback($resolved, $validated['phone'], $googlePayload);
        } catch (Throwable $exception) {
            Log::warning('Google account completion failed.', [
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);

            return back()->withInput($request->only('phone'))->withErrors([
                'google' => $exception->getMessage(),
            ]);
        }

        $request->session()->forget(['google_auth_user', 'google_existing_user_id']);
        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function completeExistingGoogleUser(int $userId, ResolvedGoogleAccount $resolved, string $phone, array $googlePayload): User
    {
        $user = User::query()->findOrFail($userId);

        $user->forceFill([
            'hu_phone' => $phone,
            'hu_google_id' => $user->hu_google_id ?: $resolved->googleId,
            'hu_google_avatar' => $googlePayload['avatar'] ?? null,
            'hu_auth_provider' => 'google',
            'hu_email_verified_at' => $user->hu_email_verified_at ?: now(),
            'hu_terms_accepted_at' => $user->hu_terms_accepted_at ?: now(),
        ])->save();

        return $user;
    }

    private function createGoogleUserWithFallback(ResolvedGoogleAccount $resolved, string $phone, array $googlePayload): User
    {
        try {
            return DB::transaction(function () use ($resolved, $phone, $googlePayload) {
                $user = User::create($this->googleUserPayload($resolved, $phone, $googlePayload));

                if ($resolved->role === 'student') {
                    $this->createStudentStatusWithFallback($user, $resolved);
                }

                return $user;
            });
        } catch (QueryException $exception) {
            if (! $this->isSequencePermissionError($exception, 'h2u_users_hu_id_seq')) {
                throw $exception;
            }

            return DB::transaction(function () use ($resolved, $phone, $googlePayload) {
                DB::statement('LOCK TABLE h2u_users IN EXCLUSIVE MODE');

                $now = now();
                $nextUserId = (int) DB::table('h2u_users')->max('hu_id') + 1;
                $payload = [
                    'hu_id' => $nextUserId,
                    ...$this->googleUserPayload($resolved, $phone, $googlePayload),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $columns = DB::getSchemaBuilder()->getColumnListing('h2u_users');
                DB::table('h2u_users')->insert(array_intersect_key($payload, array_flip($columns)));

                $user = User::query()->findOrFail($nextUserId);
                if ($resolved->role === 'student') {
                    $this->createStudentStatusWithFallback($user, $resolved);
                }

                return $user;
            });
        }
    }

    private function googleUserPayload(ResolvedGoogleAccount $resolved, string $phone, array $googlePayload): array
    {
        return [
            'hu_name' => $resolved->name,
            'hu_email' => $resolved->email,
            'hu_password' => Hash::make(Str::random(48)),
            'hu_google_id' => $resolved->googleId,
            'hu_google_avatar' => $googlePayload['avatar'] ?? null,
            'hu_auth_provider' => 'google',
            'hu_email_verified_at' => now(),
            'hu_terms_accepted_at' => now(),
            'hu_role' => $resolved->role,
            'hu_phone' => $phone,
            'hu_student_id' => $resolved->studentId,
            'hu_staff_email' => $resolved->staffEmail,
            'hu_staff_verified_at' => $resolved->staffVerifiedAt,
            'hu_public_verified_at' => $resolved->publicVerifiedAt,
            'hu_verification_status' => $resolved->verificationStatus,
            'hu_is_available' => $resolved->role === 'student',
            'hu_is_suspended' => false,
            'hu_is_blacklisted' => false,
            'hu_is_blocked' => false,
            'hu_warning_count' => 0,
            'hu_reports_count' => 0,
        ];
    }

    private function createStudentStatusWithFallback(User $user, ResolvedGoogleAccount $resolved): void
    {
        $payload = $this->studentStatusPayload($user, $resolved);

        try {
            StudentStatus::create([
                'hss_student_id' => $user->hu_id,
                ...$payload,
            ]);
        } catch (QueryException $exception) {
            if (! $this->isSequencePermissionError($exception, 'h2u_student_statuses_hss_id_seq')) {
                throw $exception;
            }

            DB::statement('LOCK TABLE h2u_student_statuses IN EXCLUSIVE MODE');

            $now = now();
            $nextStatusId = (int) DB::table('h2u_student_statuses')->max('hss_id') + 1;
            $insertPayload = [
                'hss_id' => $nextStatusId,
                'hss_student_id' => $user->hu_id,
                ...$payload,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $columns = DB::getSchemaBuilder()->getColumnListing('h2u_student_statuses');
            DB::table('h2u_student_statuses')->insert(array_intersect_key($insertPayload, array_flip($columns)));
        }
    }

    private function studentStatusPayload(User $user, ResolvedGoogleAccount $resolved): array
    {
        $source = $resolved->studentSource;
        $sourceStatus = $this->cleanString($source->ss_status_desc ?? null);

        return [
            'hss_matric_no' => $this->cleanString($source->sm_student_id ?? null) ?? $user->hu_student_id,
            'hss_program' => $this->cleanString($source->sm_program ?? null),
            'hss_program_desc' => $this->cleanString($source->pm_program_desc ?? null),
            'hss_source_student_name' => $this->cleanString($source->sm_student_name ?? null),
            'hss_source_email' => $this->normalizeEmail($source->sm_siswa_email ?? null),
            'hss_source_status_desc' => $sourceStatus,
            'hss_semester' => $this->cleanString($source->sm_level ?? null),
            'hss_status' => $this->mapUpsiStatusToInternal($sourceStatus),
            'hss_graduation_date' => $this->normalizeDate($source->sm_graduation_date ?? null),
            'hss_effective_date' => now()->toDateString(),
        ];
    }

    private function mapUpsiStatusToInternal(?string $statusDesc): string
    {
        $raw = $this->cleanString($statusDesc);
        if (! $raw) {
            return 'Active';
        }

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

        return $mapping[mb_strtolower($raw)] ?? 'Active';
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse((string) $value)->toDateString();
        } catch (Throwable) {
            return null;
        }
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

    private function isSequencePermissionError(QueryException $exception, ?string $sequenceName = null): bool
    {
        $message = strtolower((string) $exception->getMessage());

        if (! str_contains($message, 'permission denied for sequence')) {
            return false;
        }

        return $sequenceName === null || str_contains($message, strtolower($sequenceName));
    }
}
