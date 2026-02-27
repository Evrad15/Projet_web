<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeRegistrationController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.employee-register');
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Lien d’invitation invalide ou expiré.');
        }

        $signedEmail = (string) $request->query('email', '');
        $signedRole = (string) $request->query('role', '');
        $allowedRoles = ['admin', 'stock_manager', 'accountant', 'sales_manager', 'sales_employee', 'supplier_manager'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        validator(
            ['email' => $signedEmail, 'role' => $signedRole],
            [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', Rule::in($allowedRoles)],
            ]
        )->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $signedEmail,
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $signedRole,
            'client_id' => null,
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Compte employé créé avec succès. Connectez-vous.');
    }
}
