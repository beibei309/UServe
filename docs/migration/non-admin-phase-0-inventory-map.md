# Non-Admin UI/Logic Separation — Phase 0 Inventory Map

## Purpose
- Build the baseline before refactor.
- Define migration sequence with risk-first ordering.
- Lock refactor contract to preserve behavior parity.

## Preflight Contract
- Migration mode: refactor-only by default.
- UX rule: no visual/flow change unless explicitly approved.
- Slice rule: one feature slice per PR.
- Rollback rule: every PR includes rollback notes and file list.

## Baseline Behavior Coverage
| Area | Core Flow | Baseline Reference |
|---|---|---|
| Auth | login, register, password reset | Manual smoke + current screenshots |
| Onboarding | student and community verification | Manual smoke + upload/submit scenarios |
| Services Marketplace | list, details, apply/request | Manual smoke + request creation flow |
| Service Owner | create, edit, manage service | Manual smoke + CRUD flow |
| Service Requests | buyer and helper lifecycle | Manual smoke + status transition flow |
| Profile | edit profile and public profile | Manual smoke + file upload/delete flow |
| Notifications/Favorites | read and toggle interactions | Manual smoke + post-navigation checks |

## Inventory by Feature Area
| Feature Area | Primary View Files | Primary Routes | Primary Controllers |
|---|---|---|---|
| Auth | `resources/views/auth/*.blade.php` | `login`, `register`, `password.*`, `verification.*` | `AuthenticatedSessionController`, `RegisteredUserController`, `PasswordReset*` |
| Landing & Static | `welcome.blade.php`, `about.blade.php`, `help.blade.php`, `legal/*.blade.php` | `home`, `about`, `help`, `terms`, `privacy` | `HomeController`, `HelpController` |
| Students Community Entry | `students/create.blade.php`, `students/edit-profile.blade.php`, `students/index.blade.php` | `students.create`, `students.store`, `students.edit`, `students.update`, `students.index` | `StudentsController`, `ProfileController` |
| Onboarding Verification | `onboarding/students_verification.blade.php`, `onboarding/community_verification.blade.php` | `onboarding.students`, `onboarding.community.*`, `students_verification.*` | `VerificationController` |
| Services | `services/index.blade.php`, `services/details.blade.php`, `services/show.blade.php`, `services/apply.blade.php` | `services.index`, `services.details`, `services.apply`, `student-services.show` | `StudentServiceController`, `HomeController` |
| Service Owner Console | `services/create.blade.php`, `services/edit.blade.php`, `services/manage.blade.php` | `services.create`, `services.store`, `services.edit`, `services.update`, `services.destroy`, `services.manage` | `StudentServiceController` |
| Service Requests | `service-requests/index.blade.php`, `service-requests/helper.blade.php`, `service-requests/show.blade.php` | `service-requests.*`, `service-request.store` | `ServiceRequestController` |
| Profile & Public Profile | `profile/edit.blade.php`, `profile/show-public.blade.php`, `students/profile.blade.php` | `profile.*`, `profile.public`, `students.profile` | `ProfileController`, `StudentsController` |
| Notifications/Favorites | `notifications/index.blade.php`, `favorites/index.blade.php` | `notifications.*`, `favorites.*` | `NotificationController`, `FavoriteController` |

## Known Separation Hotspots
- High-coupling pages already identified:
  - `resources/views/services/details.blade.php`
  - `resources/views/services/edit.blade.php`
  - `resources/views/service-requests/helper.blade.php`
  - `resources/views/services/create.blade.php`
  - `resources/views/service-requests/index.blade.php`
  - `resources/views/service-requests/show.blade.php`
- Common smells observed:
  - inline handlers and inline scripts
  - Blade-side formatting and branch-heavy composition
  - mixed interaction logic in templates

## Exit Gate for Phase 0
- [ ] Inventory covers all user-facing non-admin routes/views.
- [ ] Every priority page has risk score in hotspot matrix.
- [ ] Every smell has destination in migration decision matrix.
- [ ] Anti-regression checklist exists per feature area.
