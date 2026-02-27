<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // On charge 'sale.client' pour éviter les requêtes SQL en boucle (N+1 Problem)
        $query = Payment::with(['sale.client']);

        if ($request->search_client) {
            $query->whereHas('sale.client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_client . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // Récupérer les 5 derniers paiements pour le widget du haut
        $recentPayments = Payment::with(['sale.client'])->latest()->take(5)->get();

        return view('dashboards.accounting', compact('payments', 'recentPayments'));
    }

    public function store(Request $request)
    {
        // 1. Validation stricte
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50', // Nom corrigé
        ]);

        try {
            // 2. Utilisation d'une Transaction pour garantir l'intégrité des données
            // (On ne veut pas enregistrer le paiement si la mise à jour de la vente échoue)
            DB::transaction(function () use ($request) {

                // Création du paiement
                $payment = Payment::create([
                    'sale_id' => $request->sale_id,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method, // Correction du champ
                    'payment_date' => now(),
                ]);

                // 3. MISE À JOUR AUTO DES IMPAYÉS
                // On récupère la vente et on incrémente son champ paid_amount
                $sale = Sale::findOrFail($request->sale_id);
                $sale->increment('paid_amount', $request->amount);

                // Vérification si tout est payé
                if ($sale->paid_amount >= $sale->total) {
                    $sale->update(['status' => 'completed']);

                    if ($sale->client_order_id) {
                        \App\Models\ClientOrder::where('id', $sale->client_order_id)->update(['status' => 'completed']);
                    }
                }
            });

            return redirect()->back()->with('success', 'Paiement enregistré et solde client mis à jour.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function create()
    {
        $sales = Sale::with('client')->latest()->get();
        return view('payments.create', compact('sales'));
    }

    public function show(Payment $payment)
    {
        $payment->load('sale.client');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $sales = Sale::with('client')->latest()->get();
        return view('payments.edit', compact('payment', 'sales'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $oldSale = $payment->sale;
            if ($oldSale) {
                $oldSale->decrement('paid_amount', $payment->amount);
            }

            $payment->update([
                'sale_id' => $validated['sale_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => now(),
            ]);

            $newSale = Sale::findOrFail($validated['sale_id']);
            $newSale->increment('paid_amount', $validated['amount']);
        });

        return redirect()->route('payments.index')->with('success', 'Paiement modifié avec succès.');
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            // Avant de supprimer le paiement, on déduit le montant de la vente
            $sale = $payment->sale;
            if ($sale) {
                $sale->decrement('paid_amount', $payment->amount);
            }
            $payment->delete();
        });

        return redirect()->back()->with('success', 'Paiement annulé et dette client réajustée.');
    }

    // Export CSV optimisé
    public function export()
    {
        $payments = Payment::with(['sale.client'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="paiements_comptables.csv"',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'N° Vente', 'Client', 'Montant', 'Méthode', 'Date']);

            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->sale_id,
                    $p->sale->client->name ?? 'N/A',
                    $p->amount,
                    $p->payment_method,
                    $p->created_at->format('d/m/Y H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
