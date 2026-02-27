# UServe

UServe is a Laravel 12 application with a strict prefixed PostgreSQL schema contract.

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

## CI

GitHub Actions workflow is available at:

- [CI workflow](.github/workflows/ci.yml)

It runs:

- fresh migration + seed
- full test suite
- Blade compile check
- config sanity
- event/listener listing sanity