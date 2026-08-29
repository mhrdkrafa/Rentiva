# Rentiva — ARCHITECTURE.md

## Style
Modular Laravel monolith with three user-facing surfaces:

```text
Public Marketplace
Tenant/Owner Application
Filament Admin
        |
Application / Domain Layer
        |
MySQL + Redis + File Storage
        |
External Services
```

## Directory Structure

```text
app/
├── Actions/
│   ├── Booking/
│   ├── Payment/
│   ├── Property/
│   └── Rental/
├── Enums/
├── Filament/
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Models/
├── Notifications/
├── Policies/
├── Services/
│   ├── Booking/
│   ├── Pricing/
│   ├── Payment/
│   ├── Search/
│   └── Media/
└── Support/

resources/views/
├── layouts/
├── components/
├── marketplace/
├── tenant/
└── owner/
```

## Public Flow
Route → Controller → Query/Service → Eloquent → View Data → Blade.

## Booking Flow
Request → validation → authorization → Booking Action → DB transaction → availability lock/check → booking record → event → notification job.

## Payment Webhook
Gateway → webhook → signature verification → idempotency check → DB transaction → payment/invoice update → event → notification.

## Concurrency
Booking acceptance is a critical section. Use transactions, row locking where appropriate, database constraints, and final availability verification.

## Queues
Use queues for image processing, emails, notifications, payment reconciliation, invoice generation, imports, and optional search indexing.

## Caching
Cache location hierarchy, facilities, property types, homepage configuration, settings, and popular public content. Do not globally cache private authorization or financial state.

## Search
MVP: MySQL scopes/full-text/indexes. Scale-up: Laravel Scout/search engine with geo-aware ranking.

## Media
Public images use optimized derivatives. Contracts and private documents must be protected by authorization-controlled routes.

## Maps
Hide map-provider API calls behind a service/adapter.

## Filament
Admin manages users, properties, locations, facilities, bookings, finance, CMS, reports, and settings. Tenant/owner UX is not automatically the Filament UI.

## Testing
Cover publishing, ownership authorization, search filters, booking conflicts, booking acceptance, payment webhook idempotency, invoice totals, private documents, chat authorization, and CMS visibility.
