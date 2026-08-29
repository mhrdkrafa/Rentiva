<?php

namespace App\Enums;

enum IssueStatus: string
{
    case REPORTED = 'reported';
    case IN_REVIEW = 'in_review';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::REPORTED => 'Laporan Diterima',
            self::IN_REVIEW => 'Sedang Ditinjau Pemilik',
            self::IN_PROGRESS => 'Teknisi Sedang Menangani',
            self::RESOLVED => 'Perbaikan Selesai',
            self::CLOSED => 'Tiket Ditutup',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REPORTED => 'warning',
            self::IN_REVIEW => 'primary',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
            self::CLOSED => 'gray',
        };
    }

    public function isResolved(): bool
    {
        return in_array($this, [self::RESOLVED, self::CLOSED], true);
    }
}
