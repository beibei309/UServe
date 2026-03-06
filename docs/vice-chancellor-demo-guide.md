# Vice Chancellor Demo Guide

## Demo Goal
- Show that the platform is safe, useful, and operationally controllable.
- Emphasize governance: warning, suspend, blacklist, blocked seller mode, and graduation handling.
- Present clear value for UPSI ecosystem and future integration readiness.

## Audience-Friendly Storyline
- Start from student/user value.
- Show trust and safety controls.
- Show admin controls and transparency.
- End with scalability/integration direction.

## 10-Step Demo Flow

### 1) Platform Overview (1-2 minutes)
- Explain platform purpose:
  - connect students/helpers/community for services
  - monitored transaction lifecycle
  - integrated trust and moderation

### 2) Browse and Discover Services
- Show service listings, categories, and provider profiles.
- Explain this is the entry point for users.

### 3) Create Service Request
- Show requester selecting a service and sending request.
- Mention status begins from `pending`.

### 4) Provider Workflow
- Show provider accepting/rejecting request.
- Continue to `in_progress` and completion stages.

### 5) Dispute Trigger
- Show how a dispute can be raised (real-world scenario).
- Explain disputes are not auto-punishment; admin reviews evidence.

### 6) Admin Request Resolution Modal
- Show neutral "Reported Statement".
- Explain action options:
  - Warn
  - Suspend/Blacklist
  - Close without penalty: Resume
  - Close without penalty: Completed & Paid
  - Dismiss without penalty: Cancelled

### 7) Warning and Restriction Matrix
- Explain statuses in simple terms:
  - **Active**: full access
  - **Blocked**: helper cannot sell, still can buy
  - **Suspended**: cannot access account (temporary hard lock)
  - **Blacklisted**: permanent hard lock
- Explain warnings:
  - warnings accumulate
  - threshold leads to stronger action

### 8) Role-Aware Enforcement
- Explain moderation differs by role:
  - helper can be seller-blocked
  - community can be blacklisted
  - student/helper can be suspended

### 9) Graduation Handling
- Explain graduation-related logic:
  - reminder communication before graduation
  - reactivation restrictions aligned with policy for graduated users

### 10) Operational Confidence
- Show admin dashboards/pages that track requests, services, feedback moderation.
- Mention mail notifications and status enforcement for consistency.

## Core Concepts to Explain Clearly

### A) Warning
- Low-severity correction mechanism.
- Includes message to user and record in warning count.

### B) Suspend / Blacklist
- High-severity restrictions for repeated or serious violations.
- Used after review and documented reason.

### C) Active vs Restricted
- Active = normal access.
- Restricted state depends on severity and role.

### D) Evidence-First Moderation
- Report text is a claim, not final truth.
- Admin checks proof before selecting action.

## Suggested Live Demo Accounts
- Buyer (student/community requester)
- Seller/helper account
- Admin account
- Optional disputed request prepared in advance

## Safety Talking Points (for leadership)
- Actions are tied to explicit reasons.
- Status transitions are enforced by middleware and controller rules.
- Communication is consistent through mail notifications.
- Policy flexibility is planned with controlled admin settings and audit approach.

## Integration Readiness Talking Points
- Current web platform is operational and stable.
- Next architecture step:
  - separate UI rendering and business logic fully
  - publish token-secured API endpoints for Muallim app
  - versioned contract for safe cross-system integration

## Demo Checklist (Before Meeting)
- Prepare one request in `disputed` status.
- Prepare one helper with non-zero warnings.
- Prepare one blocked helper (to show seller restriction behavior).
- Prepare one suspended/blacklisted user (to show access enforcement).
- Verify mail setup and queues for demonstration screenshots/logs.
- Ensure stable internet and backup local screenshots/video.

## Backup Plan If Live Flow Fails
- Use prepared screenshots for each stage.
- Show moderation action map and status matrix documents.
- Explain expected transitions clearly with examples.
