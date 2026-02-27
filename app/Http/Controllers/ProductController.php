<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;

class ProductController extends Controller
{
    // Liste tous les produits
    public function index()
    {
        // On redirige vers le contrôleur principal du Stock qui gère les stats et les fournisseurs
        return redirect()->route('dashboard.stock');
    }

    // Formulaire pour ajouter un produit
    public function create()
    {
        $products = Product::all();
        return view('products.create', compact('products'));
    }

    // Enregistre un nouveau produit
    public function store(Request $request)
    {
        // 1. Validation (toujours !)
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric',
            // ... tes autres règles
        ]);

        // 2. Enregistrement
        Product::create($request->all());

        // 3. REDIRECTION (C'est CA qui règle ton problème d'actualisation)
        return redirect()->route('dashboard.stock')
            ->with('success', 'Produit enregistré !');
    }


    // Affiche un produit
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // Formulaire pour modifier un produit
    public function edit(Product $product)
    {
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'suppliers'));
    }

    // Met à jour le produit
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        // On garde l'ancienne quantité pour calculer la différence
        $oldQuantity = $product->quantity;

        $product->update($validated);

        $difference = $product->quantity - $oldQuantity;

        if ($difference != 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
                'type'       => 'correction',
                'quantity'   => $difference,
                'description' => 'Correction manuelle (Inventaire)'
            ]);
        }

        return redirect()->route('dashboard.stock')->with('success', 'Produit mis à jour avec succès.');
    }

    // Supprime le produit
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('dashboard.stock')->with('success', 'Produit supprimé avec succès.');
    }
}
