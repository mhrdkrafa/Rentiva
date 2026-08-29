<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Facility;
use App\Models\Location;
use App\Models\PropertyType;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Properti / Kost')
                    ->required()
                    ->maxLength(255),

                Select::make('owner_id')
                    ->label('Pemilik (Owner)')
                    ->options(User::whereIn('role', [UserRole::OWNER, UserRole::SUPER_ADMIN, UserRole::ADMIN])->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('property_type_id')
                    ->label('Tipe Properti')
                    ->options(PropertyType::pluck('name', 'id'))
                    ->required(),

                Select::make('location_id')
                    ->label('Kota / Lokasi')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('gender_policy')
                    ->label('Kebijakan Penghuni')
                    ->options(collect(GenderPolicy::cases())->mapWithKeys(fn ($g) => [$g->value => $g->label()]))
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi Lengkap')
                    ->rows(4)
                    ->required(),

                Textarea::make('address')
                    ->label('Alamat Lengkap Properti')
                    ->rows(2)
                    ->required(),

                Select::make('public_location_precision')
                    ->label('Presisi Tampilan Lokasi Publik')
                    ->options([
                        'exact' => 'Alamat Tepat / Presisi',
                        'approximate' => 'Perkiraan Radius Area',
                        'area_only' => 'Hanya Nama Kelurahan / Kota',
                    ])
                    ->default('approximate')
                    ->required(),

                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(collect(VerificationStatus::cases())->mapWithKeys(fn ($v) => [$v->value => $v->label()]))
                    ->required(),

                Select::make('status')
                    ->label('Status Listing')
                    ->options(collect(PropertyStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                    ->required(),

                Toggle::make('featured')
                    ->label('Tampilkan sebagai Featured Listing')
                    ->default(false),

                CheckboxList::make('facilities')
                    ->label('Fasilitas Properti')
                    ->relationship('facilities', 'name')
                    ->columns(3),
            ]);
    }
}
