<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case UNVERIFIED = 'unverified';
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::UNVERIFIED => 'Belum Terverifikasi',
            self::PENDING => 'Verifikasi Diajukan',
            self::VERIFIED => 'Terverifikasi (Verified)',
            self::REJECTED => 'Verifikasi Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNVERIFIED => 'gray',
            self::PENDING => 'warning',
            self::VERIFIED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
