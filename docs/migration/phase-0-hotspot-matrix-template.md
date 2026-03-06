# Phase 0 Hotspot Matrix (Template)

Use this to rank non-admin pages by migration risk and decide execution order.

## Scoring Model
- Traffic (1-5): how often page is used.
- Coupling (1-5): how mixed UI/logic/JS currently is.
- Criticality (1-5): business impact if broken.
- Risk Score = Traffic × Coupling × Criticality.

## Matrix
| Area | Page/File | Route Name | Role(s) | Traffic (1-5) | Coupling (1-5) | Criticality (1-5) | Risk Score | Smells Found | Notes |
|---|---|---|---|---:|---:|---:|---:|---|---|
| Auth | resources/views/auth/login.blade.php | login | Guest |  |  |  |  | inline handlers / inline script / @php / formatting |  |
| Auth | resources/views/auth/register.blade.php | register | Guest |  |  |  |  |  |  |
| Services | resources/views/services/index.blade.php | services.index | Student/Community |  |  |  |  |  |  |
| Services | resources/views/services/show.blade.php | services.show | Student/Community |  |  |  |  |  |  |
| Requests | resources/views/... | ... | ... |  |  |  |  |  |  |

## Recommended Priority Queue
1. 
2. 
3. 

## Gate to Exit Phase 0
- [ ] All non-admin Blade pages inventoried.
- [ ] Every page has risk score.
- [ ] Priority queue approved.
- [ ] “Do not change UX” rule documented per slice.
