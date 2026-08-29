# Rentiva — Product Requirements Document

## 1. Product
Rentiva is a rental marketplace where tenants discover rooms/properties, compare options, contact owners, submit rental requests, and manage rentals. Owners manage properties, units, prices, availability, booking requests, tenants, promotions, and performance. Administrators manage the marketplace, moderation, users, content, and configuration.

Feature inspiration includes location search, list/map results, filtering, owner chat, rental requests, tenant billing, contracts, room availability, pricing, booking management, promotions, and reports. These are feature references only.

## 2. Goals
### MVP
- Search rental properties by location.
- Filter and sort listings.
- List/map-style discovery.
- Rich property details.
- Rooms/units and prices.
- Owner contact.
- Rental requests.
- Owner accept/reject workflow.
- Availability management.
- Tenant and owner dashboards.
- Admin management of core entities.
- CMS-driven homepage and public content.

### Later
- Payment gateway
- Recurring billing
- Digital contracts
- Refunds
- Promotions/ads
- Reviews
- Saved searches
- Recommendations
- Virtual tours
- Advanced analytics

## 3. Non-Goals for MVP
- Escrow
- Native mobile apps
- Full accounting
- Multi-country currency/tax
- Complex payment orchestration

## 4. Roles
- Guest
- Tenant
- Owner
- Property Manager
- Admin
- Super Admin

## 5. Core Features
### Search
Search by city, area, address, landmark, campus, transit point, or keyword. Filters: property type, rental period, price, room type, furnished status, gender policy, facilities, availability, verified status, promotion. Sort by relevance, price, newest, recommended.

### Property
Property fields include title, type, description, address, public location, owner, verification, facilities, photos, rooms, policies, promotion, and SEO.

### Units
Units support room type, floor, size, capacity, amenities, price, deposit, fees, availability, and images.

### Availability
Statuses: available, reserved, occupied, maintenance, unavailable.

### Booking
Tenant selects property/unit and period, submits request, owner accepts/rejects, then rental becomes active when required conditions are satisfied. Double booking must be prevented.

### Tenant Area
Dashboard, saved properties, requests, current rental, bills/contracts when enabled, owner chat, support.

### Owner Area
Dashboard, properties, rooms, availability, pricing, booking requests, tenants, billing/contracts when enabled, promotions, statistics, messages.

### Chat
Database-backed conversations for MVP with authorization and validated attachments.

### Finance Phase 2
Invoices, recurring bills, payment intents, gateway transactions, webhooks, refunds, reconciliation.

### CMS
Admin controls homepage, hero, search section, featured properties, locations, promotions, articles, FAQ, footer, menus, SEO, and brand settings.

### Notifications
In-app/email initially; WhatsApp/SMS later.

## 6. CMS Principle
Admin can enable/disable/reorder homepage sections and edit their content without changing Blade. Admin must not receive arbitrary CSS/layout controls.

## 7. MVP Acceptance
Users can register/login; owners can create listings; admins can verify/moderate; properties contain units; search/filter/sort works; tenant can request rental; owner can accept/reject; availability prevents conflicts; dashboards work; homepage is CMS-driven; public pages are responsive; critical workflows have automated tests.
