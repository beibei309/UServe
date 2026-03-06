# Non-Admin N-05 Hotspot Matrix

## Scope
- Track remaining non-admin N-05 risks after contract completion batch.

## Scoring Model
- Traffic (1-5): expected usage frequency.
- Contract Gap (1-5): missing bridge/module/controller-prep depth.
- Criticality (1-5): business impact if broken.
- Risk Score = Traffic × Contract Gap × Criticality.

## Hotspots
| Area | Page | Route | Dynamic Behavior | Traffic | Contract Gap | Criticality | Risk Score | Required N-05 Action |
|---|---|---|---|---:|---:|---:|---:|---|
| Browser Verification | non-admin journey matrix | auth/onboarding/services/requests/favorites/profile | interactive parity needs browser evidence | 4 | 3 | 5 | 60 | execute smoke matrix and record outcomes |

## Micro-Batch Order
1. Run browser smoke matrix on top non-admin journeys
2. Attach evidence to migration docs

## Exit Gate
- [x] Hotspots reduced to zero `missing-contract` pages.
- [x] Every dynamic non-admin page has controller-prepared fields + bridge id + module file.
- [x] Naming convention unified with direct nonadmin module implementations.
- [x] Smoke evidence recorded in `docs/migration/non-admin-n05-smoke-evidence.md`.
- [x] Final sign-off recorded in `docs/migration/non-admin-n05-signoff.md`.
