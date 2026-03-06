# Non-Admin Phase 0 Hotspot Matrix

## Scoring Model
- Traffic (1-5): expected frequency of use.
- Coupling (1-5): amount of mixed UI/logic/behavior in Blade.
- Criticality (1-5): business impact if broken.
- Risk Score = Traffic × Coupling × Criticality.

## Hotspot Matrix
| Area | Page/File | Route Name | Role(s) | Traffic | Coupling | Criticality | Risk Score | Smells Found | Notes |
|---|---|---|---|---:|---:|---:|---:|---|---|
| Service Requests | `resources/views/service-requests/index.blade.php` | `service-requests.index` | Student | 5 | 5 | 5 | 125 | inline handlers, inline scripts, Blade formatting | Buyer request lifecycle |
| Service Requests | `resources/views/service-requests/helper.blade.php` | `service-requests.index` (helper mode) | Helper | 5 | 5 | 5 | 125 | inline handlers, heavy event wiring, Blade-side formatting | Seller workflow-critical |
| Services | `resources/views/services/details.blade.php` | `services.details` | Student/Community | 5 | 5 | 5 | 125 | inline scripts, `@php`, formatting logic | Marketplace decision page |
| Services | `resources/views/services/edit.blade.php` | `services.edit` | Helper | 4 | 5 | 5 | 100 | inline handlers, script-heavy interactions | Revenue-impacting editor |
| Services | `resources/views/services/create.blade.php` | `services.create` | Helper | 4 | 5 | 5 | 100 | inline handlers, script blocks, branch-heavy form | New listing path |
| Service Requests | `resources/views/service-requests/show.blade.php` | `service-requests.show` | Student/Helper | 4 | 4 | 5 | 80 | formatting and interaction logic | Shared request detail |
| Services | `resources/views/services/manage.blade.php` | `services.manage` | Helper | 4 | 4 | 5 | 80 | inline actions, mixed display logic | Management list |
| Auth | `resources/views/auth/login.blade.php` | `login` | Guest | 5 | 2 | 5 | 50 | script-driven behavior | Entry point, low coupling |
| Auth | `resources/views/auth/register.blade.php` | `register` | Guest | 4 | 3 | 5 | 60 | inline behavior states | Conversion-critical |
| Students | `resources/views/students/index.blade.php` | `students.index` | Auth users | 3 | 3 | 4 | 36 | Alpine/interaction in Blade | Medium impact |
| Onboarding | `resources/views/onboarding/community_verification.blade.php` | `onboarding.community.verify` | Community | 3 | 3 | 5 | 45 | upload/submit behavior in view | Compliance-critical |
| Onboarding | `resources/views/onboarding/students_verification.blade.php` | `onboarding.students` | Student/Helper | 3 | 3 | 5 | 45 | upload/selfie/location interactions | Compliance-critical |

## Priority Queue (Execution Order)
1. Service Requests Buyer + Helper (`index`, `helper`, `show`)
2. Services Marketplace Critical (`details`, `edit`, `create`, `manage`)
3. Onboarding Verification (`students_verification`, `community_verification`)
4. Auth (`login`, `register`, reset flows)
5. Profile/Notifications/Favorites and remaining medium-risk views

## Anti-Regression Checklist by Feature Area
| Area | Required Smoke Checks Before Refactor |
|---|---|
| Service Requests | status transitions, modal flows, report/dispute actions, refresh/navigation parity |
| Services | list/detail render parity, CTA actions, create/edit submit, media interactions |
| Onboarding | photo/selfie upload, document submission, validation errors, retry flow |
| Auth | password toggle, submit, validation, redirect and session behavior |
| Profile | update profile, file delete/upload, public profile rendering |

## Exit Gate
- [ ] All high and medium risk pages scored.
- [ ] Queue sorted by risk and coupling.
- [ ] Anti-regression checks listed for each queued area.
- [ ] Refactor-only contract confirmed for all slices.
