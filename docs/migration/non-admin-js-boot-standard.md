# Non-Admin JS Boot Standard

## Required Boot Pattern
- Use a single IIFE module entry.
- Resolve one config bridge element by id and return early when missing.
- Parse config values once, then pass into feature functions.
- Register delegated listeners from stable containers where practical.
- Avoid duplicate event binding via `data-*-bound` guard when binding to repeated nodes.

## Contract Rules
- Every dynamic non-admin page must define:
  - controller/provider prepared fields
  - one config bridge id in Blade
  - one page module in `public/js`
- Config bridge payload should include:
  - urls
  - csrf token when needed
  - booleans already normalized as `'true'/'false'`
  - data payload serialized server-side

## Listener Rules
- Prefer delegated `click`/`change` listeners on a root node for repeatable UI lists.
- For singleton controls, bind directly once after bridge validation.
- For async actions:
  - disable trigger during request
  - restore on error
  - keep user-visible success/error feedback consistent

## Naming Rules
- Bridge ids: `<feature><Page>Config` (camelCase, unique per page).
- Module files: target namespace `public/js/nonadmin-<feature>-<page>.js`.
- Data attributes: `data-<kebab-case>` and parse once at boot.

## Controller Boundary Rules
- Controller/provider/mail layer prepares display-ready fields.
- Blade renders values only and avoids parsing/branch preparation blocks.
- JS handles interaction behavior only and avoids hard-coded route duplication when bridge values exist.

## Minimum Verification
- grep gates on Blade:
  - `@php`
  - inline `<script>`
  - inline `on*`
- syntax/lint gates:
  - `php -l` on touched controllers/providers/mail files
  - `node --check` on touched `public/js` modules
- framework gate:
  - `php artisan view:cache`
