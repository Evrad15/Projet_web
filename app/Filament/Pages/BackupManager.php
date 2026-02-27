<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use BackedEnum;

class BackupManager extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Sauvegardes';
    protected static ?string $title = 'Gestion des Sauvegardes';
    protected string $view = 'filament.pages.backup-manager';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runBackup')
                ->label('Lancer une sauvegarde')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('config:clear');

                    $exitCode = Artisan::call('backup:run --only-db');
                    $output = Artisan::output();

                    if ($exitCode === 0) {
                        Notification::make()
                            ->title('Sauvegarde réussie !')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('La sauvegarde a échoué')
                            ->body($output) // affiche le vrai message d'erreur
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    public function getBackups(): array
    {
        // On vérifie les deux dossiers possibles par précaution
        $files = Storage::disk('local')->allFiles('private/Laravel');

        // Si c'est vide, on tente aussi le dossier racine au cas où
        if (empty($files)) {
            $files = Storage::disk('local')->allFiles('Laravel');
        }

        return $files;
    }
}
