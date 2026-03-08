<?php
namespace App\Filament\Resources\Clients\Pages;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('envoyer_lien_inscription')
                ->label("Envoyer un lien d'inscription")
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->form([
                    TextInput::make('email_destinataire')
                        ->label('Email du destinataire')
                        ->email()
                        ->required()
                        ->placeholder('exemple@email.com'),
                ])
                ->action(function (array $data) {
                    $lienInscription = route('register.client');
                    Mail::send(
                        'emails.lien_inscription',
                        ['lien' => $lienInscription],
                        function ($message) use ($data) {
                            $message
                                ->to($data['email_destinataire'])
                                ->subject('Invitation à rejoindre notre plateforme');
                        }
                    );
                    Notification::make()
                        ->title('Lien envoyé avec succès !')
                        ->success()
                        ->send();
                }),
        ];
    }

   protected function afterCreate(): void
{
    $client = $this->record;
    $plainPassword = $this->data['password'];

    // Vérifier si un user avec cet email existe déjà
    if (User::where('email', $client->email)->exists()) {
        Notification::make()
            ->title('Un compte existe déjà avec cet email !')
            ->warning()
            ->send();
        return;
    }

    // Vérifier si un client avec ce téléphone existe déjà
    if (\App\Models\Client::where('phone', $client->phone)
            ->where('id', '!=', $client->id)
            ->exists()) {
        Notification::make()
            ->title('Un client avec ce numéro de téléphone existe déjà !')
            ->warning()
            ->send();
        return;
    }

    User::create([
        'name'      => $client->name,
        'email'     => $client->email,
        'password'  => Hash::make($plainPassword),
        'client_id' => $client->id,
        'role'      => 'client',
    ]);

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

    Notification::make()
        ->title('Client créé avec succès !')
        ->success()
        ->send();
}
} // <-- accolade manquante ajoutée
