<?php

namespace App\Enums;

enum GenderPolicy: string
{
    case ALL = 'all';
    case MALE_ONLY = 'male_only';
    case FEMALE_ONLY = 'female_only';
    case MARRIED_COUPLES = 'married_couples';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'Campur / Semua',
            self::MALE_ONLY => 'Khusus Putra / Pria',
            self::FEMALE_ONLY => 'Khusus Putri / Wanita',
            self::MARRIED_COUPLES => 'Khusus Pasutri / Berkeluarga',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::ALL => 'neutral',
            self::MALE_ONLY => 'accent',
            self::FEMALE_ONLY => 'primary',
            self::MARRIED_COUPLES => 'warning',
        };
    }
}
