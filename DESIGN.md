# Rentiva — DESIGN.md

## Direction
Modern, trustworthy, marketplace-oriented, location-focused, easy to scan, friendly, conversion-oriented. Do not copy Mamikos or any other marketplace's exact visual identity.

## UX Goal
```text
Where do I want to live?
        ↓
What fits my budget/needs?
        ↓
Can I trust this listing?
        ↓
Is it available?
        ↓
How do I contact/book?
```

## Components
Header, Search Bar, Location Autocomplete, Filter Drawer, Sort Control, List/Map Toggle, Property Card, Gallery, Price Block, Facility Chips, Verification Badge, Availability Badge, Owner Card, Map Preview, Booking CTA, Unit Card, Review Card, FAQ, Article Card, Promotion Banner, Footer.

## Search Results
Desktop: filters/sidebar + result cards + map option. Mobile: search + filter/sort + cards + map toggle.

## Property Card Priority
1. cover image
2. verification/promotion
3. title
4. location
5. room/type summary
6. price
7. facilities
8. CTA

## Property Detail
Gallery → title/location → price/availability → CTA → facilities → rooms → description → policies → map → owner → reviews → similar properties.

## Dashboards
Tenant: current rental, upcoming payment, requests, saved properties, messages, help.
Owner: property performance, booking requests, occupancy, revenue, available rooms, messages, quick actions.

## Admin
Filament forms grouped into Basic Information, Location, Media, Units, Pricing, Availability, Policies, SEO, Publishing.

## Design Tokens
Use semantic tokens:
primary, primary-foreground, accent, surface, surface-muted, text, text-muted, border, success, warning, danger.

Centralize brand palette and typography.

## Responsive
Mobile-first; test 360px, 390px, 768px, 1024px, 1440px+. Essential functionality cannot depend on hover.

## Accessibility
Semantic HTML, visible focus, keyboard navigation, labels, contrast, alt text, accessible errors, reduced-motion support.

## CMS Controls
Admin controls section order, visibility, copy, images, CTAs, featured properties, promotions. Admin does not control arbitrary CSS.
