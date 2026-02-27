# Zero-Legacy-Token Audit Report

Date: 2026-02-27  
Scope: `app/Http/Controllers/**/*.php`, `resources/views/**/*.blade.php`  
Mode: Informational only (no behavior changes)

## Audit Rules

1. Detect direct unprefixed model property usage on domain objects (e.g. `->name`, `->email`, `->status`, `->rating`, `->id`).
2. Detect direct unprefixed table calls in controllers (`DB::table('users')`, `DB::table('notifications')`, etc.).
3. Detect hardcoded legacy validation strings for unprefixed table/column forms (`unique:users,email`, `exists:users,id`, etc.).

## Result Summary

- Hard schema-level legacy hits (controllers/views): **0**
- Potential compatibility alias / UI-layer hits: **7**
- Legacy raw table access hits in controllers: **0**
- Legacy validation rule string hits in controllers: **0**

## Potential Compatibility Hits (Manual Review List)

These are not necessarily defects. They are compatibility aliases or non-schema request/UI identifiers.

### Controllers
- `service` computed display alias:
  - `app/Http/Controllers/StudentServiceController.php:583`  
    `$service->rating = ...` (computed value for view rendering)
- `user` temporary display aliases:
  - `app/Http/Controllers/Admin/AdminFeedbackController.php:42`  
    `$user->warning_count = ...`
  - `app/Http/Controllers/Admin/AdminFeedbackController.php:43`  
    `$user->is_blocked = ...`

### Views
- Service rating display alias (fed by computed controller field):
  - `resources/views/services/details.blade.php:144`  
    `{{ $service->rating ?? '0.0' }}`
  - `resources/views/services/details.blade.php:333`  
    `{{ number_format($service->rating, 1) }}`
- Notification route parameter alias (`id` compatibility attribute on notification model):
  - `resources/views/notifications/index.blade.php:93`
  - `resources/views/notifications/index.blade.php:102`
- Comment-only occurrences (no runtime impact):
  - `resources/views/admin/students/index.blade.php:148`
  - `resources/views/admin/community/view.blade.php:343`

## Notes

- Request input keys such as `name`, `email`, `password`, `status`, `role` were found in controllers, but these are expected HTTP payload keys and are not direct DB schema references.
- Timestamp accessors (`created_at`, `updated_at`) were found in views; these are framework-level model date accessors and not considered legacy schema leakage.

## Conclusion

For the requested scope (controllers + views), there are **no hard legacy schema tokens** requiring changes. Remaining hits are compatibility/display aliases or comments.

---

## Strict Mode Follow-up (Model Accessors) — 2026-02-27

Completed removal of unused model-level legacy accessor blocks in:

- `app/Models/Category.php`
- `app/Models/StudentStatus.php`
- `app/Models/Review.php`
- `app/Models/Admin.php`
- `app/Models/StudentService.php`

Validation after cleanup:

- `php artisan test` → **29 passed**
- `php artisan view:clear; php artisan view:cache` → success

Interim note: this stage was superseded by the final strict pass below.

## Final Strict Pass (Extended) — 2026-02-27

Additional removals completed:

- `app/Models/User.php` legacy attribute accessor block removed.
- `app/Models/ServiceRequest.php` legacy attribute accessor block removed.

Reference migrations completed:

- Updated user/service request references in routes, views, and auth/profile tests to prefixed fields.

Validation:

- `php artisan test` → **29 passed**
- `php artisan view:clear; php artisan view:cache` → success

Final remaining model-level legacy accessor:

- `app/Models/DatabaseNotification.php` accessors are intentionally retained because notification payload/type/read flags must bridge prefixed DB columns (`hn_*`) with framework notification expectations.
