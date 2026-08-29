<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Berhasil (Settlement)',
            self::EXPIRED => 'Kedaluwarsa',
            self::FAILED => 'Gagal',
            self::REFUNDED => 'Dikembalikan (Refund)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::SETTLEMENT => 'success',
            self::EXPIRED, self::FAILED => 'danger',
            self::REFUNDED => 'info',
        };
    }
}
