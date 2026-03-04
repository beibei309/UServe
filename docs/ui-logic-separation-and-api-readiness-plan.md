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

### Phase C: Endpoint Rollout
- Start with read endpoints:
  - service search
  - helper profile
  - categories
- Then write endpoints:
  - create request
  - lifecycle actions (accept/reject/in-progress/dispute/finalize)

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

### Sprint 1
- Remove Blade model/query calls.
- Extract dispute modal JS + service request workflow JS to dedicated files.
- Add one shared status mapper utility.

### Sprint 2
- Introduce service classes for:
  - moderation actions
  - service request transitions
- Introduce API resources for service/service request.

### Sprint 3
- Add token auth for external integration.
- Publish first `/api/v1` endpoints and contract docs.

## Complexity and Effort
- Complexity: medium.
- Main challenge is consistency across existing flows, not technical feasibility.
- This is achievable without stopping current feature development if phased correctly.
