<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                Select::make('role')
                    ->options([
                        'admin' => 'Administratreur',
                        'sales_manager' => 'Responsable Commercial',
                        'sales_employee' => 'Employé Commercial',
                        'stock_manager' => 'Responsable Stock',
                        'supplier_manager' => 'Responsable Fournisseur',
                        'accountant' => 'Comptable',
                    ])
                    ->required(),
            ]);
    }
}
