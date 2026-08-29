<?php

namespace App\Filament\Resources\Facilities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FacilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Fasilitas')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->label('Kategori Fasilitas')
                    ->options([
                        'property' => 'Fasilitas Umum Properti (Parkir, Dapur, CCTV)',
                        'room' => 'Fasilitas Khusus Kamar/Unit (AC, Kasur, Lemari)',
                        'general' => 'Fasilitas Tambahan / Layanan (WiFi, Laundry, Listrik)',
                    ])
                    ->required(),

                TextInput::make('icon')
                    ->label('Nama Ikon (Opsional)')
                    ->maxLength(50)
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
