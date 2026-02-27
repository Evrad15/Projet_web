<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    /**
     * Après la création du client, on crée le User associé
     * avec un mot de passe généré automatiquement envoyé par email.
     */
    protected function afterCreate(): void
    {
        $client = $this->record;

        // Générer un mot de passe aléatoire
        $plainPassword = Str::password(12);

        // Créer le User lié au client
        User::create([
            'name'      => $client->name,
            'email'     => $client->email,
            'password'  => Hash::make($plainPassword),
            'client_id' => $client->id,
        ]);

        // Envoyer le mot de passe par email
        Mail::send(
            'emails.client_credentials',
            [
                'name'     => $client->name,
                'email'    => $client->email,
                'password' => $plainPassword,
            ],
            function ($message) use ($client) {
                $message
                    ->to($client->email, $client->name)
                    ->subject('Vos identifiants de connexion');
            }
        );
    }
}
