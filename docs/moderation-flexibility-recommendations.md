# Moderation Flexibility Recommendations

## Purpose
- Capture recommended enhancements for moderation flexibility before implementation.
- Prioritize changes that improve policy adaptability without weakening control.

## Recommended Features

### 1) Configurable Warning Thresholds
- Add admin-editable settings for:
  - User warning limit
  - Service warning limit
- Optional extension:
  - Role-specific warning limits for student/helper/community

### 2) Controlled Warning Adjustments per User
- Prefer controlled actions over free number edits:
  - Add +1 warning
  - Reduce -1 warning
  - Reset warnings to 0
- Require:
  - Mandatory reason
  - Confirmation step
  - Actor identity and timestamp logging

### 3) Moderation Action Presets
- Provide reusable reason templates:
  - Late delivery
  - No-show
  - Abusive communication
  - Misleading service description
  - Payment dispute issue
- Allow admin to start from preset and edit details.

### 4) Dispute Resolution Outcome Matrix
- Expand outcomes to avoid forced single-path punishment:
  - Warn + resume request
  - Suspend/blacklist + cancel request
  - Close without penalty + resume to waiting payment
  - Close without penalty + mark completed and paid
- Optional:
  - Shared-fault outcome

### 5) Evidence-First Dispute Checklist
- Add required checklist before final action:
  - Payment proof reviewed
  - Chat timeline reviewed
  - Delivery/work completion evidence reviewed
- Store checklist results in dispute resolution record.

### 6) Moderation Audit Log
- Add centralized log for all moderation changes:
  - Who performed action
  - Target user/service/request
  - Old value → new value
  - Reason
  - Timestamp
- Support filters by date, actor, action type, and target role.

### 7) Permission Segmentation
- Split privileges by sensitivity:
  - Standard admin: warnings and dispute triage
  - Senior/super admin: threshold updates and manual warning adjustments

### 8) Undo Window for Critical Actions
- Optional safety feature:
  - Allow rollback of recent moderation actions within a limited window
  - Require reason and log rollback metadata

### 9) Action Preview Before Submit
- Show one-line impact preview before confirmation:
  - Status changes
  - Mail/notification effects
  - Request lifecycle effect

## Suggested Rollout (Small, Safe First)
- Phase 1:
  - Global warning settings UI
  - Controlled per-user warning adjustments (+1/-1/reset)
  - Mandatory reason + audit log
- Phase 2:
  - Role-specific limits
  - Preset reasons
  - Evidence checklist
- Phase 3:
  - Permission segmentation refinements
  - Undo window

## Risk Notes
- Free-form direct warning count edits are high risk without audit and role control.
- Threshold changes should be constrained to privileged admins.
- Any flexible action must preserve traceability and consistency.
