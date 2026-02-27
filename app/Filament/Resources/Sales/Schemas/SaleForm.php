<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sales_employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('client_id')
                    ->required()
                    ->numeric(),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('completed'),
            ]);
    }
}
