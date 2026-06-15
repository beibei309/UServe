# UPSI2u

UPSI2u is a Laravel 12 application with a strict prefixed PostgreSQL schema contract.

## Schema Contract

The project follows prefixed physical tables and columns as source of truth.

- Tables: `h2u_*`
- User columns: `hu_*`
- Admin columns: `ha_*`
- Service Request columns: `hsr_*`
- Review columns: `hr_*`
- Notification columns: `hn_*`

## Current Launch Status

- Prefixed schema remap completed for models/controllers/views.
- Default destructive seeding is blocked in production unless explicitly allowed.
- Use `LaunchSeeder` or focused page/admin seeders for production setup.
- UPSI student status can run in the supervisor-required single-connection mode.
- Service request payment proof files are served through authorized controller routes.

## Release Check Commands

Run these before release:

1. `composer install --no-dev --optimize-autoloader`
2. `npm ci`
3. `npm run build`
4. `php artisan migrate --force`
5. `php artisan db:seed --class=LaunchSeeder --force`
6. `php artisan test`
7. `php artisan config:cache`
8. `php artisan route:cache`
9. `php artisan view:cache`
10. `php artisan event:list`

Do not run `php artisan migrate:fresh --seed --force` on production data. The default `DatabaseSeeder` truncates application tables and is intended for local reset/demo data only.

## Production Launch Checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` to the real HTTPS domain.
- Rotate any database, mail, and app credentials that were shared outside the server.
- Set the platform database with `DB_CONNECTION=pgsql` and the expected PostgreSQL `DB_*` values.
- For one-connection UPSI deployment, set `UPSI_DB_CONNECTION=pgsql`, `UPSI_LIVE_REFRESH_ENABLED=true`, and `UPSI_STUDENT_VIEW=home2u.h2u_student`.
- Keep `ALLOW_DESTRUCTIVE_DATABASE_SEEDING=false` unless an intentional reset is approved.
- Keep `FILESYSTEM_LOCAL_SERVE=false`; private payment proofs and verification files should use authorized controller routes.
- Run `php artisan upsi:sync-student-status --limit=10` as a dry-run before enabling scheduled/apply sync.
- Confirm community verification, helper onboarding, service creation, service request payment proof, reports, and admin review flows in the browser.

## External Asset / CSP Notes

Several production pages still load third-party assets from CDNs. Before a strict university CSP is enabled, either bundle these libraries locally or allow only the exact required sources.

Current CDN inventory:

- Admin layout/login: Tailwind CDN, SweetAlert2, Font Awesome, Leaflet.
- Admin dashboard and student dashboard: Chart.js.
- Admin legal pages and service create/edit pages: Quill.
- Service create/edit pages: Flatpickr and SweetAlert2.
- Verification/onboarding pages: SweetAlert2.
- Public profile edit helper script dynamically loads SweetAlert2.

Preferred launch hardening path:

1. Bundle already-installed packages locally first: SweetAlert2, Flatpickr, Font Awesome.
2. Add npm packages for Chart.js, Quill, and Leaflet, then expose them through `resources/js/app.js` or page-specific Vite entries.
3. After CDNs are removed, add a strict CSP header allowing the app origin and required image/font sources only.

## Cleanup Candidates

- `resources/views/services/show.blade.php` appears to be legacy because current service links use `services.details`; verify all routes before deletion.
- Keep `resources/views/services/details.blade.php` and `public/js/nonadmin-services-details*.js`; they are active.
- `services/apply.blade.php` and `public/js/nonadmin-services-apply.js` were removed because `/services/apply` redirects to `/services`.

## Deployment Commands

### Local (normal development)
```bash
composer install
npm install
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=PageContentSeeder --force
npm run dev
```

### Local (production-like test on localhost)
```bash
composer install
npm install
npm run build
php artisan migrate --force
php artisan db:seed --class=PageContentSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Admin server / production
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --class=PageContentSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

## CI

GitHub Actions workflow is available at:

- [CI workflow](.github/workflows/ci.yml)

It runs:

- non-admin Blade gate
- admin Blade gate
- fresh migration + seed
- full test suite
- Blade compile check
- config sanity
- event/listener listing sanity

## Merge and Gate Policy

- Install local pre-push hooks: `powershell -ExecutionPolicy Bypass -File ./scripts/install_git_hooks.ps1`
