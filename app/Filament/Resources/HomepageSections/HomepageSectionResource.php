<?php

namespace App\Filament\Resources\HomepageSections;

use App\Filament\Resources\HomepageSections\Pages\EditHomepageSection;
use App\Filament\Resources\HomepageSections\Pages\ListHomepageSections;
use App\Models\HomepageSection;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepageSectionResource extends Resource
{
    protected static ?string $model = HomepageSection::class;

    protected static string | \UnitEnum | null $navigationGroup = 'CMS & Konten';

    protected static ?string $navigationLabel = 'Seksi Beranda';

    protected static ?string $modelLabel = 'Seksi Beranda';

    protected static ?string $pluralModelLabel = 'Daftar Seksi Beranda';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('section_key')
                    ->label('Kode Seksi (Section Key)')
                    ->disabled()
                    ->required(),

                TextInput::make('title')
                    ->label('Judul Seksi')
                    ->nullable(),

                Textarea::make('subtitle')
                    ->label('Subjudul / Deskripsi')
                    ->rows(2)
                    ->nullable(),

                TextInput::make('order')
                    ->label('Urutan Tampilan')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_visible')
                    ->label('Tampilkan di Beranda')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('section_key')
                    ->label('Kode Seksi')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Judul Seksi')
                    ->limit(40),

                IconColumn::make('is_visible')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomepageSections::route('/'),
            'edit' => EditHomepageSection::route('/{record}/edit'),
        ];
    }
}
