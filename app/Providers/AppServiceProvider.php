<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Model binding is handled directly in User model imports
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Verification Listener
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Verified::class,
            \App\Listeners\HandleUserVerification::class
        );

        View::composer('layouts.navbar', function ($view) {
            $authUser = Auth::user();
            $user = $authUser instanceof User ? $authUser : null;
            $isLoggedIn = $user !== null;
            $isHelper = $isLoggedIn && $user->hu_role === 'helper';
            $viewMode = session('view_mode', 'buyer');

            if ($isLoggedIn && ($user->hu_role === 'student' || ($user->hu_role === 'helper' && $user->hu_is_blocked))) {
                $viewMode = 'buyer';
            }

            $unreadNotificationCount = 0;
            if ($isLoggedIn) {
                try {
                    if (Schema::hasTable('h2u_notifications')) {
                        $unreadNotificationCount = $user->unreadNotifications()->count();
                    }
                } catch (\Throwable $e) {
                    $unreadNotificationCount = 0;
                }
            }

            $view->with([
                'user' => $user,
                'isLoggedIn' => $isLoggedIn,
                'isHelper' => $isHelper,
                'viewMode' => $viewMode,
                'unreadNotificationCount' => $unreadNotificationCount,
            ]);
        });

        View::composer('components.account-restriction-modal', function ($view) {
            $authUser = Auth::user();
            $user = $authUser instanceof User ? $authUser : null;
            $isRestricted = $user ? $user->isHardLocked() : false;
            $title = 'Account Restricted';
            $message = 'Your account cannot access the platform at this time.';
            $statusLabel = 'Restricted';
            $scope = 'Site access: Disabled';
            $reason = $user->hu_blacklist_reason ?? 'No specific reason provided.';

            if ($user && $user->hu_is_blacklisted) {
                $title = 'Account Blacklisted';
                $message = 'Your account has been permanently blacklisted. Please contact support for further assistance.';
                $statusLabel = 'Blacklisted';
                $scope = 'Site access: Disabled permanently';
            } elseif ($user && $user->hu_is_suspended) {
                $title = 'Account Suspended';
                $message = 'Your account has been suspended temporarily. Please contact support for details.';
                $statusLabel = 'Suspended';
                $scope = 'Site access: Disabled temporarily';
            }

            $view->with('accountRestrictionData', [
                'isRestricted' => $isRestricted,
                'title' => $title,
                'message' => $message,
                'statusLabel' => $statusLabel,
                'scope' => $scope,
                'reason' => $reason,
            ]);
        });

        View::composer('components.verification-modal', function ($view) {
            $authUser = Auth::user();
            $user = $authUser instanceof User ? $authUser : null;
            $isRestricted = $user ? $user->isHardLocked() : false;
            $isCommunityUnverified = $user
                ? $user->hu_role === 'community' && $user->hu_verification_status !== 'approved' && !$isRestricted && $user->hasVerifiedEmail()
                : false;
            $isOnCommunityOnboarding = request()->routeIs('onboarding.community.*');
            $isPending = $user ? $user->hu_verification_status === 'pending' : false;
            $hasFiles = $user ? !empty($user->hu_verification_document_path) && !empty($user->hu_selfie_media_path) : false;
            $reviewInProgress = $isPending && $hasFiles;
            $title = $reviewInProgress ? 'Verification in Progress' : 'Verification Required';
            $message = $reviewInProgress
                ? 'Your submitted document and selfie are currently being reviewed by the admin team.'
                : 'To keep the platform safe, please complete your community verification before continuing.';
            $reason = $user
                ? ($user->hu_verification_note ?: ($reviewInProgress ? 'Status: Pending admin review.' : 'Proof of residency and selfie are required.'))
                : '';

            $view->with('verificationModalData', [
                'isCommunityUnverified' => $isCommunityUnverified,
                'isOnCommunityOnboarding' => $isOnCommunityOnboarding,
                'reviewInProgress' => $reviewInProgress,
                'title' => $title,
                'message' => $message,
                'reason' => $reason,
            ]);
        });

        View::composer('components.banned-modal', function ($view) {
            $authUser = Auth::user();
            $user = $authUser instanceof User ? $authUser : null;
            $isBanned = false;
            $title = 'Account Suspended';
            $message = 'Your account has been suspended due to policy violations.';
            $reason = $user->hu_blacklist_reason ?? 'No specific reason provided.';

            if ($user && $user->hu_is_suspended) {
                $isBanned = true;
                $title = 'Account Suspended';
                $message = 'Your account has been suspended. You cannot access the platform.';
            } elseif ($user && $user->hu_is_blacklisted) {
                $isBanned = true;
                $title = 'Account Blacklisted';
                $message = 'Your account has been blacklisted. Access is restricted.';
            }

            $view->with('bannedModalData', [
                'isBanned' => $isBanned,
                'title' => $title,
                'message' => $message,
                'reason' => $reason,
            ]);
        });
    }
}
