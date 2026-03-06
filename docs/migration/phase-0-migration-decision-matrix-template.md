# Phase 0 Migration Decision Matrix (Template)

Use this per feature slice to decide where each piece of logic belongs.

## Rules
- Blade: presentation only (light `@if` display conditions are okay).
- Controller: orchestration + prepared view data.
- Model/Accessor/Cast: reusable domain formatting and derived fields.
- JS Modules: behavior, modal state, interactions, event delegation.
- Routes: controller actions only for endpoint handling.

## Decision Matrix
| Slice | File | Current Logic/Behavior | Keep in Blade? | Move To | Target Symbol/Module | Rationale | Done |
|---|---|---|---|---|---|---|---|
| Auth/Login | resources/views/... | password toggle inline | No | JS module | public/js/nonadmin-login.js | behavior only | [ ] |
| Services/List | resources/views/... | date formatting | No | model accessor | `getXxxDisplayAttribute` | reusable formatting | [ ] |
| Profile | resources/views/... | path fallback logic | No | controller prep | `prepareProfileViewData()` | avoid Blade branching | [ ] |

## Mapping Legend
- Move To = `controller` | `model accessor` | `request validator` | `js module` | `route/controller action`.

## Exit Gate
- [ ] Every smell has an explicit destination.
- [ ] No “TBD” destinations remain.
- [ ] Owners assigned for each row.
