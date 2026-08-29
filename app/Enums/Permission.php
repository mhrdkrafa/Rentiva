<?php

namespace App\Enums;

enum Permission: string
{
    // Admin / System Permissions
    case ACCESS_ADMIN_PANEL = 'access_admin_panel';
    case MANAGE_USERS = 'manage_users';
    case MANAGE_CMS = 'manage_cms';
    case MANAGE_SETTINGS = 'manage_settings';
    case MODERATE_PROPERTIES = 'moderate_properties';

    // Owner Permissions
    case MANAGE_PROPERTIES = 'manage_properties';
    case MANAGE_UNITS = 'manage_units';
    case MANAGE_PRICING = 'manage_pricing';
    case REVIEW_BOOKINGS = 'review_bookings';
    case ACCEPT_BOOKINGS = 'accept_bookings';
    case REJECT_BOOKINGS = 'reject_bookings';
    case VIEW_FINANCE = 'view_finance';
    case ASSIGN_MANAGERS = 'assign_managers';

    // Tenant Permissions
    case REQUEST_BOOKING = 'request_booking';
    case CANCEL_OWN_BOOKING = 'cancel_own_booking';
    case VIEW_OWN_RENTALS = 'view_own_rentals';
    case CREATE_REVIEW = 'create_review';

    // Property Manager Permissions (scoped to assigned properties)
    case MANAGE_ASSIGNED_UNITS = 'manage_assigned_units';
    case REVIEW_ASSIGNED_BOOKINGS = 'review_assigned_bookings';
    case MANAGE_ASSIGNED_AVAILABILITY = 'manage_assigned_availability';

    public function label(): string
    {
        return match ($this) {
            self::ACCESS_ADMIN_PANEL => 'Akses Panel Admin',
            self::MANAGE_USERS => 'Kelola Pengguna',
            self::MANAGE_CMS => 'Kelola Konten CMS',
            self::MANAGE_SETTINGS => 'Kelola Pengaturan',
            self::MODERATE_PROPERTIES => 'Moderasi & Verifikasi Properti',
            self::MANAGE_PROPERTIES => 'Kelola Properti Sendiri',
            self::MANAGE_UNITS => 'Kelola Unit / Kamar',
            self::MANAGE_PRICING => 'Kelola Skema Harga',
            self::REVIEW_BOOKINGS => 'Tinjau Permintaan Booking',
            self::ACCEPT_BOOKINGS => 'Terima Permintaan Booking',
            self::REJECT_BOOKINGS => 'Tolak Permintaan Booking',
            self::VIEW_FINANCE => 'Lihat Laporan Keuangan',
            self::ASSIGN_MANAGERS => 'Tugaskan Manajer Properti',
            self::REQUEST_BOOKING => 'Ajukan Permintaan Sewa',
            self::CANCEL_OWN_BOOKING => 'Batalkan Pengajuan Sendiri',
            self::VIEW_OWN_RENTALS => 'Lihat Sewa Aktif Sendiri',
            self::CREATE_REVIEW => 'Buat Ulasan Properti',
            self::MANAGE_ASSIGNED_UNITS => 'Kelola Unit Properti yang Ditugaskan',
            self::REVIEW_ASSIGNED_BOOKINGS => 'Tinjau Booking Properti yang Ditugaskan',
            self::MANAGE_ASSIGNED_AVAILABILITY => 'Atur Ketersediaan Properti yang Ditugaskan',
        };
    }
}
