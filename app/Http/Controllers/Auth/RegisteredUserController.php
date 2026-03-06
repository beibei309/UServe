<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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

        $user = User::create([
            'hu_name' => $request->name,
            'hu_email' => $request->email,
            'hu_password' => Hash::make($request->password),
            'hu_role' => $request->role,
            'hu_phone' => $request->phone,
            'hu_student_id' => $request->student_id,
            'hu_verification_status' => 'pending',
            'hu_is_available' => $request->role === 'student' ? true : false,
        ]);

        if ($user->hu_role === 'student') {
            $semesterValue = $request->input('semester');
            $semesterValue = is_string($semesterValue) && trim($semesterValue) !== ''
                ? trim($semesterValue)
                : null;

            StudentStatus::create([
                'hss_student_id' => $user->hu_id,
                'hss_matric_no' => $user->hu_student_id,
                'hss_semester' => $semesterValue,
                'hss_status' => 'active',
                'hss_graduation_date' => null,
                'hss_effective_date' => now(),
            ]);
        }


        event(new Registered($user));

        if (method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
