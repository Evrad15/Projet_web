<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // 1. Création du profil Client (Données métier)
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // 2. Création du compte Utilisateur (Accès connexion)
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'client_id' => $client->id,
        ]);

        // Si l'action vient d'un employé commercial, on le redirige sur son dashboard
        if (auth()->check() && auth()->user()->role === 'sales_employee') {
            return redirect()->route('dashboard.sales_employee')->with('success', 'Client "' . $client->name . '" ajouté. Vous pouvez maintenant créer une vente.');
        }

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function show(Client $client)
    {
        // Récupère l'historique des achats du client (les plus récents en premier)
        $sales = $client->sales()->with(['items.product', 'sales_employee'])->latest()->paginate(5);

        // Calcul du montant total dépensé par ce client (KPI Client)
        $totalSpent = $client->sales()->sum('total');

        return view('clients.show', compact('client', 'sales', 'totalSpent'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    public function checkSolvency(Client $client)
    {
        // Seuil de solvabilité (peut être mis en configuration)
        $solvencyThreshold = 500000; // 500 000 FCFA

        // Calcul du solde dû. Utilise `paid_amount` pour la performance.
        // Assurez-vous que la colonne `paid_amount` est bien mise à jour sur la table `sales` lors de chaque paiement.
        $balance = $client->sales()->sum(DB::raw('total - COALESCE(paid_amount, 0)'));

        return response()->json([
            'balance' => $balance,
            'balance_formatted' => number_format($balance, 0, ',', ' ') . ' FCFA',
            'is_over_limit' => $balance > $solvencyThreshold,
            'limit' => $solvencyThreshold,
            'limit_formatted' => number_format($solvencyThreshold, 0, ',', ' ') . ' FCFA',
        ]);
    }
}
