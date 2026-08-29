<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case PROPERTY_MANAGER = 'property_manager';
    case TENANT = 'tenant';
    case GUEST = 'guest';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::OWNER => 'Owner / Landlord',
            self::PROPERTY_MANAGER => 'Property Manager',
            self::TENANT => 'Tenant',
            self::GUEST => 'Guest',
        };
    }

    public function canAccessAdminPanel(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::ADMIN], true);
    }
}
