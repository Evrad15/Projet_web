<?php

namespace App\Filament\Resources\Users\Pages;

use App\Mail\RegistrationInvite;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_registration_link')
                ->label('Inviter un membre du personnel')
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    TextInput::make('email')
                        ->label('Email de destination')
                        ->email()
                        ->required(),
                    \Filament\Forms\Components\Select::make('role')
                        ->label('Rôle à attribuer')
                        ->options([
                            'admin' => 'Administrateur',
                            'stock_manager' => 'Gestionnaire de Stock',
                            'accountant' => 'Comptable',
                            'sales_manager' => 'Responsable des Ventes',
                            'sales_employee' => 'Vendeur',
                            'supplier_manager' => 'Fournisseurs',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    // On inclut l'email ET le rôle dans le lien signé
                    $link = URL::temporarySignedRoute('register.employee', now()->addHours(24), [
                        'email' => $data['email'],
                        'role' => $data['role']
                    ]);

                    Mail::to($data['email'])->send(new RegistrationInvite($link));

                    Notification::make()
                        ->title('Invitation envoyée')
                        ->success()
                        ->send();
                }),
        ];
    }
}
