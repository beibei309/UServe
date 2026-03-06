# Non-Admin N-05 Smoke Evidence

## Run Context
- Date: 2026-03-06
- Scope: auth, profile, dashboard, services, service-requests, favorites contract closure evidence

## Evidence Matrix
| Check | Command/Source | Result |
|---|---|---|
| Legacy Blade module includes removed | `Select-String` on `resources/views/**/*.blade.php` for `js/services-*.js` and `js/service-requests-*.js` | `legacy_blade_includes=0` |
| Canonical Blade module includes active | `Select-String` on `resources/views/**/*.blade.php` for `js/nonadmin-(services|service-requests)-*.js` | `canonical_blade_includes=9` |
| Canonical nonadmin JS syntax | `node --check` on 9 canonicalized services/request modules | pass |
| Non-admin regression gate | `./scripts/nonadmin_regression_gate.ps1` | pass |
| Auth + access smoke tests | `php artisan test` for `RegistrationTest`, `AuthenticationTest`, `EmailVerificationTest`, `AccessControlTest` | pass (13 tests, 28 assertions) |
| Profile route smoke test | `php artisan test tests/Feature/ProfileTest.php --filter=displayed` | pass (1 test) |
| Non-admin route presence | `routes/web.php`, `routes/auth.php` route-name scan | pass (`services.*`, `service-requests.*`, `favorites.*`, `dashboard`, `profile.edit`, `register`) |
| UI-only audited pages free from auth/formatting calls | regex scan on audited pages for `auth()->`, `Auth::`, `optional()`, `number_format()`, `round()`, `substr()`, `Str::limit()` | pass (0 matches in 5 audited pages) |
| UI-only audited pages enforcement | `scripts/nonadmin_regression_gate.ps1` + `scripts/nonadmin_regression_gate.sh` audit-file rule | pass |

## Browser Runtime Note
- `php artisan serve --host=127.0.0.1 --port=8082` failed in this execution environment with CLI Opcache memory region fatal error.
- Contract closure evidence is captured via static contract checks, syntax checks, route presence scan, regression gate, and feature smoke tests.

## Closure Status
- N-05 canonical naming is frozen at `public/js/nonadmin-*`.
- Legacy alias-era module files are retired.
- Contract map and hotspot matrix are aligned with standardized canonical module references.
- UI-only guardrail is active for audited non-admin hotspot pages.
- Full-project hardening matrix is recorded in `docs/migration/blade-separation-final-smoke-matrix.md`.
