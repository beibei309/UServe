<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View|JsonResponse
    {
        if ($request->expectsJson()) {
            $isVerified = $request->user()->hasVerifiedEmail();

            return response()->json([
                'verified' => $isVerified,
                'redirect_to' => route('dashboard', absolute: false),
            ]);
        }

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email', [
                        'verifyEmailUi' => [
                            'user_email' => $request->user()->hu_email,
                        ],
                    ]);
    }
}
