<?php

namespace App\Filament\Resources\Locations\Schemas;

use App\Enums\LocationType;
use App\Models\Location;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lokasi / Kota / Kampus')
                    ->required()
                    ->maxLength(255),

                Select::make('parent_id')
                    ->label('Induk Lokasi (Parent)')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Select::make('type')
                    ->label('Jenis Lokasi')
                    ->options(collect(LocationType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->label()]))
                    ->required(),

                TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->nullable(),

                TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
