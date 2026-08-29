<?php

namespace App\Filament\Resources\Facilities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Fasilitas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'property' => 'Properti',
                        'room' => 'Kamar/Unit',
                        'general' => 'Umum',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'property' => 'info',
                        'room' => 'primary',
                        'general' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('properties_count')
                    ->label('Dipakai di Properti')
                    ->counts('properties')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Kategori')
                    ->options([
                        'property' => 'Fasilitas Properti',
                        'room' => 'Fasilitas Kamar',
                        'general' => 'Fasilitas Umum',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
