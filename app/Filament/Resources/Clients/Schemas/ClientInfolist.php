<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nom'),
                TextEntry::make('email')
                    ->label('Email')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label('Téléphone')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label('Adresse')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
