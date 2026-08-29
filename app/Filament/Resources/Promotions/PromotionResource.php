<?php

namespace App\Filament\Resources\Promotions;

use App\Enums\DiscountType;
use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Models\Promotion;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Pemasaran & Promosi';

    protected static ?string $navigationLabel = 'Kupon & Voucher Promo';

    protected static ?string $modelLabel = 'Voucher Promo';

    protected static ?string $pluralModelLabel = 'Daftar Voucher Promo';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Kupon Promo (Voucher Code)')
                    ->required()
                    ->unique(Promotion::class, 'code', ignoreRecord: true)
                    ->maxLength(50)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),

                TextInput::make('name')
                    ->label('Nama Kampanye / Promo')
                    ->required()
                    ->maxLength(255),

                Select::make('discount_type')
                    ->label('Jenis Diskon')
                    ->options([
                        DiscountType::PERCENTAGE->value => 'Persentase (%)',
                        DiscountType::FIXED->value => 'Nominal Tetap (Rp)',
                    ])
                    ->default(DiscountType::PERCENTAGE->value)
                    ->required(),

                TextInput::make('discount_value')
                    ->label('Nilai Diskon (% atau Rp)')
                    ->numeric()
                    ->required(),

                TextInput::make('max_discount_amount')
                    ->label('Batas Maksimal Diskon (Rp) - Opsional untuk %')
                    ->numeric()
                    ->nullable(),

                TextInput::make('min_transaction_amount')
                    ->label('Minimal Transaksi (Rp)')
                    ->numeric()
                    ->default(0),

                DateTimePicker::make('starts_at')
                    ->label('Tanggal Mulai Promo')
                    ->nullable(),

                DateTimePicker::make('ends_at')
                    ->label('Tanggal Berakhir Promo')
                    ->nullable(),

                TextInput::make('max_uses')
                    ->label('Batas Total Pemakaian (Kuota)')
                    ->numeric()
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Promo')
                    ->badge()
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('name')
                    ->label('Nama Promo')
                    ->searchable(),

                TextColumn::make('discount_type')
                    ->label('Tipe')
                    ->badge(),

                TextColumn::make('used_count')
                    ->label('Terpakai')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }
}
