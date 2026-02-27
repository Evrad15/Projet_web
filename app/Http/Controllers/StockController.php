<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement; // N'oublie pas d'importer le modèle
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockController extends Controller
{
    // Afficher le dashboard stock
    public function index(Request $request)
    {
        // 1. On récupère les fournisseurs (Indispensable pour tes modals)
        $suppliers = User::where('role', 'supplier_manager')->get();

        // 2. On récupère les produits avec filtres
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'rupture' => $query->where('quantity', '<=', 0),
                'low' => $query->where('quantity', '>', 0)->where('quantity', '<', 5),
                'available' => $query->where('quantity', '>=', 5),
                default => null
            };
        }

        $products = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // 3. On génère les statistiques pour les compteurs du haut
        $stats = [
            'total_products' => Product::count(),
            'total_stock'    => Product::sum('quantity'),
            'stock_value'    => Product::selectRaw('SUM(price * quantity) as total')->value('total') ?? 0,
            'low_stock'      => Product::where('quantity', '<', 5)->count(),
        ];

        // 4. UN SEUL return à la fin avec toutes les variables nécessaires
        return view('dashboards.stock', compact('products', 'stats', 'suppliers'));
    }



    // Enregistrer un nouveau produit
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255|unique:products,name',
            'price'    => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $product = Product::create($request->only(['name', 'price', 'quantity', 'description']));

        // Enregistrement du stock initial
        if ($product->quantity > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
                'type'       => 'initial',
                'quantity'   => $product->quantity,
                'description' => 'Stock initial à la création'
            ]);
        }

        return redirect()->route('dashboard.stock')
            ->with('success', 'Produit ajouté avec succès !');
    }

    // Créer une nouvelle commande
    public function storeOrder(Request $request)
    {
        // 1. Validation stricte des données entrantes
        $validated = $request->validate([
            'supplier_manager_id' => 'required|exists:users,id',
            'delivery_date'       => 'required|date|after_or_equal:today',
            'products'            => 'required|array|min:1',
            'products.*.name'     => 'required|string',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // 2. Utilisation d'une transaction pour garantir l'intégrité des données.
            // Si une erreur survient (ex: produit non trouvé), tout est annulé.
            $order = DB::transaction(function () use ($validated) {
                // Création de la commande parente
                $order = Order::create([
                    'supplier_manager_id' => $validated['supplier_manager_id'],
                    'delivery_date'       => $validated['delivery_date'],
                    'status'              => 'En_cours', // Statut initial
                ]);

                // Boucle sur les produits envoyés par le formulaire
                foreach ($validated['products'] as $item) {
                    $nameToSearch = trim($item['name']);
                    $product = Product::where('name', $nameToSearch)->first();

                    if (!$product) {
                        throw new \Exception("Le produit '{$nameToSearch}' n'existe pas dans la base de données. Vérifiez l'orthographe.");
                    }

                    // Utilisation de la relation pour créer l'item. C'est plus sûr
                    // et ça évite les problèmes de MassAssignment sur 'order_id'.
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                    ]);
                }

                return $order;
            });

            // 5. Si tout s'est bien passé, on redirige avec un succès.
            return redirect()->route('dashboard.stock')
                ->with('success', 'Commande #' . $order->id . ' enregistrée avec succès !');
        } catch (\Exception $e) {
            Log::error('Echec creation commande stock', [
                'user_id' => auth()->id(),
                'supplier_manager_id' => $validated['supplier_manager_id'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'products_count' => isset($validated['products']) && is_array($validated['products'])
                    ? count($validated['products'])
                    : null,
                'exception' => $e->getMessage(),
            ]);

            // 6. En cas d'erreur, la transaction est automatiquement annulée.
            // On redirige l'utilisateur avec le message d'erreur.
            return redirect()->back()
                ->withInput() // Garde les données tapées dans le formulaire
                ->withErrors(['error' => $e->getMessage()]);
        }
    }


    // Dashboard commandes (CORRIGÉ POUR LE FILTRAGE)
    public function ordersDashboard(Request $request)
    {
        $hiddenIds = session()->get('hidden_orders', []);

        // 1. On prépare la requête des commandes (on garde "orders")
        $query = Order::whereNotIn('id', $hiddenIds)->with(['items.product', 'supplier']);

        // --- Tes filtres de recherche (Search, Status, Sort) restent identiques ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('supplier', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('items.product', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sortOrder = $request->get('sort', 'desc');
        $query->orderBy('delivery_date', $sortOrder);

        // 2. On récupère les commandes pour le tableau
        $orders = $query->paginate(10)->withQueryString();

        // 3. AJOUT : On récupère les fournisseurs pour la modal de modification
        // On va chercher dans la table User ceux qui ont le bon rôle
        $suppliers = \App\Models\User::where('role', 'supplier_manager')->get();

        // 4. On envoie les DEUX variables à la vue
        return view('dashboards.commandes', compact('orders', 'suppliers'));
    }


    // --- FONCTION SUPPRIMER ---
    public function destroyOrder($id)
    {
        $order = Order::findOrFail($id);

        // On supprime d'abord les items (si pas de cascade en BD) puis la commande
        $order->items()->delete();
        $order->delete();

        return redirect()->route('stock.order.dashboard')->with('success', 'Commande supprimée avec succès.');
    }

    // --- FONCTION LIVRER (Mise à jour Stock) ---
    public function deliverOrder($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        // Sécurité : Ne pas livrer une commande déjà livrée
        if ($order->status === 'livrée') {
            return redirect()->route('stock.order.dashboard')->with('error', 'Cette commande a déjà été réceptionnée.');
        }

        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->product) {
                    // AJOUT AU STOCK : On incrémente la quantité du produit
                    $item->product->increment('quantity', $item->quantity);

                    // HISTORIQUE : On enregistre le mouvement
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id'    => auth()->id(),
                        'type'       => 'supply', // Approvisionnement
                        'quantity'   => $item->quantity,
                        'description' => "Réception Commande Fournisseur #{$order->id}"
                    ]);
                }
            }

            // Mise à jour du statut
            $order->update(['status' => 'livrée']);
        });

        return redirect()->route('stock.order.dashboard')->with('success', 'Réception confirmée ! Le stock a été mis à jour.');
    }
    // Annuler plusieurs commandes (CORRIGÉ)
    public function bulkCancelOrder(Request $request)
    {
        $orderIds = $request->input('order_ids');

        if (!$orderIds || !is_array($orderIds)) {
            return redirect()->route('stock.order.dashboard')->with('error', 'Aucune commande sélectionnée.');
        }

        // Annuler uniquement les commandes qui sont en 'pending'
        Order::whereIn('id', $orderIds)
            ->where('status', 'En_cours')
            ->update(['status' => 'Annulée']);

        return redirect()->route('stock.order.dashboard')->with('success', 'Commandes sélectionnées annulées !');
    }

    // Masquer plusieurs commandes (CORRIGÉ : GESTION SESSION ROBUSTE)
    public function bulkHideOrder(Request $request)
    {
        $orderIds = $request->input('order_ids');

        if (!$orderIds || !is_array($orderIds)) {
            return redirect()->route('stock.order.dashboard')->with('error', 'Aucune commande sélectionnée.');
        }

        // Récupérer les IDs déjà masqués, fusionner et supprimer les doublons
        $existingHidden = session()->get('hidden_orders', []);
        $updatedHidden = array_unique(array_merge($existingHidden, $orderIds));

        // Enregistrer le tableau propre en session
        session(['hidden_orders' => $updatedHidden]);

        return redirect()->route('stock.order.dashboard')->with('success', count($orderIds) . ' commande(s) masquée(s).');
    }

    // Optionnel : Pour réinitialiser la vue et tout revoir
    public function resetHiddenOrders()
    {
        session()->forget('hidden_orders');
        return redirect()->route('stock.order.dashboard')->with('success', 'Toutes les commandes sont de nouveau visibles.');
    }

    // Affiche la page de modification
    public function editOrder($id) // Reçois l'ID brut (ex: 5)
    {
        // On cherche la commande manuellement
        $order = Order::with('items.product')->find($id);

        // Si on ne trouve rien, on évite l'erreur page blanche
        if (!$order) {
            return redirect()->route('stock.order.dashboard')->with('error', 'Commande introuvable.');
        }

        $suppliers = User::where('role', 'supplier_manager')->get();

        // Retourne ta vue exacte (vérifie bien le chemin du fichier blade)
        return view('stock.edit', compact('order', 'suppliers'));
    }


    // Traite la modification
    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Sécurité : Empêcher la modification d'une commande déjà validée/livrée
        if ($order->status === 'livrée') {
            return redirect()->route('stock.order.dashboard')->with('error', 'Une commande livrée ne peut plus être modifiée.');
        }

        $request->validate([
            'supplier_manager_id' => 'required|exists:users,id',
            'delivery_date'       => 'required|date',
            'products'            => 'required|array|min:1',
        ]);

        try {
            \DB::transaction(function () use ($request, $order) {

                // 1. Mise à jour de l'entête
                $order->update([
                    'supplier_manager_id' => $request->supplier_manager_id,
                    'delivery_date'       => $request->delivery_date,
                    // On garde le statut actuel si non fourni dans le form
                    'status'              => $request->status ?? $order->status,
                ]);

                // 2. Suppression et Recréation (Méthode radicale mais efficace)
                $order->items()->delete();

                foreach ($request->products as $item) {
                    $productName = trim($item['name'] ?? '');

                    if (empty($productName)) continue;

                    $product = Product::where('name', $productName)->first();

                    if (!$product) {
                        throw new \Exception("Le produit '{$productName}' n'existe pas.");
                    }

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                    ]);
                }
            });

            return redirect()->route('stock.order.dashboard')->with('success', 'Commande mise à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    // Afficher l'historique des mouvements
    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->paginate(20)->withQueryString();

        return view('dashboards.movements', compact('movements'));
    }

    // Récupérer les données pour le graphique statistique
    public function getChartData()
    {
        $startDate = \Carbon\Carbon::create(date('Y'), 2, 10);
        $endDate = $startDate->copy()->addMonth();

        // On récupère les mouvements groupés par jour et par type
        $movements = StockMovement::selectRaw('DATE(created_at) as date, type, SUM(quantity) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('type', ['sale', 'supply'])
            ->groupBy('date', 'type')
            ->get();

        $labels = [];
        $salesData = [];
        $supplyData = [];

        // On parcourt chaque jour de la période pour avoir un axe X continu
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');

            // Ventes (stockées en négatif, on prend la valeur absolue pour le graph)
            $sale = $movements->where('date', $formattedDate)->where('type', 'sale')->first();
            $salesData[] = $sale ? abs($sale->total) : 0;

            // Approvisionnements
            $supply = $movements->where('date', $formattedDate)->where('type', 'supply')->first();
            $supplyData[] = $supply ? $supply->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $salesData,
            'supplies' => $supplyData
        ]);
    }
}




