<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            // Suppression depuis la page de détail : supprime aussi le User lié
            DeleteAction::make()
                ->before(function () {
                    User::where('client_id', $this->record->id)->delete();
                }),
        ];
    }
}