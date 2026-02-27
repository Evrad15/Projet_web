<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // si tu utilises barryvdh/laravel-dompdf

class SupplierController extends Controller
{
    public function index(Request $request) // Affiche la liste des commandes du fournisseur
    {
        $search = $request->input('search');
        $showOld = $request->input('show_old'); // paramÃ¨tre pour afficher les commandes livrÃ©es

        // Base query : commandes du fournisseur connectÃ©
        $query = Order::where('supplier_manager_id', Auth::id());

        // Filtrer les commandes livrÃ©es si le paramÃ¨tre show_old n'est pas dÃ©fini
        if (!$showOld) {
            $query->where('status', '!=', 'livrÃ©e');
        }

        // Filtre par nom du produit
        if ($search) {
            $query->whereHas('items.product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Trier
        if ($request->sort_by == 'order_date') {
            $query->orderBy('created_at', 'desc'); // ordre dÃ©croissant : la plus rÃ©cente en premier
        } elseif ($request->sort_by == 'delivery_date') {
            $query->orderBy('delivery_date', 'asc'); // ordre croissant : la plus proche en premier
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Charger les items et pagination
        $orders = $query->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // conserve les paramÃ¨tres de recherche

        return view('dashboards.supplier', compact('orders', 'search', 'showOld'));
    }


    public function createOrder() // Affiche le formulaire de crÃ©ation de commande
    {
        // RÃ©cupÃ©rer tous les produits pour pouvoir les sÃ©lectionner
        $products = Product::all(); // ou filtrer si tu veux certains produits
        return view('suppliers.order.create_order', compact('products'));
    }

    public function showOrder(Order $order) // Affiche les dÃ©tails d'une commande
    {
        $this->authorizeOrder($order);

        $order->load('items.product');

        return view('supplier.show_order', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order) // Nouvelle mÃ©thode pour mettre Ã  jour le statut de la commande
    {
        $this->authorizeOrder($order);

        $request->validate([
            'status' => 'required|string|in:en attente,livrÃ©e,annulÃ©e',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('supplier.orders')->with('success', 'Statut de la commande mis Ã  jour.');
    }

    public function exportOrdersPdf(Request $request) // Nouvelle mÃ©thode pour exporter les commandes au format PDF
    {
        $query = Order::where('supplier_manager_id', Auth::id())
            ->with('items');

        // Masquer les commandes livrÃ©es par dÃ©faut
        if (!$request->show_old) {
            $query->where('status', '!=', 'livrÃ©e');
        }

        // Recherche par produit
        if ($request->search) {
            $query->whereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Tri
        if ($request->sort_by === 'delivery_date') {
            $query->orderBy('delivery_date', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->get();

        $pdf = Pdf::loadView('suppliers.orders_pdf', compact('orders'));

        return $pdf->download('commandes_fournisseur.pdf');
    }

    private function buildOrdersQuery(Request $request)
    {
        $query = Order::where('supplier_manager_id', Auth::id())
            ->with('items');

        // Masquer les commandes livrÃ©es par dÃ©faut
        if (!$request->show_old) {
            $query->where('status', '!=', 'livrÃ©e');
        }

        // Recherche par produit
        if ($request->search) {
            $query->whereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // TRI (identique Ã©cran / PDF)
        if ($request->sort_by === 'delivery_date') {
            // Livraison : plus proche â†’ plus Ã©loignÃ©e
            $query->orderBy('delivery_date', 'asc');
        } else {
            // Date commande : plus rÃ©cente â†’ plus ancienne
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * VÃ©rifie que la commande appartient bien au fournisseur connectÃ©
     */
    protected function authorizeOrder(Order $order)
    {
        if ($order->supplier_manager_id !== Auth::id()) {
            abort(403, "Vous n'Ãªtes pas autorisÃ© Ã  accÃ©der Ã  cette commande.");
        }
    }
}



