# Project Demo Runbook (Warning, Banned, Active, Graduate)

## Objective
- Deliver a clear, leadership-friendly demo of core platform value and trust/safety controls.
- Show how moderation protects users while preserving fair outcomes.

## Demo Duration
- Recommended: 20–30 minutes.

## Demo Roles to Prepare
- **Buyer account** (student/community)
- **Seller account** (helper)
- **Admin account**
- Optional: one **graduated** user profile for graduation scenario

## Data Setup Checklist (Before Live Demo)
- One service already approved and visible.
- One service request in each useful status:
  - `pending`
  - `in_progress`
  - `disputed`
- One helper with warning count > 0.
- One account in each moderation state:
  - active
  - blocked (seller-restricted only)
  - suspended
  - blacklisted

## Demo Flow (Recommended Sequence)

### Step 1: Open with Platform Purpose (2 minutes)
- Explain the platform in one sentence:
  - “UPSI service marketplace with built-in trust and moderation governance.”
- State what audience will see:
  - user flow
  - admin governance
  - status enforcement and fairness

### Step 2: User Journey (3 minutes)
- Login as buyer.
- Browse services and open provider profile.
- Create a service request.
- Explain request lifecycle starts at `pending`.

### Step 3: Seller Journey (3 minutes)
- Login as helper/seller.
- Accept request and move it to `in_progress`.
- Show progression toward completion/payment.

### Step 4: Dispute Scenario (4 minutes)
- Show a request marked `disputed`.
- Open admin **Resolve Dispute** modal.
- Explain this key message:
  - dispute text is a **reported statement**, not automatic truth.

### Step 5: Admin Resolution Options (5 minutes)
- In the modal, explain each option in plain language:
  - **Warn**: increase warning, notify user, request resumes.
  - **Suspend/Blacklist**: serious penalty, hard-lock account, request cancelled.
  - **Close without penalty (Resume)**: continue transaction fairly.
  - **Close without penalty (Completed & Paid)**: finalize when payment/proof already valid.
  - **Dismiss without penalty (Cancelled)**: close case without user punishment.

### Step 6: Status Model (3 minutes)
- Explain account states clearly:
  - **Active**: full access.
  - **Blocked**: helper cannot sell, still can buy.
  - **Suspended**: temporary hard lock.
  - **Blacklisted**: permanent hard lock.
- Explain warning threshold concept:
  - warnings accumulate
  - crossing threshold enables stronger action.

### Step 7: Enforcement Proof (3 minutes)
- Show what happens on restricted accounts:
  - blocked helper redirected away from seller-only actions.
  - suspended/blacklisted user blocked from normal access.
- Mention mail notifications are sent on moderation actions.

### Step 8: Graduation Handling (2 minutes)
- Explain graduation-related control:
  - graduation reminders
  - policy-aware reactivation logic for graduated accounts.
- Emphasize this protects platform continuity and policy compliance.

### Step 9: Wrap with Governance + Integration Readiness (2 minutes)
- Governance:
  - reasoned moderation
  - role-aware penalties
  - transparent statuses
- Integration readiness:
  - moving toward UI-logic separation
  - preparing API-first architecture for Muallim app integration.

## Talk Track (Simple Script)
- “Our platform does not only match users with services; it enforces trust.”
- “Warnings are corrective, not immediately punitive.”
- “Severe actions are role-aware and evidence-driven.”
- “We can close disputes fairly without forcing punishment.”
- “Status controls are enforced in system behavior, not only shown in UI.”

## Core Features to Highlight as “Important”
- Service discovery + request lifecycle.
- Dispute resolution matrix with fair/no-penalty outcomes.
- Warning and role-based moderation.
- Status enforcement (active/blocked/suspended/blacklisted).
- Graduation-aware account handling.
- Notification consistency (mail + system behavior).

## Backup Plan (If Live Flow Fails)
- Use prepared screenshots for each step.
- Show moderation action map document and explain expected transitions.
- Keep one short recorded walkthrough video as fallback.

## Do/Don’t for Leadership Demo
- **Do** keep language non-technical and policy-focused.
- **Do** explain impact in user terms (“can buy”, “cannot sell”, “account locked”).
- **Do** show fairness options, not only punishments.
- **Don’t** dive into code unless asked.
- **Don’t** change too many variables live; keep controlled scenarios.
