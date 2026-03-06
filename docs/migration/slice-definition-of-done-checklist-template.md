# Slice Definition of Done Checklist (Template)

Apply this checklist to each vertical slice before merge.

## Slice Info
- Slice Name:
- Area:
- Owner:
- PR:
- Date:

## Separation Gates
- [ ] Inline event attributes in slice = 0 (`on...=`).
- [ ] Inline `<script>` blocks in slice = 0 (except approved global includes).
- [ ] `@php` blocks in target views = 0.
- [ ] Blade data/business formatting removed (dates/status/path mapping/etc.).
- [ ] Endpoint route closures not introduced.

## Behavior Parity Gates
- [ ] Primary page actions behave same as before.
- [ ] Modal open/close/actions work.
- [ ] Form submit/validation still works.
- [ ] Works after normal navigation.
- [ ] Works after AJAX-style navigation/reload.

## Validation Gates
- [ ] `php -l` pass for changed PHP files.
- [ ] `node --check` pass for changed JS files.
- [ ] `php artisan view:cache` pass.
- [ ] IDE diagnostics: no new errors from this slice.

## Smoke Matrix (Pass/Fail)
| Scenario | Expected | Result |
|---|---|---|
| Navigate to page | Page renders correctly |  |
| Click main CTA | Correct action triggered |  |
| Open modal | Modal displays and closes |  |
| Submit form | Success/validation works |  |
| Back/forward nav | Handlers still active |  |

## Rollback Notes
- Safe rollback points:
- Files to revert if needed:
- Data migration impact (if any):

## Sign-off
- [ ] Owner sign-off
- [ ] Reviewer sign-off
- [ ] Ready to merge
