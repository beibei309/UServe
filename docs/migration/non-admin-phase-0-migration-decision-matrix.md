# Non-Admin Phase 0 Migration Decision Matrix

## Rules
- Blade: presentation only; light visibility `@if` is acceptable.
- Controller: request orchestration and prepared view data only.
- Model/Accessor: reusable derived labels/formatting.
- JS Modules: interactions, modal states, delegated actions.
- Routes: endpoint handlers in controller actions only.
- Security: no `eval` or `new Function` in loaders/modules.

## Decision Matrix
| Slice | File | Current Logic/Behavior | Keep in Blade? | Move To | Target Symbol/Module | Rationale | Owner | Done |
|---|---|---|---|---|---|---|---|---|
| Requests Buyer | `service-requests/index.blade.php` | action handlers + state/format composition | No | controller + js module | `ServiceRequestController::prepareIndexViewData`, `public/js/nonadmin-requests-index.js` | separate workflow interaction from render | TBD | [ ] |
| Requests Helper | `service-requests/helper.blade.php` | helper workflow handlers + modal state | No | controller + js module | `ServiceRequestController::prepareHelperViewData`, `public/js/nonadmin-requests-helper.js` | deterministic lifecycle behavior | TBD | [ ] |
| Request Detail | `service-requests/show.blade.php` | branch-heavy status and date formatting | No | controller + accessor + js module | `prepareShowViewData`, request status/date accessors, `nonadmin-requests-show.js` | eliminate Blade business formatting | TBD | [ ] |
| Service Detail | `services/details.blade.php` | script-heavy interactions + `@php` data shaping | No | controller + js module | `StudentServiceController::prepareDetailsViewData`, `nonadmin-services-details.js` | highest coupling and traffic | TBD | [ ] |
| Service Create | `services/create.blade.php` | inline form behavior and computed display states | No | request validator + js module | `StoreStudentServiceRequest`, `nonadmin-services-create.js` | keep validation and behavior out of Blade | TBD | [ ] |
| Service Edit | `services/edit.blade.php` | inline behavior + mutation state in template | No | controller + js module | `prepareEditViewData`, `nonadmin-services-edit.js` | parity-safe edit flow | TBD | [ ] |
| Service Manage | `services/manage.blade.php` | inline actions and formatting decisions | Partial | controller + js module | `prepareManageViewData`, `nonadmin-services-manage.js` | list rendering only in Blade | TBD | [ ] |
| Auth Login | `auth/login.blade.php` | toggle/show-password behavior in page script | No | js module | `public/js/nonadmin-auth-login.js` | behavior-only module | TBD | [ ] |
| Auth Register | `auth/register.blade.php` | inline form state and interactions | No | js module + request rules | `nonadmin-auth-register.js`, FormRequest rules | reduce template coupling | TBD | [ ] |
| Onboarding Student | `onboarding/students_verification.blade.php` | upload/selfie flow logic in view | No | controller + js module | `prepareStudentOnboardingViewData`, `nonadmin-onboarding-student.js` | workflow stability | TBD | [ ] |
| Onboarding Community | `onboarding/community_verification.blade.php` | doc upload/submit behavior in template | No | controller + js module | `prepareCommunityOnboardingViewData`, `nonadmin-onboarding-community.js` | compliance-critical flow | TBD | [ ] |
| Profile Public | `profile/show-public.blade.php` | conditional formatting and fallback logic | No | controller + accessor | `ProfileController::preparePublicViewData` | clean public render | TBD | [ ] |

## Lifecycle and Delegation Convention
- Module lifecycle must implement:
  - `init()`
  - `destroy()`
  - `reinit()`
- Event handling convention:
  - `data-action="..."`
  - delegated listener on stable root container
  - no direct one-off node-bound handlers for dynamic content

## Hard Validation Gates for Each Slice
- Inline event attributes in target views = 0.
- Inline `<script>` in target views = 0 (except approved layout includes).
- `@php` in target views = 0.
- Blade-side date/business formatting = 0.
- Endpoint route closures introduced = 0.
- `php -l`, `node --check`, `php artisan view:cache` all pass.
- Runtime smoke for navigation, modals, forms, role checks, post-AJAX interaction all pass.

## Exit Gate
- [ ] No row left without destination.
- [ ] No row left with undefined module/symbol.
- [ ] Owner assigned for each row.
- [ ] Slice-level DoD checklist attached per PR.
