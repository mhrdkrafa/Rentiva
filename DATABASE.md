# Rentiva — DATABASE.md

## Database
MySQL 8+, InnoDB, utf8mb4. Redis for cache/queues in production. Laravel migrations are the authoritative schema history.

## Core Tables

### users
id, name, email, phone, password, status, email_verified_at, timestamps, deleted_at

### user_profiles
id, user_id, avatar_path, bio, gender nullable, date_of_birth nullable, occupation nullable, emergency_contact nullable, timestamps

### locations
id, parent_id nullable, name, slug, type, latitude nullable, longitude nullable, is_active, timestamps

### property_types
id, name, slug, description, is_active, timestamps

### properties
id, owner_id, property_type_id, location_id, name, slug, description, address, latitude, longitude, public_location_precision, verification_status, status, featured, published_at, seo_title, seo_description, timestamps, deleted_at

### property_images
id, property_id, path, alt_text, caption, sort_order, is_cover, timestamps

### facilities
id, name, slug, icon, type, is_active, timestamps

### facility_property
property_id, facility_id

### room_types
id, name, slug, description, timestamps

### units
id, property_id, room_type_id, name, floor, size, capacity, description, status, available_from, timestamps, deleted_at

### unit_images
id, unit_id, path, alt_text, sort_order, timestamps

### unit_facility
unit_id, facility_id

### price_plans
id, unit_id, billing_period, amount, deposit_amount, active_from, active_until, is_active, timestamps

### additional_fees
id, property_id nullable, unit_id nullable, name, type, amount, frequency, is_required, is_active, timestamps

### availability_blocks
id, unit_id, starts_at, ends_at, reason, status, source_type, source_id nullable, timestamps

### booking_requests
id, tenant_id, property_id, unit_id, starts_at, ends_at nullable, price_snapshot, fee_snapshot, deposit_snapshot, total_snapshot, status, tenant_note, owner_note, expires_at, accepted_at, rejected_at, cancelled_at, timestamps

### rentals
id, booking_request_id, tenant_id, owner_id, property_id, unit_id, starts_at, ends_at nullable, status, monthly_amount, deposit_amount, timestamps, deleted_at

### contracts
id, rental_id, document_path, version, starts_at, ends_at nullable, status, signed_at nullable, timestamps

### invoices
id, rental_id, invoice_number, billing_period_start, billing_period_end, due_at, subtotal, fees, discount, total, status, issued_at, paid_at nullable, timestamps

### invoice_items
id, invoice_id, description, quantity, unit_amount, total_amount, timestamps

### payments
id, invoice_id nullable, booking_request_id nullable, payer_id, amount, currency, provider, provider_reference, idempotency_key, status, paid_at nullable, metadata JSON nullable, timestamps

### refunds
id, payment_id, amount, reason, provider_reference nullable, status, processed_at nullable, timestamps

### conversations
id, property_id nullable, booking_request_id nullable, timestamps

### conversation_participants
conversation_id, user_id, joined_at

### messages
id, conversation_id, sender_id, body, attachment_path nullable, read_at nullable, timestamps, deleted_at

### favorites
id, user_id, property_id, timestamps; unique(user_id, property_id)

### promotions
id, owner_id nullable, name, type, value, starts_at, ends_at, max_uses nullable, status, timestamps

### property_promotions
property_id, promotion_id

### reviews
id, rental_id, reviewer_id, property_id, rating, title, body, status, published_at nullable, timestamps

### articles
id, author_id, category_id nullable, title, slug, excerpt, body, featured_image_path, status, published_at, seo_title, seo_description, timestamps, deleted_at

### categories
id, name, slug, type, timestamps

### homepage_sections
id, key, type, title nullable, config JSON nullable, is_enabled, sort_order, timestamps

### menus / menu_items
Hierarchical CMS navigation.

### website_settings
key, value, type, group, timestamps

### reports
id, reporter_id, target_type, target_id, reason, description, status, resolved_by nullable, resolved_at, timestamps

### audit_logs
id, user_id nullable, action, subject_type, subject_id, old_values JSON nullable, new_values JSON nullable, ip_address nullable, user_agent nullable, timestamps

## Financial Rules
- Store IDR amounts as integers.
- Never use FLOAT/DOUBLE for money.
- Store price/fee snapshots on historical bookings and invoices.
- Never physically delete financial records.

## Important Indexes
properties(slug), properties(location_id,status,verification_status), units(property_id,status), price_plans(unit_id,is_active), booking_requests(unit_id,starts_at,ends_at,status), rentals(unit_id,starts_at,ends_at,status), invoices(rental_id,due_at,status), payments(provider_reference), payments(idempotency_key), messages(conversation_id,created_at), articles(status,published_at).

## Transaction Rules
Booking creation/acceptance must be transactional. Availability checks must be atomic. Payment webhook processing must be idempotent. Invoice generation must not duplicate a billing period.
