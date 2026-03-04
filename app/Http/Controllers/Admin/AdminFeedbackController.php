<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountBannedMail;
use App\Mail\AccountWarnedMail;
use App\Mail\SellerBlockedMail;
use App\Mail\SellerUnblockedMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminFeedbackController extends Controller
{
    private const FEEDBACK_ROLES = ['student', 'community', 'helper'];

    private function userWarningLimit(): int
    {
        return (int) config('moderation.user_warning_limit', 3);
    }

    private function finalActionForRole(string $role): string
    {
        return $role === 'helper' ? 'block' : 'suspend';
    }

    private function finalActionLabelForRole(string $role): string
    {
        return $this->finalActionForRole($role) === 'block'
            ? 'BLOCK SELLER ACCESS'
            : 'SUSPEND ACCOUNT';
    }
    public function index(Request $request)
    {
        $query = User::whereIn('hu_role', self::FEEDBACK_ROLES)
            ->has('reviewsReceived')
            ->withAvg('reviewsReceived as reviews_received_avg_rating', 'hr_rating')
            ->withCount('reviewsReceived')
            ->orderByDesc('reviews_received_avg_rating');

        $selectedRole = $request->input('role');
        if (in_array($selectedRole, self::FEEDBACK_ROLES, true)) {
            $query->where('hu_role', $selectedRole);
        } else {
            $selectedRole = '';
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('hu_name', 'like', '%' . $search . '%')
                                    ->orWhere('hu_email', 'like', '%' . $search . '%');
            });
        }

        $usersWithReviews = $query->paginate(10)->appends($request->only('search', 'role'));

        $finalActions = collect(self::FEEDBACK_ROLES)->mapWithKeys(fn ($role) => [
            $role => $this->finalActionLabelForRole($role),
        ])->all();

        return view('admin.feedback.index', [
            'usersWithReviews' => $usersWithReviews,
            'userWarningLimit' => $this->userWarningLimit(),
            'selectedRole' => $selectedRole,
            'roleOptions' => self::FEEDBACK_ROLES,
            'finalActions' => $finalActions,
        ]);
    }

    public function sendWarning(Request $request, User $user)
    {
        if (!in_array($user->hu_role, self::FEEDBACK_ROLES, true)) {
            return back()->with('error', 'This user role is not supported in Feedback moderation.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $reason = trim((string) $request->input('reason'));

        if ($user->isHardLocked() || $user->hu_is_blocked) {
            return back()->with('error', 'User is already restricted.');
        }

        $limit = $this->userWarningLimit();

        if ($user->hu_warning_count >= $limit) {
            $label = $this->finalActionLabelForRole($user->hu_role);
            return back()->with('warning', "User already reached {$limit} warnings. Use {$label}.");
        }

        $user->increment('hu_warning_count');
        Mail::to($user->hu_email)->send(new AccountWarnedMail($user, $reason));

        if ($user->hu_warning_count >= $limit) {
            $label = $this->finalActionLabelForRole($user->hu_role);
            $message = "User has reached {$limit} warnings. {$label} is now available.";
        } else {
            $message = "Warning sent to {$user->hu_name}. Current warning count: {$user->hu_warning_count}.";
        }

        return back()->with('success', $message);
    }

    public function enforceRoleAction(Request $request, User $user)
    {
        if (!in_array($user->hu_role, self::FEEDBACK_ROLES, true)) {
            return back()->with('error', 'This user role is not supported in Feedback moderation.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $reason = trim((string) $request->input('reason'));

        if ($user->isHardLocked() || $user->hu_is_blocked) {
            return back()->with('warning', 'User is already restricted.');
        }

        $limit = $this->userWarningLimit();

        if ($user->hu_warning_count < $limit) {
            return back()->with('error', "User must receive {$limit} warnings before final action.");
        }

        if ($this->finalActionForRole($user->hu_role) === 'block') {
            $user->hu_is_blocked = true;
            $user->hu_blacklist_reason = $reason;
            $user->save();
            Mail::to($user->hu_email)->send(new SellerBlockedMail($user, $reason));

            return back()->with('success', "Helper {$user->hu_name} has been blocked from seller actions.");
        }

        $user->hu_is_suspended = true;
        $user->hu_is_blacklisted = false;
        $user->hu_blacklist_reason = $reason;
        $user->save();
        Mail::to($user->hu_email)->send(new AccountBannedMail($user, $reason));

        return back()->with('success', "User {$user->hu_name} has been suspended after reaching {$limit} warnings.");
    }

    public function unblockUser(User $user)
    {
        if ($user->hu_role !== 'helper') {
            return back()->with('error', 'Seller unblock is only applicable to helper accounts.');
        }

        if (!$user->hu_is_blocked) {
            return back()->with('info', 'Helper is already unblocked.');
        }

        $user->hu_is_blocked = false;
        $user->save();
        Mail::to($user->hu_email)->send(new SellerUnblockedMail($user));

        return back()->with('success', "Helper {$user->hu_name} has been unblocked for seller actions.");
    }

    public function blockUser(Request $request, User $user)
    {
        return $this->enforceRoleAction($request, $user);
    }
}
