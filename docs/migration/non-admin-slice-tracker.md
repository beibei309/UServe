# Non-Admin UI/Logic Separation — Slice Tracker

## Workflow (Mandatory)
1. Audit
2. Refactor
3. Validate
4. Smoke test
5. Merge

## PR Control Rules
- One feature slice per PR.
- Refactor-only unless UX change is explicitly approved.
- PR must include rollback notes.
- PR must include gate results with pass/fail.

## PR Template (Use for Every Slice)
### Scope
- Slice:
- Files changed:
- Routes affected:

### Logic Moves
- Blade logic moved to controller:
- Blade formatting moved to model/accessor:
- Inline behavior moved to JS module:

### Gate Results
- Inline handlers in slice: PASS/FAIL
- Inline script blocks in slice: PASS/FAIL
- `@php` in slice views: PASS/FAIL
- Blade formatting logic removed: PASS/FAIL
- Route closure regression: PASS/FAIL
- `php -l`: PASS/FAIL
- `node --check`: PASS/FAIL
- `php artisan view:cache`: PASS/FAIL
- Runtime smoke set: PASS/FAIL

### Rollback Note
- Safe rollback commit:
- Files to revert:
- Data impact:

## Slice Backlog and Status
| Slice ID | Area | Scope Files | Risk | Status | Owner | Notes |
|---|---|---|---:|---|---|---|
| N-01 | Service Requests | `service-requests/index`, `service-requests/helper`, `service-requests/show` | 125 | Planned | TBD | Start here |
| N-02 | Services Core | `services/details`, `services/edit`, `services/create`, `services/manage` | 125 | Planned | TBD | Highest marketplace coupling |
| N-03 | Onboarding | `onboarding/students_verification`, `onboarding/community_verification` | 45 | Planned | TBD | Compliance-sensitive |
| N-04 | Auth | `auth/login`, `auth/register`, password flows | 60 | Planned | TBD | Keep behavior parity strict |
| N-05 | Profile & Peripheral | `profile/*`, `students/profile`, `notifications`, `favorites` | 36 | Planned | TBD | Final cleanup |

## Hard Exit Criteria Per Slice
- [ ] Separation complete for the slice.
- [ ] Behavior parity confirmed against baseline.
- [ ] All static/build/runtime gates green.
- [ ] No new diagnostics.
- [ ] Rollback metadata documented.
