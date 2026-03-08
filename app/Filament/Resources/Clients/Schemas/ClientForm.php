<?php
namespace App\Filament\Resources\Clients\Schemas;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(table: 'clients', column: 'email', ignoreRecord: true),
                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->required(),
                TextInput::make('address')
                    ->label('Adresse')
                    ->required(),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->required()
                    ->minLength(8)
                    ->dehydrated(false), // ne pas sauvegarder dans la table clients
            ]);
    }
}
