# Rentiva — SCHEMA.md

## Relationship Map

```text
User
 ├── owns -> Properties
 ├── requests -> BookingRequests
 ├── rents -> Rentals
 ├── sends -> Messages
 ├── saves -> Favorites
 ├── writes -> Articles
 └── creates -> Reports

Property
 ├── owner
 ├── property_type
 ├── location
 ├── images
 ├── facilities
 ├── units
 ├── promotions
 ├── booking_requests
 ├── rentals
 └── reviews

Unit
 ├── property
 ├── room_type
 ├── images
 ├── facilities
 ├── price_plans
 ├── availability_blocks
 ├── booking_requests
 └── rentals

BookingRequest
 ├── tenant
 ├── property
 ├── unit
 └── may become Rental

Rental
 ├── tenant
 ├── owner
 ├── property
 ├── unit
 ├── contract
 ├── invoices
 └── reviews

Invoice
 ├── invoice_items
 └── payments

Conversation
 ├── participants
 └── messages
```

## Statuses
Property: draft, pending_review, published, suspended, archived

Verification: unverified, pending, verified, rejected

Unit: available, reserved, occupied, maintenance, unavailable

Booking: pending, accepted, rejected, cancelled, expired

Rental: pending, active, ending, ended, terminated

Invoice: draft, issued, pending, paid, overdue, cancelled

Payment: pending, processing, paid, failed, refunded, partially_refunded

Promotion: draft, scheduled, active, paused, expired

Report: open, investigating, resolved, rejected

## Booking Invariants
1. No overlapping active rentals for one unit.
2. No conflicting accepted bookings.
3. Availability changes are atomic.
4. Booking price values are snapshots.
5. Cancellation rules are server-side.
6. Expired requests cannot be accepted without renewal.
7. Owners can only accept requests for controlled properties.
8. Tenants cannot book unavailable/non-bookable units.

## Payment Invariants
1. Verify webhook signatures.
2. Webhooks are idempotent.
3. Provider references are unique when available.
4. Payment amount is validated server-side.
5. Client totals are never trusted.
6. Refunds cannot exceed refundable amounts.

## Search
Keyword + location + property type + price + facilities + room type + availability + verification + promotion + sort.

MVP can use MySQL queries and indexes. A dedicated search engine may be introduced later without changing the domain model.

## Location
Use hierarchical locations such as country → province → city → district/area → landmark/campus/transit point. Exact property coordinates should not automatically be exposed publicly.

## CMS
Homepage sections:
Hero → Search → Locations → Featured Properties → Benefits → Promotions → Articles → CTA

Each section has a stable key, component type, enabled state, order, and controlled configuration.

## SEO
Indexable entities expose title, description, canonical, robots, social image, and published date where relevant.
