# Moderation Action Map

## Scope
- Covers current moderation behavior after latest uncommitted changes.
- Focuses on warning, block, suspend, blacklist, and unblock flows.
- Maps each action to controller logic, status updates, and email dispatch.

## Global Rules
- User warning limit: `config('moderation.user_warning_limit', 3)`.
- Service warning limit: `config('moderation.service_warning_limit', 3)`.
- Hard lock means `hu_is_suspended` or `hu_is_blacklisted`.
- Seller restriction means hard lock or `hu_is_blocked`.

## User-Level Moderation (Admin)

### Admin Feedback Module
- Route group:
  - `POST /admin/feedback/{user}/warning`
  - `POST /admin/feedback/{user}/enforce`
  - `POST /admin/feedback/{user}/unblock`
- Controller: `App\Http\Controllers\Admin\AdminFeedbackController`.

#### Action: Warn user
- Method: `sendWarning(Request $request, User $user)`.
- Preconditions:
  - User role in `student|community|helper`.
  - `reason` is required.
  - User is not hard-locked and not blocked.
  - Warning count below configured limit.
- Data updates:
  - `hu_warning_count` incremented by 1.
- Mail sent:
  - `AccountWarnedMail($user, $reason)`.

#### Action: Enforce final role action
- Method: `enforceRoleAction(Request $request, User $user)`.
- Preconditions:
  - `reason` is required.
  - User warning count has reached limit.
- Role outcomes:
  - `helper`: sets `hu_is_blocked = true`, sets `hu_blacklist_reason`.
    - Mail: `SellerBlockedMail($user, $reason)`.
  - `student|community`: sets `hu_is_suspended = true`, `hu_is_blacklisted = false`, sets `hu_blacklist_reason`.
    - Mail: `AccountBannedMail($user, $reason)`.

#### Action: Unblock helper seller access
- Method: `unblockUser(User $user)`.
- Preconditions:
  - User role must be `helper`.
  - User currently blocked.
- Data updates:
  - `hu_is_blocked = false`.
- Mail sent:
  - `SellerUnblockedMail($user)`.

### Admin Request Dispute Module
- Controller: `App\Http\Controllers\Admin\AdminRequestController`.
- Method: `resolveDispute(Request $request, $id)`.

#### Action: Warn target user
- Trigger: `action_type = warn`.
- Data updates:
  - Target `hu_warning_count` incremented.
  - Request status moved to `waiting_payment`.
- Mail sent:
  - `AccountWarnedMail($user, $note)`.

#### Action: Suspend/Blacklist target user
- Trigger: `action_type = suspend_or_blacklist|restrict|ban`.
- Community target:
  - Updates: `hu_is_blacklisted = 1`, `hu_is_suspended = 0`, sets reason.
  - Mail: `UserBlacklisted($user, $note)`.
- Student/helper target:
  - Updates: `hu_is_suspended = 1`, `hu_is_blacklisted = 0`, sets reason.
  - Mail: `AccountBannedMail($user, $note)`.
- Request status moved to `cancelled`.

#### Action: Close dispute without penalty (resume)
- Trigger: `action_type = resume`.
- Data updates:
  - Request status moved to `waiting_payment`.
- Mail sent:
  - None.

#### Action: Close dispute without penalty (completed paid)
- Trigger: `action_type = complete_paid`.
- Data updates:
  - Request status moved to `completed`.
  - Payment status moved to `paid`.
- Mail sent:
  - None.

#### Action: Dismiss dispute without penalty
- Trigger: `action_type = dismiss`.
- Data updates:
  - Request status moved to `cancelled`.
- Mail sent:
  - None.

### Admin Student Module
- Controller: `App\Http\Controllers\Admin\AdminStudentController`.

#### Action: Ban student/helper
- Method: `ban(Request $request, $id)`.
- Data updates:
  - `hu_is_suspended = 1`, `hu_is_blacklisted = 0`, sets reason.
- Mail sent:
  - `AccountBannedMail($student, $reason)`.

#### Action: Unban student/helper
- Method: `unban($id)`.
- Data updates:
  - `hu_is_suspended = 0`, `hu_is_blacklisted = 0`, `hu_is_blocked = 0`, clears reason.
- Mail sent:
  - `AccountUnbannedMail($student)`.

### Admin Community Module
- Controller: `App\Http\Controllers\Admin\AdminCommunityController`.

#### Action: Blacklist community user
- Method: `blacklist(Request $request, $id)`.
- Data updates:
  - `hu_is_blacklisted = 1`, `hu_is_blocked = 0`, sets reason.
- Mail sent:
  - `UserBlacklisted($user, $reason)`.

#### Action: Unblacklist community user
- Method: `unblacklist($id)`.
- Data updates:
  - `hu_is_blacklisted = 0`, `hu_is_blocked = 0`, clears reason.
- Mail sent:
  - `UserUnblacklisted($user)`.

## Service-Level Moderation (Admin)

### Admin Services Module
- Controller: `App\Http\Controllers\Admin\AdminServicesController`.

#### Action: Approve service
- Method: `approve(StudentService $service)`.
- Data updates:
  - `hss_approval_status = approved`.
- Mail sent:
  - `ServiceApprovedMail($service)`.

#### Action: Reject service
- Method: `reject(Request $request, StudentService $service)`.
- Data updates:
  - `hss_approval_status = rejected`, stores reject reason in `hss_warning_reason`.
- Mail sent:
  - `ServiceRejectedMail($service)`.

#### Action: Warn service
- Method: `storeWarning(Request $request, $id)`.
- Preconditions:
  - `reason` is required.
  - Service warning count below configured limit.
- Data updates:
  - Increments `hss_warning_count`.
  - Stores `hss_warning_reason`.
- Mail sent:
  - `ServiceWarningMail($service, $reason)`.

#### Action: Suspend service
- Method: `suspend(StudentService $service)`.
- Data updates:
  - `hss_approval_status = suspended`.
- Mail sent:
  - `ServiceSuspendedMail($service)`.

#### Action: Unblock service
- Method: `unblock(StudentService $service)`.
- Data updates:
  - `hss_approval_status = approved`.
  - `hss_warning_count = 0`.
- Notification:
  - In-app notification only (`ServiceStatusNotification('unblocked', $service)`).
- Mail:
  - No email currently sent in this method.

## Access Enforcement Effects

### Login-time enforcement
- Controller: `Auth\AuthenticatedSessionController@store`.
- Behavior:
  - Hard-locked users are logged out immediately after auth and shown reasoned error.

### Per-request enforcement
- Middleware: `CheckUserStatus`.
- Behavior:
  - Hard-locked users are forced out to login with reason.
  - Blocked helpers are forced into buyer mode.
  - Seller-only routes denied for blocked helpers.
  - Non-verified helper denied seller routes.

## Dispute Modal UX Behavior (Admin Requests)
- Modal now frames dispute text as reporter claim, not automatic truth.
- Action labels and token are explicit:
  - `warn`
  - `suspend_or_blacklist`
  - `resume`
  - `complete_paid`
  - `dismiss`
- UI provides pre-submit impact preview for warning/suspension paths.
- Button tooltips explain action side-effects.

## Mail Design Standardization
- Current direction is unified HTML `view(...)` templates across moderation and service events.
- Legacy duplicate warning path was removed:
  - Removed class: `App\Mail\WarningMail`.
  - Removed view: `resources/views/emails/warning.blade.php`.
- Canonical service warning path:
  - `App\Mail\ServiceWarningMail` + `resources/views/emails/service_warning.blade.php`.

## Public Profile Risk Transparency

### Public profile pages
- `ProfileController@showPublic` and `StudentsController@profile` now pass:
  - `reportCount` from `hu_reports_count`.
  - `latestReportReason` from latest disputed request reason.
- Views show report notice card when count > 0:
  - `resources/views/profile/show-public.blade.php`.
  - `resources/views/students/profile.blade.php`.

## Graduation-Related Governance
- Admin can manually send graduation reminder email from student status page.
- Reminder route:
  - `POST /admin/student-status/remind/{id}`.
- Reminder mail:
  - `GraduationReminderMail`.
- Graduated users are blocked from unban/reactivation in admin student module.
- Helper verification rejects users who are too close to graduation (< 3 months remaining).

## Role-Based Summary

### Admin side
- Can issue warnings and enforce role-aware final actions.
- All major moderation actions dispatch matching emails.
- Service warning is now single-path and standardized.

### Helper side
- Can be seller-blocked without losing buyer access.
- Seller unblock now sends explicit reactivation email.
- Blocked helper is prevented from seller-only actions by middleware.

### Student side
- Can be suspended and reactivated with mail notifications.
- Public profile can display moderation report signals.

### Community side
- Can be blacklisted/unblacklisted with dedicated mails.
- Community restrictions are reflected in moderation status and enforced at login/middleware.
