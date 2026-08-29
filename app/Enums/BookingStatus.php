<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case PAYMENT_PENDING = 'payment_pending';
    case CONFIRMED = 'confirmed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_APPROVAL => 'Menunggu Persetujuan Pemilik',
            self::APPROVED => 'Disetujui (Menunggu Pembayaran)',
            self::REJECTED => 'Pengajuan Ditolak',
            self::CANCELLED => 'Dibatalkan Penyewa',
            self::EXPIRED => 'Kadaluwarsa',
            self::PAYMENT_PENDING => 'Menunggu Konfirmasi Pembayaran',
            self::CONFIRMED => 'Sewa Terkonfirmasi (Resmi)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING_APPROVAL => 'warning',
            self::APPROVED => 'primary',
            self::REJECTED => 'danger',
            self::CANCELLED => 'gray',
            self::EXPIRED => 'gray',
            self::PAYMENT_PENDING => 'warning',
            self::CONFIRMED => 'success',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::REJECTED, self::CANCELLED, self::EXPIRED], true);
    }

    public function blocksDates(): bool
    {
        return in_array($this, [self::APPROVED, self::PAYMENT_PENDING, self::CONFIRMED], true);
    }
}
