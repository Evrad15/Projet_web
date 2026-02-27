<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sales_employee.name')
                    ->label('Vendeur')
                    ->placeholder('-'),
                TextEntry::make('client.name')
                    ->label('Client'),
                TextEntry::make('total')
                    ->money('XAF')
                    ->label('Montant'),
                TextEntry::make('status'),
                RepeatableEntry::make('items')
                    ->label('Lignes de vente')
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Produit')
                            ->placeholder('-'),
                        TextEntry::make('quantity')
                            ->label('Qté'),
                        TextEntry::make('price')
                            ->label('Prix unitaire')
                            ->money('XAF'),
                        TextEntry::make('line_total')
                            ->label('Total ligne')
                            ->state(fn ($record) => (float) $record->quantity * (float) $record->price)
                            ->money('XAF'),
                    ])
                    ->columns(4),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
