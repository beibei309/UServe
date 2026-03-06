# Non-Admin N-05 View-Model Contract Map

## Contract Fields
- `prepared_fields`: data prepared by controller/provider/mail layer.
- `config_bridge_id`: DOM bridge id consumed by page module.
- `module_file`: page module under `public/js`.
- `status`: `standardized`.

## Page Contract Map
| Area | View | Route | Controller/Provider | prepared_fields | config_bridge_id | module_file | status |
|---|---|---|---|---|---|---|---|
| Auth | `auth/login.blade.php` | `login` | `AuthenticatedSessionController` | `session_error`, `email_error` | `loginConfig` | `public/js/auth-login.js` | standardized |
| Auth | `auth/verify-email.blade.php` | `verification.notice` | `EmailVerificationPromptController` | `verification_link_sent` | `verifyEmailConfig` | `public/js/auth-verify-email.js` | standardized |
| Auth | `auth/register.blade.php` | `register` | `RegisteredUserController` | `initial_role`, `initial_community_type`, `is_student_selected`, `is_community_selected`, section visibility flags | `registerConfig` | `public/js/nonadmin-auth-register.js` | standardized |
| Onboarding | `onboarding/students_verification.blade.php` | `onboarding.students` | `VerificationController` | selfie/location/api endpoints, csrf | `studentsVerificationConfig` | `public/js/students-verification.js` | standardized |
| Onboarding | `onboarding/community_verification.blade.php` | `onboarding.community.verify` | `VerificationController` | upload/location endpoints, radius, csrf | `communityVerificationConfig` | `public/js/community-verification.js` | standardized |
| Students | `students/create.blade.php` | `students.create` | `StudentsController` | ready state, create URL | `studentsCreateConfig` | `public/js/students-create.js` | standardized |
| Students | `students/edit-profile.blade.php` | `students.edit` | `StudentsController` | image state, constraints | `studentsEditProfileConfig` | `public/js/students-edit-profile.js` | standardized |
| Students | `students/index.blade.php` | `students.index` | `StudentsController` | listing payload, action URLs | `studentsIndexConfig` | `public/js/students-index.js` | standardized |
| Services | `services/index.blade.php` | `services.index` | `StudentServiceController` | filters, payload, route links | `servicesIndexConfig` | `public/js/nonadmin-services-index.js` | standardized |
| Services | `services/details.blade.php` | `services.details` | `StudentServiceController` | image URL, whatsapp URL, operating days, booking payload | `servicesDetailsConfig` | `public/js/nonadmin-services-details.js` | standardized |
| Services | `services/apply.blade.php` | `services.apply` | `StudentServiceController` | apply payload, constraints | `servicesApplyConfig` | `public/js/nonadmin-services-apply.js` | standardized |
| Services | `services/create.blade.php` | `services.create` | `StudentServiceController` | submit URL, category payload, defaults | `servicesCreateConfig` | `public/js/nonadmin-services-create.js` | standardized |
| Services | `services/edit.blade.php` | `services.edit` | `StudentServiceController` | service payload, options, update URLs | `servicesEditConfig` | `public/js/nonadmin-services-edit.js` | standardized |
| Services | `services/manage.blade.php` | `services.manage` | `StudentServiceController` | item payload, action URLs | `servicesManageConfig` | `public/js/nonadmin-services-manage.js` | standardized |
| Requests | `service-requests/index.blade.php` | `service-requests.index` | `ServiceRequestController` | user mode payload, action URLs | `serviceRequestsIndexConfig` | `public/js/nonadmin-service-requests-index.js` | standardized |
| Requests | `service-requests/helper.blade.php` | `service-requests.index` (helper mode) | `ServiceRequestController` | helper payload, action URLs | `serviceRequestsHelperConfig` | `public/js/nonadmin-service-requests-helper.js` | standardized |
| Requests | `service-requests/show.blade.php` | `service-requests.show` | `ServiceRequestController` | request payload, action URLs | `serviceRequestsShowConfig` | `public/js/nonadmin-service-requests-show.js` | standardized |
| Favorites | `favorites/index.blade.php` | `favorites.index` | `FavoriteController` | toggle URL, csrf | `favoritesIndexConfig` | `public/js/favorites-index.js` | standardized |
| Profile | `profile/edit.blade.php` | `profile.edit` | `ProfileController` | `initial_tab`, status flags, profile avatar/initial, rating display fields, review card display payload | `profileEditConfig` | `public/js/nonadmin-profile-edit.js` | standardized |
| Dashboard | `dashboard.blade.php` | `dashboard` | `DashboardController` | `search_query`, `welcome_name`, `popular_searches`, service card payload, helper card payload | `dashboardConfig` | `public/js/nonadmin-dashboard.js` | standardized |
| Shared Layout | `layouts/app.blade.php`, `layouts/helper.blade.php` | layout | `AppServiceProvider` | `session_success`, `session_error` | `sessionAlertConfig` | `public/js/session-alerts.js` | standardized |
| Shared Component | `components/banned-modal.blade.php` | component include | `AppServiceProvider` | modal title/message/reason/logout token | `bannedModalConfig` | `public/js/banned-modal.js` | standardized |

## N-05 Priority Hotspots
1. Add browser smoke matrix evidence for auth/onboarding/services/requests/favorites/profile.
2. Keep non-admin gate clean as new pages/modules evolve.

## Canonical Naming Freeze
- Canonical non-admin module namespace is `public/js/nonadmin-*`.
- `public/js/services-*.js` and `public/js/service-requests-*.js` are retired and must not be reintroduced.
- Blade non-admin pages must reference canonical `nonadmin-*` modules only.

## Smoke Evidence
- See `docs/migration/non-admin-n05-smoke-evidence.md` for Batch 4 contract-closure verification evidence.
- Sign-off record: `docs/migration/non-admin-n05-signoff.md`.
