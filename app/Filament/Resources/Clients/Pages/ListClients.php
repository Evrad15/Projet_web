<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau client')
                ->icon('heroicon-o-plus'),
        ];
    }
}