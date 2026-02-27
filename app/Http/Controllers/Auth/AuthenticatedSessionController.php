<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche la vue de connexion.
     */
    public function create(Request $request): View
    {
        // Si l'URL contient "staff", on affiche la vue privée
        if ($request->is('staff/*')) {
            return view('auth.staff-login');
        }

        return view('auth.login');
    }

    /**
     * Gère la tentative de connexion.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        /**
         * SECURITÉ : On utilise redirect() au lieu de intended().
         * Pourquoi ? Pour éviter que si un Comptable a essayé d'aller sur l'URL du Stock,
         * il ne soit redirigé vers l'erreur après sa connexion.
         */
        return match ($user->role) {
            'admin'            => redirect()->route('filament.admin.pages.dashboard'),
            'stock_manager'    => redirect()->route('dashboard.stock'),
            'sales_manager'    => redirect()->route('dashboard.sales'),
            'sales_employee'   => redirect()->route('dashboard.sales_employee'),
            'accountant'       => redirect()->route('dashboard.accounting'),
            'client'           => redirect()->route('dashboards.clients'),
            'supplier_manager' => redirect()->route('supplier.orders'),
            default            => redirect()->route('dashboard'),
        };
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
