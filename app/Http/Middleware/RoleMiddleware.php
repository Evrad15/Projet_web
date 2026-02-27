<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles)) {
            // Option 1: rediriger vers la page d'accueil
            return redirect('/home')->with('error', "Accès refusé : rôle non autorisé");

            // Option 2: ou abort 403
            // abort(403, 'Accès refusé');
        }

        return $next($request);
    }
}
