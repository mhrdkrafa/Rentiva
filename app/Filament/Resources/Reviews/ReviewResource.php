<?php

namespace App\Filament\Resources\Reviews;

use App\Enums\ReviewModerationStatus;
use App\Filament\Resources\Reviews\Pages\EditReview;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Models\Review;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Katalog Properti';

    protected static ?string $navigationLabel = 'Ulasan & Rating';

    protected static ?string $modelLabel = 'Ulasan';

    protected static ?string $pluralModelLabel = 'Daftar Ulasan & Rating';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('moderation_status')
                    ->label('Status Moderasi')
                    ->options([
                        ReviewModerationStatus::PENDING->value => 'Menunggu Moderasi',
                        ReviewModerationStatus::APPROVED->value => 'Disetujui (Publik)',
                        ReviewModerationStatus::REJECTED->value => 'Ditolak',
                    ])
                    ->required(),

                TextInput::make('rating')
                    ->label('Rating Keseluruhan (1-5)')
                    ->numeric()
                    ->disabled(),

                Textarea::make('comment')
                    ->label('Ulasan Penyewa')
                    ->disabled()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('owner_reply')
                    ->label('Balasan Pemilik Kost')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.name')
                    ->label('Properti')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state)),

                TextColumn::make('comment')
                    ->label('Ulasan')
                    ->limit(40),

                TextColumn::make('moderation_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ReviewModerationStatus $state): string => $state->color()),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record) => $record->moderation_status !== ReviewModerationStatus::APPROVED)
                    ->action(fn (Review $record) => $record->update(['moderation_status' => ReviewModerationStatus::APPROVED])),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Review $record) => $record->moderation_status !== ReviewModerationStatus::REJECTED)
                    ->action(fn (Review $record) => $record->update(['moderation_status' => ReviewModerationStatus::REJECTED])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
