<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupManager extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Sauvegardes';
    protected static ?string $title = 'Gestion des sauvegardes';
    protected static ?int $navigationSort = 9999;
    protected string $view = 'filament.pages.backup-manager';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runBackup')
                ->label('Lancer une sauvegarde')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (): void {
                    $tempPath = storage_path('app/backup-temp/temp');
                    File::ensureDirectoryExists($tempPath);

                    $systemRoot = getenv('SYSTEMROOT') ?: getenv('SystemRoot') ?: 'C:\\Windows';
                    $windir = getenv('WINDIR') ?: $systemRoot;
                    $comspec = getenv('COMSPEC') ?: getenv('ComSpec') ?: $windir.'\\System32\\cmd.exe';
                    $path = getenv('PATH') ?: '';

                    $baseEnv = [
                        'TMP' => $tempPath,
                        'TEMP' => $tempPath,
                        'TMPDIR' => $tempPath,
                        'SYSTEMROOT' => $systemRoot,
                        'SystemRoot' => $systemRoot,
                        'WINDIR' => $windir,
                        'COMSPEC' => $comspec,
                        'ComSpec' => $comspec,
                        'PATH' => $path,
                    ];

                    $result = Process::path(base_path())
                        ->env($baseEnv)
                        ->timeout(300)
                        ->run([PHP_BINARY, 'artisan', 'backup:run', '--only-db']);

                    $output = $this->normalizeUtf8(trim($result->output() . PHP_EOL . $result->errorOutput()));

                    if (! $result->successful() && str_contains($output, "Can't create TCP/IP socket")) {
                        $result = Process::path(base_path())
                            ->env(array_merge($baseEnv, [
                                'DB_HOST' => 'localhost',
                                'DB_PORT' => '3306',
                                'DB_SOCKET' => '',
                            ]))
                            ->timeout(300)
                            ->run([PHP_BINARY, 'artisan', 'backup:run', '--only-db']);

                        $output = $this->normalizeUtf8(trim($result->output() . PHP_EOL . $result->errorOutput()));
                    }

                    if ($result->successful()) {
                        Notification::make()
                            ->title('Sauvegarde reussie')
                            ->body($output !== '' ? $output : 'Sauvegarde terminee.')
                            ->success()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('La sauvegarde a echoue')
                        ->body($output !== '' ? $output : 'Verifiez les logs pour plus de details.')
                        ->danger()
                        ->persistent()
                        ->send();
                }),
        ];
    }

    public function getBackups(): array
    {
        $files = Storage::disk('local')->allFiles('private/Laravel');

        if (empty($files)) {
            $files = Storage::disk('local')->allFiles('Laravel');
        }

        usort($files, function (string $a, string $b): int {
            $aTime = Storage::disk('local')->lastModified($a) ?: 0;
            $bTime = Storage::disk('local')->lastModified($b) ?: 0;

            return $bTime <=> $aTime;
        });

        return $files;
    }

    public function deleteBackup(string $encodedPath): void
    {
        $path = base64_decode($encodedPath, true);

        if (! is_string($path) || $path === '') {
            Notification::make()
                ->title('Suppression impossible')
                ->body('Chemin de sauvegarde invalide.')
                ->danger()
                ->send();

            return;
        }

        $isAllowedPath = str_starts_with($path, 'private/Laravel/') || str_starts_with($path, 'Laravel/');
        if (! $isAllowedPath) {
            Notification::make()
                ->title('Suppression refusee')
                ->body('Ce fichier ne peut pas etre supprime.')
                ->danger()
                ->send();

            return;
        }

        if (! Storage::disk('local')->exists($path)) {
            Notification::make()
                ->title('Fichier introuvable')
                ->warning()
                ->send();

            return;
        }

        Storage::disk('local')->delete($path);

        Notification::make()
            ->title('Sauvegarde supprimee')
            ->success()
            ->send();
    }

    private function normalizeUtf8(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'UTF-8', 'UTF-8, Windows-1252, ISO-8859-1');
        if (is_string($converted) && mb_check_encoding($converted, 'UTF-8')) {
            return $converted;
        }

        $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);

        return is_string($converted) ? $converted : '';
    }
}
