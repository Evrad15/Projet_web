<?php

namespace App\Http\Controllers;

use App\Models\ClientOrder;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientOrderController extends Controller
{
    // Liste des commandes du client
    public function index()
    {
        $clientId = Auth::user()->client_id;

        // 1. Commandes en attente (ClientOrder)
        // Ce sont les pré-commandes qui n'ont pas encore été transformées en vente.
        $pendingOrders = ClientOrder::where('client_id', $clientId)
            ->where('status', 'en attente')
            ->with('items.product')
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        // 2. Achats à crédit (dettes en cours, basées sur le modèle Sale)
        $creditSales = \App\Models\Sale::where('client_id', $clientId)
            ->whereRaw('COALESCE(paid_amount, 0) < total')
            ->with('items.product')
            ->latest()
            ->paginate(10, ['*'], 'credit_page');

        // 3. Achats terminés (ventes soldées, basées sur le modèle Sale)
        $completedSales = \App\Models\Sale::where('client_id', $clientId)
            ->whereRaw('COALESCE(paid_amount, 0) >= total')
            ->with('items.product')
            ->latest()
            ->paginate(10, ['*'], 'completed_page');

        return view('orders.index', compact('pendingOrders', 'creditSales', 'completedSales'));
    }

    // Afficher le formulaire de commande
    public function create()
    {
        $products = Product::where('quantity', '>', 0)->paginate(15);
        return view('orders.create', compact('products'));
    }

    // Enregistrer la commande
    public function store(Request $request)
    {
        // 1. Validation des données du formulaire
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $totalAmount = 0;

        try {
            DB::beginTransaction();

            // 2. Création de la vente principale
            $order = ClientOrder::create([
                'client_id' => Auth::user()->client_id ?? Auth::id(), // ID du client
                'order_number' => 'CMD-' . strtoupper(uniqid()), // Génération d'un numéro unique
                'total_amount' => 0, // Sera mis à jour après le calcul
                'status' => 'en attente', // Statut initial, pour traitement par un commercial
            ]);

            // 3. Boucle sur chaque article du panier pour les traiter
            foreach ($validated['products'] as $itemData) {
                $product = Product::find($itemData['id']);
                $quantity = $itemData['quantity'];

                // 3a. Vérification du stock disponible
                if ($product->quantity < $quantity) {
                    throw new \Exception("Stock insuffisant pour le produit : {$product->name}. Restant : {$product->quantity}");
                }

                // 3b. Création de l'article de vente (SaleItem) lié à la vente
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price, // On enregistre le prix au moment de la commande
                    'subtotal'   => $product->price * $quantity,
                ]);

                // 3c. Décrémentation du stock et enregistrement du mouvement
                $product->decrement('quantity', $quantity);
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => Auth::id(),
                    'type'       => 'sale',
                    'quantity'   => -$quantity, // Négatif car c'est une sortie de stock
                    'description' => "Commande client #{$order->id}"
                ]);

                // 3d. Mise à jour du montant total
                $totalAmount += $product->price * $quantity;
            }

            // 4. Mise à jour de la commande avec le montant total final
            $order->update(['total_amount' => $totalAmount]);

            DB::commit(); // Valide toutes les opérations si tout s'est bien passé

        } catch (\Exception $e) {
            DB::rollBack(); // Annule toutes les opérations en cas d'erreur
            return back()->with('error', $e->getMessage())->withInput();
        }

        // 5. Redirection avec un message de succès
        return redirect()->route('orders.index')->with('success', 'Votre commande a été envoyée avec succès !');
    }

    // Afficher les détails d'une commande
    public function show($id)
    {
        $order = ClientOrder::where('client_id', Auth::user()->client_id)->with('items.product')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Annuler et supprimer une commande client.
     *
     * @param  \App\Models\ClientOrder  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ClientOrder $order)
    {
        // 1. Vérifier que l'utilisateur est bien le propriétaire de la commande
        if ($order->client_id !== Auth::user()->client_id) {
            abort(403, 'Action non autorisée.');
        }

        // 2. Vérifier que la commande peut être annulée (par exemple, si elle est encore "en attente")
        if (strtolower($order->status) !== 'en attente') {
            return redirect()->route('orders.index')->with('error', 'Cette commande ne peut plus être annulée car elle est déjà en cours de traitement.');
        }

        try {
            DB::transaction(function () use ($order) {
                // 3. Parcourir les articles pour remettre le stock
                foreach ($order->items as $item) {
                    if ($item->product) {
                        // Incrémenter le stock du produit
                        $item->product->increment('quantity', $item->quantity);

                        // Tracer le mouvement de stock (retour/annulation)
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id'    => Auth::id(),
                            'type'       => 'return',
                            'quantity'   => $item->quantity, // Quantité positive car c'est un retour en stock
                            'description' => "Annulation commande client #{$order->id}"
                        ]);
                    }
                }

                // 4. Supprimer la commande et ses articles (grâce à la cascade en base de données)
                $order->delete();
            });
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Une erreur est survenue lors de l\'annulation de la commande.');
        }

        // 5. Rediriger avec un message de succès
        return redirect()->route('orders.index')->with('success', 'La commande a été annulée avec succès et les produits remis en stock.');
    }

    /**
     * Affiche une facture spécifique pour le client connecté.
     *
     * @param \App\Models\Sale $sale
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showInvoice(Sale $sale)
    {
        // 1. Vérifier que la facture (vente) appartient bien au client connecté.
        if ($sale->client_id !== Auth::user()->client_id) {
            abort(403, 'Accès non autorisé à cette facture.');
        }

        // 2. Retourner la vue d'impression qui est déjà utilisée par les employés.
        return view('sales.print', compact('sale'));
    }

    // Liste des factures
    public function indexInvoices()
    {
        // Une "facture" correspond à un "achat" finalisé, c'est-à-dire un enregistrement dans la table 'sales'.
        // On récupère donc les ventes (sales) du client connecté.
        $invoices = \App\Models\Sale::where('client_id', Auth::user()->client_id)
            ->latest() // Les plus récentes en premier
            ->paginate(10);

        return view('/clients/invoices', compact('invoices'));
    }
}
