<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Models\Menu;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Pengaturan Website';

    protected static ?string $navigationLabel = 'Menu Navigasi';

    protected static ?string $modelLabel = 'Menu Navigasi';

    protected static ?string $pluralModelLabel = 'Daftar Menu Navigasi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->label('Lokasi Penempatan (Location Key)')
                    ->required()
                    ->unique(Menu::class, 'location', ignoreRecord: true),

                Repeater::make('items')
                    ->relationship('items')
                    ->label('Daftar Tautan Menu')
                    ->schema([
                        TextInput::make('title')
                            ->label('Label Menu')
                            ->required(),

                        TextInput::make('url')
                            ->label('Alamat URL / Rute')
                            ->required(),

                        Select::make('target')
                            ->label('Target Tab')
                            ->options([
                                '_self' => 'Buka di Tab yang Sama (_self)',
                                '_blank' => 'Buka di Tab Baru (_blank)',
                            ])
                            ->default('_self'),

                        TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->orderColumn('order')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->badge(),

                TextColumn::make('items_count')
                    ->label('Jumlah Tautan')
                    ->counts('items'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
