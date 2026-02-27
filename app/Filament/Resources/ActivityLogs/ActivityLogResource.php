<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use Spatie\Activitylog\Models\Activity as ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use BackedEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Journal d\'activités';

    protected static ?string $recordTitleAttribute = 'description';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Journal')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Connexion réussie'     => 'success',
                        'Déconnexion effectuée' => 'warning',
                        'created'               => 'success',
                        'updated'               => 'info',
                        'deleted'               => 'danger',
                        default                 => 'gray',
                    }),

                Tables\Columns\TextColumn::make('causer.roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->color('warning')
                    ->placeholder('Système'),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Utilisateur')
                    ->placeholder('Système')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view'  => ViewActivityLog::route('/{record}'),
        ];
    }
}