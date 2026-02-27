<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Supporte un ou plusieurs rôles séparés par virgule.
     * Exemple d'utilisation dans web.php :
     *   'role:sales_manager'
     *   'role:sales_manager,sales_employee'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Non connecté → login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // 2. Aplatir les rôles (gère "sales_manager,sales_employee" en un seul argument)
        $allowed = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map(fn($r) => trim($r))
            ->toArray();

        // 3. Rôle non autorisé → 403 (page d'erreur claire, pas de redirect silencieux)
        if (!in_array($userRole, $allowed)) {
            abort(403, 'Accès interdit : vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
