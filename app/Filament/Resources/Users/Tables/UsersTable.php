<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('role')
                    ->label('Peran (Role)')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof UserRole ? $state->label() : (UserRole::tryFrom($state)?->label() ?? $state))
                    ->color(fn ($state): string => match ($state instanceof UserRole ? $state->value : $state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'owner' => 'info',
                        'property_manager' => 'primary',
                        'tenant' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof UserStatus ? $state->value : $state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'suspended' => 'danger',
                        'inactive' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Peran')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(collect(UserStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),

                TrashedFilter::make(),
            ])
            ->recordActions([
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
