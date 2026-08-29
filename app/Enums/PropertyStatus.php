<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case PUBLISHED = 'published';
    case SUSPENDED = 'suspended';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf (Draft)',
            self::PENDING_REVIEW => 'Menunggu Moderasi',
            self::PUBLISHED => 'Dipublikasikan (Aktif)',
            self::SUSPENDED => 'Ditangguhkan',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_REVIEW => 'warning',
            self::PUBLISHED => 'success',
            self::SUSPENDED => 'danger',
            self::ARCHIVED => 'gray',
        };
    }
}
