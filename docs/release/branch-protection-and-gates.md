# Branch Protection and Gate Policy

## Protected Branch Scope
- Apply to `develop`, `main`, and production release branches.

## Required Pull Request Rules
- Require a pull request before merging.
- Require at least 1 approving review.
- Dismiss stale approvals when new commits are pushed.
- Require conversation resolution before merging.
- Restrict direct pushes to protected branches.

## Required Status Checks
- Require all checks to pass before merging.
- Mark these checks as required:
  - `nonadmin-gate`
  - `admin-gate`
  - `laravel-tests`

## CI Enforcement Source
- Workflow: `.github/workflows/ci.yml`
- Non-admin gate script: `scripts/nonadmin_regression_gate.sh`
- Admin gate script: `scripts/admin_regression_gate.sh`

## Local Developer Enforcement
- Install hooks locally:
  - `powershell -ExecutionPolicy Bypass -File ./scripts/install_git_hooks.ps1`
- Hook path:
  - `scripts/git-hooks/pre-push`
- Pre-push behavior:
  - runs `scripts/nonadmin_regression_gate.ps1`
  - runs `scripts/admin_regression_gate.ps1`

## Monthly Allowlist Review
- Cadence: first Monday of every month.
- Owner: Engineering maintainer on release duty.
- Required output:
  - review both gate allowlists
  - remove entries no longer needed
  - record outcome in `docs/migration/blade-separation-final-smoke-matrix.md`

## Release Requirement
- Before each production release, rerun the smoke matrix and append a new dated evidence block to:
  - `docs/migration/blade-separation-final-smoke-matrix.md`
