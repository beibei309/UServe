# Blade Separation Final Smoke Matrix

## Run Context
- Date: 2026-03-06
- Scope: auth, onboarding, services, requests, favorites, profile, admin
- Objective: final hardening evidence for full-project Blade separation gates

## Gate and Policy Matrix
| Area | Check | Source | Result |
|---|---|---|---|
| Non-admin views | Hard gate (`@php`, inline `<script>`, inline `on*`) | `scripts/nonadmin_regression_gate.ps1` | pass |
| Non-admin views | Forbidden data-access helpers (`auth()->`, `Auth::`) | full non-admin scan + gate | pass (`nonadmin_auth_hits=0`) |
| Non-admin views | Presentation helpers restricted to allowlist | `scripts/nonadmin_regression_gate.ps1`, `scripts/nonadmin_regression_gate.sh` | pass |
| Admin views | Hard gate (`@php`, inline `<script>`, inline `on*`) | `scripts/admin_regression_gate.ps1` | pass |
| Admin views | Forbidden data-access helpers (`auth()->`, `Auth::`) | admin gate | pass |
| Admin views | Presentation helpers restricted to allowlist | `scripts/admin_regression_gate.ps1`, `scripts/admin_regression_gate.sh` | pass |
| CI pipeline | Both non-admin and admin gates required | `.github/workflows/ci.yml` | pass (configured) |

## Smoke Coverage Matrix
| Area | Verification | Result |
|---|---|---|
| Auth | `RegistrationTest`, `AuthenticationTest`, `EmailVerificationTest` | pass |
| Onboarding | route group presence scan (`onboarding.*`) | pass (`onboarding=5`) |
| Services | route group presence scan (`services.*`) | pass (`services=19`) |
| Requests | route group presence scan (`service-requests.*`) | pass (`requests=16`) |
| Favorites | route group presence scan (`favorites.*`) | pass (`favorites=9`) |
| Profile | route presence + `ProfileTest --filter=displayed` | pass (`profile=1`, test pass) |
| Admin | route group presence scan (`admin.*`) + admin gate | pass (`admin=66`) |

## Test Snapshot
- Auth + access smoke: 13 passed (28 assertions)
- Profile displayed smoke: 1 passed (1 assertion)

## Final Status
- Non-admin audited standard is preserved.
- Full-project gate hardening is active through non-admin and admin parallel gate scripts.
- Merge policy can require both gate scripts to pass before integration.

## Ongoing Cadence
- Monthly allowlist review: first Monday of each month; owner is release-duty engineering maintainer.
- Release requirement: rerun this smoke matrix before every production release.
- Evidence policy: append each release run as a dated section in this file.
