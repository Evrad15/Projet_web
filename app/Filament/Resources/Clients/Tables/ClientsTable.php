<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Adresse')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Suppression d'un client : supprime aussi son User
                DeleteAction::make()
                    ->before(function ($record) {
                        User::where('client_id', $record->id)->delete();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Suppression en masse : supprime aussi les Users liés
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            $ids = $records->pluck('id');
                            User::whereIn('client_id', $ids)->delete();
                        }),
                ]),
            ]);
    }
}