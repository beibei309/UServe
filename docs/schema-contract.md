# Schema Contract (Prefixed)

Date: 2026-02-27

This project uses prefixed physical tables/columns as the source of truth.

## Core Tables

- `h2u_users` (`hu_id` PK)
- `h2u_admins` (`ha_id` PK)
- `h2u_categories` (`hc_id` PK)
- `h2u_student_services` (`hss_id` PK)
- `h2u_service_requests` (`hsr_id` PK)
- `h2u_reviews` (`hr_id` PK)
- `h2u_notifications` (`hn_id` PK)
- `h2u_student_statuses` (`hss_id` PK)
- `h2u_faqs` (`hfq_id` PK)
- `h2u_favorites` (`hf_id` PK)
- `h2u_reports` (`hrp_id` PK)

## Naming Rules

- Tables: `h2u_*`
- User columns: `hu_*`
- Admin columns: `ha_*`
- Category columns: `hc_*`
- Service columns: `hss_*`
- Request columns: `hsr_*`
- Review columns: `hr_*`
- Notification columns: `hn_*`
- FAQ columns: `hfq_*`

## Intentional Compatibility Layer

- `app/Models/DatabaseNotification.php` is intentionally kept to bridge Laravel notification API fields (`id`, `type`, `data`, `read_at`) to prefixed columns (`hn_*`).

## Release Validation Commands

- `php artisan migrate:fresh --seed --force`
- `php artisan test`
- `php artisan view:clear; php artisan view:cache`
- `php artisan config:cache`
- `php artisan event:list`

If all commands pass, the prefixed schema contract is considered healthy for release candidate status.
