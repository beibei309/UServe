<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $oldInput = (array) $request->session()->get('_old_input', []);
        $initialRole = $oldInput['role'] ?? 'student';
        $initialCommunityType = $oldInput['community_type'] ?? 'public';
        $registerUi = [
            'initial_role' => $initialRole,
            'initial_community_type' => $initialCommunityType,
            'is_student_selected' => $initialRole === 'student',
            'is_community_selected' => $initialRole === 'community',
            'show_student_section' => $initialRole === 'student',
            'show_community_section' => $initialRole === 'community',
        ];
        return view('auth.register', ['registerUi' => $registerUi]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:h2u_users,hu_email',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->role === 'student' && !str_ends_with($value, '@siswa.upsi.edu.my')) {
                        $fail('Student must use @siswa.upsi.edu.my email');
                    }
                    if ($request->role === 'community' && $request->community_type === 'staff') {
                        $pattern = '/^[a-zA-Z0-9._%+-]+@(upsi\.edu\.my|fsskj\.upsi\.edu\.my|fpm\.upsi\.edu\.my|fsmt\.upsi\.edu\.my|fskik\.upsi\.edu\.my|meta\.upsi\.edu\.my|fbk\.upsi\.edu\.my|fpe\.upsi\.edu\.my|fmsp\.upsi\.edu\.my|ftv\.upsi\.edu\.my|fsk\.upsi\.edu\.my|bendahari\.upsi\.edu\.my|ict\.upsi\.edu\.my)$/';
                        if (!preg_match($pattern, $value)) {
                            $fail('Staff must use a valid UPSI staff email (e.g., @upsi.edu.my or faculty subdomain).');
                        }
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,community'],
            'phone' => ['required', 'string', 'max:20'],
            'student_id' => ['required_if:role,student', 'nullable', 'string', 'max:20'],
            'community_type' => ['nullable', 'in:public,staff'],
        ]);

        try {
            $user = DB::transaction(function () use ($request) {
                $createdUser = User::create([
                    'hu_name' => $request->name,
                    'hu_email' => $request->email,
                    'hu_password' => Hash::make($request->password),
                    'hu_role' => $request->role,
                    'hu_phone' => $request->phone,
                    'hu_student_id' => $request->student_id,
                    'hu_verification_status' => 'pending',
                    'hu_is_available' => $request->role === 'student',
                    'hu_is_suspended' => false,
                    'hu_is_blacklisted' => false,
                    'hu_is_blocked' => false,
                    'hu_warning_count' => 0,
                    'hu_reports_count' => 0,
                ]);

                if ($createdUser->hu_role === 'student') {
                    $semesterValue = $request->input('semester');
                    $semesterValue = is_string($semesterValue) && trim($semesterValue) !== ''
                        ? trim($semesterValue)
                        : null;

                    StudentStatus::create([
                        'hss_student_id' => $createdUser->hu_id,
                        'hss_matric_no' => $createdUser->hu_student_id,
                        'hss_semester' => $semesterValue,
                        'hss_status' => 'active',
                        'hss_graduation_date' => null,
                        'hss_effective_date' => now(),
                    ]);
                }

                return $createdUser;
            });
        } catch (QueryException $exception) {
            Log::error('Registration failed due to database query exception.', [
                'email' => $request->email,
                'error' => $exception->getMessage(),
            ]);

            if ($this->isSequencePermissionError($exception, 'h2u_users_hu_id_seq')) {
                try {
                    $user = $this->createUserWithoutSequence($request);
                } catch (Throwable $fallbackException) {
                    Log::error('Registration fallback without sequence failed.', [
                        'email' => $request->email,
                        'error' => $fallbackException->getMessage(),
                    ]);

                    return back()
                        ->withInput($request->except(['password', 'password_confirmation']))
                        ->withErrors([
                            'registration' => 'Registration is temporarily unavailable due to a server database permission issue. Please contact support or try again later.',
                        ]);
                }
            } elseif ($this->isNotNullViolationError($exception)) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors([
                        'registration' => 'Registration failed due to server database schema constraints. Please contact support.',
                    ]);
            } else {
                throw $exception;
            }
        }


        event(new Registered($user));

        if (method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function createUserWithoutSequence(Request $request): User
    {
        return DB::transaction(function () use ($request) {
            DB::statement('LOCK TABLE h2u_users IN EXCLUSIVE MODE');

            $nextUserId = (int) DB::table('h2u_users')->max('hu_id') + 1;
            $now = now();

            DB::table('h2u_users')->insert([
                'hu_id' => $nextUserId,
                'hu_name' => $request->name,
                'hu_email' => $request->email,
                'hu_password' => Hash::make($request->password),
                'hu_role' => $request->role,
                'hu_phone' => $request->phone,
                'hu_student_id' => $request->student_id,
                'hu_verification_status' => 'pending',
                'hu_is_available' => $request->role === 'student',
                'hu_is_suspended' => false,
                'hu_is_blacklisted' => false,
                'hu_is_blocked' => false,
                'hu_warning_count' => 0,
                'hu_reports_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $createdUser = User::query()->findOrFail($nextUserId);

            if ($createdUser->hu_role === 'student') {
                $semesterValue = $request->input('semester');
                $semesterValue = is_string($semesterValue) && trim($semesterValue) !== ''
                    ? trim($semesterValue)
                    : null;

                $this->createStudentStatusWithFallback($createdUser, $semesterValue);
            }

            return $createdUser;
        });
    }

    private function createStudentStatusWithFallback(User $user, ?string $semesterValue): void
    {
        try {
            StudentStatus::create([
                'hss_student_id' => $user->hu_id,
                'hss_matric_no' => $user->hu_student_id,
                'hss_semester' => $semesterValue,
                'hss_status' => 'active',
                'hss_graduation_date' => null,
                'hss_effective_date' => now(),
            ]);
        } catch (QueryException $exception) {
            if (! $this->isSequencePermissionError($exception, 'h2u_student_statuses_hss_id_seq')) {
                throw $exception;
            }

            DB::statement('LOCK TABLE h2u_student_statuses IN EXCLUSIVE MODE');

            $nextStatusId = (int) DB::table('h2u_student_statuses')->max('hss_id') + 1;
            $now = now();

            DB::table('h2u_student_statuses')->insert([
                'hss_id' => $nextStatusId,
                'hss_student_id' => $user->hu_id,
                'hss_matric_no' => $user->hu_student_id,
                'hss_semester' => $semesterValue,
                'hss_status' => 'active',
                'hss_graduation_date' => null,
                'hss_effective_date' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function isSequencePermissionError(QueryException $exception, ?string $sequenceName = null): bool
    {
        $message = strtolower((string) $exception->getMessage());

        if (! str_contains($message, 'permission denied for sequence')) {
            return false;
        }

        if ($sequenceName === null) {
            return true;
        }

        return str_contains($message, strtolower($sequenceName));
    }

    private function isNotNullViolationError(QueryException $exception): bool
    {
        $message = strtolower((string) $exception->getMessage());

        return str_contains($message, 'sqlstate[23502]')
            || str_contains($message, 'not null violation')
            || str_contains($message, 'null value in column');
    }
}
