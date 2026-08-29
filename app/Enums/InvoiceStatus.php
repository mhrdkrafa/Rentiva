<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::CANCELLED => 'Dibatalkan',
            self::REFUNDED => 'Dikembalikan (Refund)',
            self::EXPIRED => 'Kedaluwarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'warning',
            self::PAID => 'success',
            self::CANCELLED, self::EXPIRED => 'danger',
            self::REFUNDED => 'info',
        };
    }
}
