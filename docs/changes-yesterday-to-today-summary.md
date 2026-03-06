# Changes Summary (Yesterday → Today, Since Last Commit)

## Scope Note
- This summary is based on current uncommitted workspace changes compared to the latest commit.
- Includes controller logic, middleware behavior, Blade UX, mail templates, routes, and docs added during this moderation/safety/API-readiness workstream.

## 1) Admin Side Changes

### A. Moderation Control and Feedback Workflow
- **`app/Http/Controllers/Admin/AdminFeedbackController.php`**
  - Added role-aware feedback moderation for `student`, `community`, `helper`.
  - Added warning threshold logic via `config('moderation.user_warning_limit', 3)`.
  - Added:
    - `sendWarning()` with reason validation and warning increments.
    - `enforceRoleAction()` for final action after warning threshold.
    - `unblockUser()` for helper seller-access restoration.
    - `blockUser()` aliasing final enforcement path.
  - Mail linkage:
    - `AccountWarnedMail`
    - `AccountBannedMail`
    - `SellerBlockedMail`
    - `SellerUnblockedMail`

- **`resources/views/admin/feedback/index.blade.php`**
  - Updated moderation UI flow to align with warning threshold/final-action model.
  - Role filters and action clarity improved.

### B. Admin Request Dispute Resolution
- **`app/Http/Controllers/Admin/AdminRequestController.php`**
  - `resolveDispute()` now supports expanded outcomes:
    - `warn`
    - `suspend_or_blacklist` (plus backward compatibility `restrict|ban`)
    - `resume` (close dispute without penalty, continue waiting payment)
    - `complete_paid` (close dispute without penalty, force completed+paid)
    - `dismiss` (close dispute/cancelled)
  - Added safe fallback message to prevent undefined response variable paths.

- **`resources/views/admin/requests/index.blade.php`**
  - Resolve modal redesigned for usability:
    - constrained height and internal scroll
    - clearer “how it works”
    - neutral “Reported Statement” framing
    - action tooltips and impact preview before confirmation
    - no-penalty buttons:
      - resume
      - complete paid
      - dismiss
  - Updated action token from `restrict` to clearer `suspend_or_blacklist`.

### C. Admin Service Moderation
- **`app/Http/Controllers/Admin/AdminServicesController.php`**
  - Service warning flow unified to single mailable:
    - now sends `ServiceWarningMail($service, $reason)`
  - Keeps service warning limit from `config('moderation.service_warning_limit', 3)`.

- **`resources/views/admin/services/index.blade.php`**
  - Updated moderation UX hints/status displays to match warning threshold and suspension workflow.

### D. Admin Student / Community Governance
- **`app/Http/Controllers/Admin/AdminStudentController.php`**
  - Ban/unban flow aligned with mail sending.
  - Enforces “cannot unban graduated user” rule.

- **`app/Http/Controllers/Admin/AdminCommunityController.php`**
  - Blacklist/unblacklist flow refined and aligned with dedicated community mails.

- **`resources/views/admin/students/index.blade.php`**
- **`resources/views/admin/students/view.blade.php`**
- **`resources/views/admin/students/edit.blade.php`**
- **`resources/views/admin/students/export_pdf.blade.php`**
- **`resources/views/admin/community/index.blade.php`**
- **`resources/views/admin/community/view.blade.php`**
- **`resources/views/admin/community/edit.blade.php`**
  - UI refresh and status visibility improvements for moderation states.

### E. Admin Navigation/Layout
- **`resources/views/admin/layout.blade.php`**
  - Updated structure/navigation to reflect newer moderation modules.

### F. Admin Routing
- **`routes/web.php`**
  - Added/updated feedback moderation routes.
  - Confirms dispute resolve route.
  - Keeps community and student moderation route endpoints aligned.

## 2) Helper Side Changes

### A. Seller Restriction Behavior
- **`app/Http/Middleware/CheckUserStatus.php`**
  - Helper with `hu_is_blocked`:
    - can remain buyer
    - denied seller-only routes
    - forced out of seller mode where applicable
  - Hard-lock users (suspended/blacklisted) are forced logout/redirect with reason.

- **`app/Http/Controllers/DashboardController.php`**
  - `switchMode()` blocks helper from entering seller mode if blocked.

- **`resources/views/service-requests/index.blade.php`**
- **`resources/views/service-requests/show.blade.php`**
- **`resources/views/components/account-restriction-modal.blade.php`**
  - Buyer/seller UX and restriction messaging improved for blocked/suspended states.

### B. Seller Moderation Mail
- **`app/Mail/SellerBlockedMail.php`** (new)
- **`app/Mail/SellerUnblockedMail.php`** (new)
- **`resources/views/emails/seller_blocked.blade.php`** (new)
- **`resources/views/emails/seller_unblocked.blade.php`** (new)
  - Dedicated helper-specific moderation communication.

## 3) Student Side Changes

### A. Login and Restriction Clarity
- **`app/Http/Controllers/Auth/AuthenticatedSessionController.php`**
  - On login, hard-locked users are immediately logged out and shown reasoned status message.

- **`resources/views/auth/login.blade.php`**
- **`resources/views/components/verification-modal.blade.php`**
  - Better warning/blacklist/suspension messaging UX.

### B. Public Profile Safety Signals
- **`app/Http/Controllers/ProfileController.php`**
- **`app/Http/Controllers/StudentsController.php`**
  - Passes `reportCount` and latest dispute/report reason to public profile pages.

- **`resources/views/profile/show-public.blade.php`**
- **`resources/views/students/profile.blade.php`**
  - Displays report notice cards when applicable.

### C. Service Listing Restrictions
- **`app/Http/Controllers/StudentServiceController.php`**
  - Excludes restricted helpers from seller listings (suspended/blacklisted/blocked).

## 4) Community Side Changes

### A. Community Moderation State
- **`app/Http/Controllers/Admin/AdminCommunityController.php`**
  - Blacklist/unblacklist logic and reason handling reinforced.

- **`app/Mail/UserBlacklisted.php`**
- **`app/Mail/UserUnblacklisted.php`**
- **`resources/views/emails/blacklisted.blade.php`**
- **`resources/views/emails/unblacklisted.blade.php`**
  - Unified HTML-style communication and consistent moderation messaging.

## 5) Cross-Role Core Domain Changes

### A. User Model Status Semantics
- **`app/Models/User.php`**
  - Consolidated helpers for moderation state:
    - `isHardLocked()`
    - `isSellerRestricted()`
    - `moderationStatusKey()`
  - Supports consistent role-aware checks across controllers/middleware/views.

### B. Global Moderation Configuration
- **`config/moderation.php`** (new)
  - Added:
    - `user_warning_limit`
    - `service_warning_limit`

### C. Reporting/Admin Utilities
- **`app/Http/Controllers/Admin/ReportAdminController.php`**
- **`app/Http/Controllers/Admin/UserAdminController.php`**
  - Supporting adjustments for moderation status behavior in admin operations.

## 6) Mail System Standardization

### Mail class adjustments
- **Updated**
  - `app/Mail/GraduationReminderMail.php`
  - `app/Mail/ServiceSuspendedMail.php`
  - `app/Mail/UserBlacklisted.php`
  - `app/Mail/UserUnblacklisted.php`
- **Deleted**
  - `app/Mail/WarningMail.php`
- **Result**
  - Single canonical service warning path and unified HTML view-based rendering direction.

### Email templates updated
- `resources/views/emails/account_banned.blade.php`
- `resources/views/emails/account_unbanned.blade.php`
- `resources/views/emails/account_warned.blade.php`
- `resources/views/emails/blacklisted.blade.php`
- `resources/views/emails/graduation_reminder.blade.php`
- `resources/views/emails/service/suspended.blade.php`
- `resources/views/emails/service_approved.blade.php`
- `resources/views/emails/service_rejected.blade.php`
- `resources/views/emails/service_request.blade.php`
- `resources/views/emails/unblacklisted.blade.php`
- `resources/views/emails/service_warning.blade.php` (new)
- `resources/views/emails/seller_blocked.blade.php` (new)
- `resources/views/emails/seller_unblocked.blade.php` (new)
- `resources/views/emails/warning.blade.php` (deleted legacy duplicate)

## 7) Documentation Added/Updated
- **`docs/moderation-action-map.md`** (updated)
- **`docs/moderation-flexibility-recommendations.md`** (new)
- **`docs/project-demo-runbook.md`** (new)
- **`docs/ui-logic-separation-and-api-readiness-plan.md`** (new)
- **`docs/vice-chancellor-demo-guide.md`** (new)
- **`docs/changes-yesterday-to-today-summary.md`** (this file)

## 8) Functional Impact Overview by Role

### Admin
- Gains clearer, safer moderation controls with more dispute outcomes.
- Can execute role-aware final actions and no-penalty closures.
- Receives aligned UI, status messaging, and documentation runbooks.

### Helper
- Can be seller-blocked without full account hard-lock.
- Sees clearer enforcement boundaries between buyer and seller capabilities.
- Receives dedicated block/unblock mails.

### Student
- Restriction communication is clearer at login and moderation touchpoints.
- Public trust signals visible via report notice on profiles.
- Ban/unban flow is stricter with graduation-aware policy.

### Community
- Blacklist/unblacklist flows are explicit and role-specific.
- Community penalties map to dedicated email and status behavior.
