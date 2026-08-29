<?php

namespace App\Enums;

enum UnitStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';
    case UNAVAILABLE = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia (Siap Huni)',
            self::RESERVED => 'Dipesan (Reserved)',
            self::OCCUPIED => 'Terisi Penuh',
            self::MAINTENANCE => 'Perbaikan / Maintenance',
            self::UNAVAILABLE => 'Tidak Tersedia',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::RESERVED => 'warning',
            self::OCCUPIED => 'danger',
            self::MAINTENANCE => 'gray',
            self::UNAVAILABLE => 'gray',
        };
    }

    public function isBookable(): bool
    {
        return $this === self::AVAILABLE;
    }
}
