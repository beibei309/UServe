<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Do not apply web user status restrictions inside admin portal.
        if ($request->is('admin') || $request->is('admin/*') || $request->routeIs('admin.*')) {
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $wantsJson = $request->expectsJson() || $request->isJson();

            $emailVerificationRoutes = [
                'verification.notice',
                'verification.verify',
                'verification.send',
                'logout',
            ];

            if (empty($user->hu_email_verified_at) && !$request->routeIs($emailVerificationRoutes)) {
                if ($wantsJson) {
                    return response()->json([
                        'message' => 'Please verify your email first.',
                    ], 403);
                }

                return redirect()
                    ->route('verification.notice')
                    ->with('info', 'Please verify your email first before accessing this page.');
            }

            $studentOnboardingRoutes = [
                'onboarding.students',
                'students_verification.upload_selfie',
            ];

            $communityOnboardingRoutes = [
                'onboarding.community.verify',
                'onboarding.community.upload_selfie',
                'onboarding.community.submit_doc',
            ];

            if ($request->routeIs($studentOnboardingRoutes) && $user->hu_role !== 'student') {
                if ($wantsJson) {
                    return response()->json([
                        'message' => 'Only student accounts can access helper onboarding.',
                    ], 403);
                }

                if ($user->hu_role === 'community') {
                    return redirect()
                        ->route('onboarding.community.verify')
                        ->with('info', 'Community users must use community verification flow.');
                }

                return redirect()->route('dashboard');
            }

            if ($request->routeIs($communityOnboardingRoutes) && $user->hu_role !== 'community') {
                if ($wantsJson) {
                    return response()->json([
                        'message' => 'Only community accounts can access community onboarding.',
                    ], 403);
                }

                if ($user->hu_role === 'student') {
                    return redirect()
                        ->route('onboarding.students')
                        ->with('info', 'Student users must use helper onboarding flow.');
                }

                return redirect()->route('dashboard');
            }

            if ($user->hu_is_blocked || $user->hu_is_blacklisted || $user->hu_is_suspended) {
                // Allow viewing pages (GET/HEAD) so the restriction modal can appear immediately
                // on the page the user clicked. Still block any state-changing actions.
                if (!$request->routeIs('logout') && !in_array($request->method(), ['GET', 'HEAD'], true)) {
                    if ($wantsJson) {
                        return response()->json([
                            'message' => 'Your account is restricted. You cannot continue browsing.',
                        ], 403);
                    }

                    return redirect()
                        ->back()
                        ->with('error', 'Your account is restricted. You cannot continue browsing.');
                }
            }

            $sellerOnlyRoutes = [
                'students.index',
                'services.manage',
                'services.create',
                'services.store',
                'services.edit',
                'services.update',
                'services.destroy',
                'availability.toggle',
                'availability.updateSettings',
                'switch.mode',
            ];

            if ($request->routeIs($sellerOnlyRoutes)) {
                $isVerifiedHelper = $user->hu_role === 'helper' && !empty($user->hu_helper_verified_at);

                if (!$isVerifiedHelper) {
                    if ($wantsJson) {
                        return response()->json([
                            'message' => 'Only verified helpers can access seller pages.',
                        ], 403);
                    }

                    return redirect()
                        ->route('dashboard')
                        ->with('info', 'This page is for verified helpers only.');
                }
            }

            if ($user->hu_role === 'community' && $user->hu_verification_status !== 'approved') {
                $allowedActionRoutes = [
                    'onboarding.community.verify',
                    'onboarding.community.upload_photo',
                    'onboarding.community.upload_selfie',
                    'onboarding.community.submit_doc',
                    'verification.save_location',
                    'verification.notice',
                    'verification.verify',
                    'verification.send',
                    'logout',
                ];

                if (!empty($user->hu_email_verified_at)) {
                    if (!in_array($request->method(), ['GET', 'HEAD'], true) && !$request->routeIs($allowedActionRoutes)) {
                        if ($wantsJson) {
                            return response()->json([
                                'message' => 'Please complete community verification first.',
                            ], 403);
                        }

                        return redirect()
                            ->back()
                            ->with('info', 'Please complete community verification first.');
                    }
                }
            }
        }

        return $next($request);
    }
}
