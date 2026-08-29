# Rentiva — AGENTS.md

## Project Identity
**Rentiva** is an original rental marketplace and property-management platform. Reference marketplaces are used only for feature inspiration. Never copy their branding, content, assets, source code, proprietary terminology, or exact visual identity.

## Stack
- Laravel 13
- PHP 8.2+
- Filament 5
- Livewire 4
- Blade
- Tailwind CSS
- Alpine.js
- MySQL 8+
- Redis
- Vite
- Pest
- Laravel Boost where available

## Agent Rules
1. Read `PRD.md`, `ARCHITECTURE.md`, `SCHEMA.md`, `DESIGN.md`, and `RULES.md` before major work.
2. Follow `TASK.md` unless the task explicitly changes the plan.
3. Never invent payment, booking, refund, or cancellation behavior.
4. Never trust client-side price, availability, ownership, or payment state.
5. Booking/payment state transitions must be server-side and transactional.
6. Use Eloquent relationships and Actions/Services for business workflows.
7. Filament is the admin CMS, not the public design system.
8. Every schema change requires a migration.
9. Critical marketplace workflows require automated tests.
10. Never store secrets or credentials in source control.
11. Validate and authorize uploads.
12. Use queues for slow work such as image processing and notifications.
13. Prevent double booking with transactional checks and appropriate locking/constraints.
14. Store money as integer minor units, never floating point.
15. Payment webhooks must verify signatures and be idempotent.
16. Owners may only manage properties they own or are assigned to manage.
17. Private tenant/owner information must never be public.
18. Do not turn arbitrary CSS properties into CMS fields.
19. CMS controls content/composition; code controls component design.
20. Update documentation when architecture, schema, or workflow changes.

## Definition of Done
A feature is complete when functionality, authorization, validation, responsive UI, edge cases, tests, migrations, and relevant documentation are complete.
