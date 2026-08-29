<?php

namespace App\Enums;

enum IssuePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Rendah (Kosmetik / Ringan)',
            self::MEDIUM => 'Sedang (Perlu Diperbaiki)',
            self::HIGH => 'Tinggi (Mengganggu Kenyamanan)',
            self::URGENT => 'Darurat / Mendesak (Air/Listrik Mati)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'primary',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }
}
