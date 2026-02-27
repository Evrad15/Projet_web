<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        return view('auth.register', ['email' => $request->query('email')]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
                'unique:clients,email',
            ],
            'phone' => ['required', 'string', 'max:20', 'unique:clients,phone'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.unique' => 'Ce numéro de téléphone est déjà enregistré.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $client = \App\Models\Client::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
            ]);

            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'client',
                'client_id' => $client->id,
            ]);
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboards.clients');
    }
}
