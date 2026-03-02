<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        // On écoute l'événement de connexion
        Event::listen(Login::class, function (Login $event) {
            /** @var \App\Models\User|null $user */
            $user = \App\Models\User::find($event->user->getAuthIdentifier());

            if ($user) {
                activity('auth')
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('Connexion réussie');
            }
        });
        // Log de Déconnexion
        Event::listen(Logout::class, function (Logout $event) {
            /** @var \App\Models\User|null $user */
            $user = $event->user ? \App\Models\User::find($event->user->getAuthIdentifier()) : null;

            if ($user) { // On vérifie que l'utilisateur existe encore
                activity('auth')
                    ->performedOn($user)
                    ->causedBy($user)
                    ->log('Déconnexion effectuée');
            }
        });
    }
}
