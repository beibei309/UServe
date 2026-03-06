# UI-Logic Separation and API Readiness Plan

## Why This Matters Now
- External integration with Muallim app requires stable business logic and predictable APIs.
- Current Laravel app is functional, but some business/data logic still exists in Blade views.
- Separating UI from logic improves maintainability, testing, API reuse, and integration speed.

## Is It Possible
- Yes, fully possible in Laravel MVC.
- It is not a rewrite; it is an incremental refactor.
- Risk is manageable if done module-by-module with tests.

## Current Situation (Summary)
- Good: most core rules already live in controllers, models, and middleware.
- Gap: some view files still contain:
  - model/query calls
  - heavy `@php` data shaping
  - large inline workflow JS

## Target Architecture
- **Controller**: request validation, orchestration only.
- **Service Layer**: business rules and state transitions.
- **Model/Policies**: domain helpers, permissions, statuses.
- **API Resources/DTOs**: response shape for web/API consistency.
- **Blade/UI**: rendering only, minimal conditional display logic.
- **Frontend JS Modules**: modal/workflow logic extracted from Blade.

## Refactor Priorities

### Priority 0 (Immediate)
- Remove direct model/query usage from Blade.
- Move DB lookups into controller/service and pass prepared data to views.

### Priority 1 (High)
- Move heavy `@php` blocks in views to:
  - presenters/view-model helpers
  - model accessors
  - service output mappers
- Extract large inline JS in Blade to dedicated JS files.

### Priority 2 (Stabilization)
- Add API Resources for key entities:
  - User/Profile
  - Service
  - ServiceRequest
  - Moderation status snapshot
- Centralize status labels and transition mapping.

## API Readiness Track (For Muallim Integration)

### Scope Decision (Updated)
- External app integration does **not** need full admin APIs.
- Current agreed scope is only data needed by:
  - `services/index.blade.php`
  - `services/show.blade.php` (details page)
- Therefore, API work is narrowed to service listing/detail payloads only and can be scheduled after admin UI/logic cleanup.

### Phase A: Contract-First
- Define endpoint contract document:
  - path, method, auth type
  - request/response JSON schema
  - error codes
- Freeze field naming conventions.

### Phase B: Authentication Strategy
- Current app uses session auth.
- For external app integration, introduce token-based auth (Sanctum or Passport).
- Keep web session auth for current Blade UI.

### Phase C: Endpoint Rollout (Narrow Scope)
- Start with read endpoints only for service marketplace app screens:
  - service listing dataset (for `services/index`)
  - service detail dataset (for `services/show`)
  - supporting category/filter metadata if required by listing UI
- Defer write/action lifecycle endpoints until explicitly needed.

### Phase D: Operational Readiness
- Add API versioning (`/api/v1`).
- Add rate limiting and audit logs.
- Add integration test suite for critical flows.

## Moderation Domain Model to Keep Consistent

### User Status
- `active`: no hard restrictions.
- `suspended`: cannot access platform.
- `blacklisted`: permanently hard-locked.
- `blocked`: helper seller restriction only (buyer still available).

### Warning Concept
- Warning count accumulates until configured threshold.
- Threshold should be admin-configurable with audit trail.

### Graduation Concept
- Graduation state affects account handling and reactivation rules.
- Keep graduation checks in service/controller layer, not view templates.

## Suggested Implementation Plan (Low Risk)

### Admin-First Rollout (Start Here)

#### Phase 1: Admin UI/Logic Boundary Cleanup
- Scope:
  - `admin/requests`
  - `admin/services`
  - `admin/feedback`
  - `admin/students` + `admin/community`
- Actions:
  - move heavy `@php` blocks from Blade to controller/service prepared view data
  - extract inline modal/workflow JS to dedicated JS modules per page
  - keep Blade to rendering and light conditional display only
- Exit criteria:
  - no direct query/model construction in admin Blade
  - no long inline workflow scripts in admin Blade
  - same behavior parity verified with regression checks

#### Phase 2: Admin Domain Service Extraction
- Create service classes and move business rules out of controllers:
  - `ModerationService`
  - `ServiceRequestWorkflowService`
  - `GraduationGovernanceService`
- Centralize:
  - warning threshold checks
  - final action mapping by role
  - dispute outcome transitions
- Exit criteria:
  - controllers become orchestration-only (validate, call service, return response)
  - moderation transitions are unit-testable independently

#### Phase 3: API-Ready Response Layer (Admin Domain First)
- Add API Resources / DTO mappers for core admin-managed entities:
  - User moderation snapshot
  - Service
  - Service Request
- Standardize status labels and transition keys in one mapper.
- Exit criteria:
  - web/admin and API share consistent response/status semantics
  - no duplicated status mapping logic across views/controllers

#### Phase 4: Contract-First Admin API Slice (`/api/v1`)
- Publish only service listing/detail read endpoints required by external app screens.
- Keep admin moderation/workflow APIs out of scope for now.
- Add auth/rate limit/audit only for these scoped endpoints.
- Exit criteria:
-  - stable contract docs for listing/detail payloads
-  - integration test coverage for service list/detail responses

### Execution Order Inside Admin (Small to Large)
1. `admin/feedback` (small, high clarity)
2. `admin/services` (medium)
3. `admin/requests` (complex modal/workflow)
4. `admin/students` and `admin/community` (policy-heavy, cross-cutting)

### Sprint Mapping
- **Sprint 1**: Phase 1 for `feedback + services`
- **Sprint 2**: Phase 1 for `requests` + start Phase 2 services
- **Sprint 3**: Finish Phase 2 + Phase 3 base resources/mappers
- **Sprint 4**: Optional scoped `/api/v1` service list/detail endpoints (only if needed)

### Admin-First Guardrails
- Keep existing routes and UX intact while refactoring internals.
- Refactor one admin module at a time; no cross-module mixed PRs.
- Every moved rule must have at least one test or regression assertion.
- No new business logic inside Blade during this rollout.

## Complexity and Effort
- Complexity: medium.
- Main challenge is consistency across existing flows, not technical feasibility.
- This is achievable without stopping current feature development if phased correctly.

## Non-Admin Rollout (Phase-by-Phase)

### Preflight Requirements
- Establish behavior baseline for each role and critical flow.
- Use risk scoring per page: `traffic × coupling × criticality`.
- Enforce refactor-only contract unless UX change is explicitly approved.

### Phase 0 Artifacts
- Inventory map: `docs/migration/non-admin-phase-0-inventory-map.md`
- Hotspot matrix: `docs/migration/non-admin-phase-0-hotspot-matrix.md`
- Migration decision matrix: `docs/migration/non-admin-phase-0-migration-decision-matrix.md`
- Slice tracker + PR controls: `docs/migration/non-admin-slice-tracker.md`

### Foundation Rules for Non-Admin
- JS modules must follow deterministic lifecycle: `init`, `destroy`, `reinit`.
- Use delegated `data-action` event model for dynamic content.
- Forbid dynamic code execution (`eval`, `new Function`) in loaders/modules.

### Global Execution Controls
- Vertical slice workflow: Audit → Refactor → Validate → Smoke test → Merge.
- One feature slice per PR to minimize blast radius.
- Each PR must include gate results and rollback notes.
