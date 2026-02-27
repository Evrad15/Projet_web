<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->suffix('XAF'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('alert_threshold')
                    ->required()
                    ->numeric()
                    ->default(5),
            ]);
    }
}
