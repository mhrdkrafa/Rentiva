<?php

namespace App\Enums;

enum BillingPeriod: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case SEMI_ANNUALLY = 'semi_annually';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Harian',
            self::WEEKLY => 'Mingguan',
            self::MONTHLY => 'Bulanan',
            self::QUARTERLY => '3 Bulan (Triwulan)',
            self::SEMI_ANNUALLY => '6 Bulan (Semester)',
            self::YEARLY => 'Tahunan',
        };
    }

    public function suffix(): string
    {
        return match ($this) {
            self::DAILY => '/hari',
            self::WEEKLY => '/minggu',
            self::MONTHLY => '/bulan',
            self::QUARTERLY => '/3 bulan',
            self::SEMI_ANNUALLY => '/6 bulan',
            self::YEARLY => '/tahun',
        };
    }
}
