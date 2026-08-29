<?php

namespace App\Filament\Resources\WebsiteSettings;

use App\Filament\Resources\WebsiteSettings\Pages\EditWebsiteSetting;
use App\Filament\Resources\WebsiteSettings\Pages\ListWebsiteSettings;
use App\Models\WebsiteSetting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebsiteSettingResource extends Resource
{
    protected static ?string $model = WebsiteSetting::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan Website';

    protected static ?string $navigationLabel = 'Konfigurasi Website';

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Daftar Pengaturan Website';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Kunci Pengaturan (Key)')
                    ->disabled()
                    ->required(),

                Select::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Identitas & Informasi Umum',
                        'contact' => 'Kontak & Layanan Bantuan',
                        'social' => 'Media Sosial',
                        'seo' => 'SEO & Meta Tag',
                    ])
                    ->required(),

                TextInput::make('description')
                    ->label('Keterangan / Fungsi')
                    ->nullable(),

                Textarea::make('value')
                    ->label('Nilai Pengaturan (Value)')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label('Grup')
                    ->badge(),

                TextColumn::make('key')
                    ->label('Kunci (Key)')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('value')
                    ->label('Nilai Saat Ini')
                    ->limit(40),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebsiteSettings::route('/'),
            'edit' => EditWebsiteSetting::route('/{record}/edit'),
        ];
    }
}
