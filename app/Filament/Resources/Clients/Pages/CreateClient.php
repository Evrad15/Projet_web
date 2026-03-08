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
use Illuminate\Support\Str;

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
                        [
                            'lien' => $lienInscription,
                        ],
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
    $plainPassword = $this->data['password']; // récupère le mdp saisi

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
}
