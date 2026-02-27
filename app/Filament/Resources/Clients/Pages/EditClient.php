<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Après la mise à jour du client, on synchronise le User lié.
     */
    protected function afterSave(): void
    {
        $client = $this->record;

        $user = User::where('client_id', $client->id)->first();

        if ($user) {
            $user->update([
                'name'  => $client->name,
                'email' => $client->email,
            ]);
        }
    }
}