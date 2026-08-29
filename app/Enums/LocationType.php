<?php

namespace App\Enums;

enum LocationType: string
{
    case PROVINCE = 'province';
    case CITY = 'city';
    case DISTRICT = 'district';
    case AREA = 'area';
    case CAMPUS = 'campus';
    case LANDMARK = 'landmark';

    public function label(): string
    {
        return match ($this) {
            self::PROVINCE => 'Provinsi',
            self::CITY => 'Kota / Kabupaten',
            self::DISTRICT => 'Kecamatan',
            self::AREA => 'Area / Kelurahan',
            self::CAMPUS => 'Kampus / Universitas',
            self::LANDMARK => 'Landmark / Transit Point',
        };
    }
}
