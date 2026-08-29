<?php

namespace App\Enums;

enum RentalStatus: string
{
    case PENDING_MOVE_IN = 'pending_move_in';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_MOVE_IN => 'Menunggu Jadwal Masuk (Check-in)',
            self::ACTIVE => 'Sewa Aktif Berjalan',
            self::COMPLETED => 'Sewa Selesai',
            self::TERMINATED => 'Sewa Berakhir Lebih Awal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING_MOVE_IN => 'primary',
            self::ACTIVE => 'success',
            self::COMPLETED => 'gray',
            self::TERMINATED => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING_MOVE_IN, self::ACTIVE], true);
    }
}
