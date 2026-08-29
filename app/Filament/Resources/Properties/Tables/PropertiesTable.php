<?php

namespace App\Filament\Resources\Properties\Tables;

use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Properti')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('owner.name')
                    ->label('Pemilik (Owner)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('propertyType.name')
                    ->label('Tipe Properti')
                    ->badge()
                    ->color('info'),

                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->searchable(),

                TextColumn::make('gender_policy')
                    ->label('Kebijakan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof GenderPolicy ? $state->label() : (GenderPolicy::tryFrom($state)?->label() ?? $state)),

                TextColumn::make('verification_status')
                    ->label('Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof VerificationStatus ? $state->label() : (VerificationStatus::tryFrom($state)?->label() ?? $state))
                    ->color(fn ($state): string => match ($state instanceof VerificationStatus ? $state->value : $state) {
                        'verified' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof PropertyStatus ? $state->label() : (PropertyStatus::tryFrom($state)?->label() ?? $state))
                    ->color(fn ($state): string => match ($state instanceof PropertyStatus ? $state->value : $state) {
                        'published' => 'success',
                        'pending_review' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('units_count')
                    ->label('Unit')
                    ->counts('units')
                    ->sortable(),

                IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('verification_status')
                    ->label('Filter Verifikasi')
                    ->options(collect(VerificationStatus::cases())->mapWithKeys(fn ($v) => [$v->value => $v->label()])),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(collect(PropertyStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),

                SelectFilter::make('property_type_id')
                    ->label('Filter Tipe')
                    ->options(PropertyType::pluck('name', 'id')),

                SelectFilter::make('location_id')
                    ->label('Filter Lokasi')
                    ->options(Location::pluck('name', 'id')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve_verification')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Property $record): bool => $record->verification_status !== VerificationStatus::VERIFIED)
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi & Publikasikan Properti')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui verifikasi properti ini? Properti akan otomatis berstatus publik.')
                    ->action(function (Property $record) {
                        $record->update([
                            'verification_status' => VerificationStatus::VERIFIED,
                            'status' => PropertyStatus::PUBLISHED,
                            'verified_at' => now(),
                            'published_at' => $record->published_at ?? now(),
                            'rejection_reason' => null,
                        ]);
                    }),

                Action::make('reject_verification')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Property $record): bool => $record->verification_status === VerificationStatus::PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Property $record, array $data) {
                        $record->update([
                            'verification_status' => VerificationStatus::REJECTED,
                            'status' => PropertyStatus::DRAFT,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
