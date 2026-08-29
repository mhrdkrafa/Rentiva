<?php

namespace App\Enums;

enum RefundStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::COMPLETED => 'Dana Dikembalikan',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
