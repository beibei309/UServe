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

See full contract in [docs/schema-contract.md](docs/schema-contract.md).

## Current Refactor Status

- Prefixed schema remap completed for models/controllers/views.
- `migrate:fresh --seed --force` passes.
- Test suite passes.
- Blade cache compile passes.

## Release Check Commands

Run these before release:

1. `php artisan migrate:fresh --seed --force`
2. `php artisan test`
3. `php artisan view:clear; php artisan view:cache`
4. `php artisan config:cache`
5. `php artisan event:list`

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

- Branch protection and required checks: [docs/release/branch-protection-and-gates.md](docs/release/branch-protection-and-gates.md)
- Install local pre-push hooks: `powershell -ExecutionPolicy Bypass -File ./scripts/install_git_hooks.ps1`
