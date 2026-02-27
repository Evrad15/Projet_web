@extends('layouts.app')

@section('content')
<div class="container">  
    <h2>Créer une nouvelle commande</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('stock.order.store_order') }}" method="POST">
        @csrf

       <div id="products-wrapper">
            <div class="product-row mb-2 d-flex gap-2 align-items-center"">
                <input type="text" name="products[0][name]" placeholder="Nom du produit" class="form-control mb-1" required>
                <input type="number" name="products[0][quantity]" placeholder="Quantité" class="form-control mb-1" required>
            </div>
        </div>

        <button type="button" id="add-product" class="btn btn-secondary mb-3">Ajouter un produit</button>
        <button type="button" id="remove-product" class="btn btn-danger mb-3" style="display:none;">Supprimer un produit</button>

        <div class="mb-3">
    <label for="supplier_manager_id" class="form-label">Fournisseur</label>
    <select name="supplier_manager_id" id="supplier_manager_id" class="form-control" required>
        <option value="">-- Choisir un fournisseur --</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ old('supplier_manager_id') == $supplier->id ? 'selected' : '' }}>
                {{ $supplier->name }}
            </option>
        @endforeach
    </select>
    @error('supplier_manager_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>


        <!-- Date de livraison -->
        <div class="mb-3">
            <label for="delivery_date" class="form-label">Date de livraison</label>
            <input type="date" id="delivery_date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}" required>
            @error('delivery_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary mt-3">Créer la commande</button>
    </form>
</div>
<script>
let productIndex = 1; // Commence après la ligne de base
const wrapper = document.getElementById('products-wrapper');
const removeBtn = document.getElementById('remove-product');

// Ajouter un produit
document.getElementById('add-product').addEventListener('click', function() {
    const newRow = document.createElement('div');
    newRow.classList.add('product-row', 'mb-2', 'd-flex', 'gap-2', 'align-items-center');

    newRow.innerHTML = `
        <input type="text" name="products[${productIndex}][name]" placeholder="Nom du produit" class="form-control" required>
        <input type="number" name="products[${productIndex}][quantity]" placeholder="Quantité" class="form-control" required>
    `;

    wrapper.appendChild(newRow);
    productIndex++;

    // Afficher le bouton supprimer si plus d'une ligne
    toggleRemoveButton();
});

// Supprimer la dernière ligne ajoutée
removeBtn.addEventListener('click', function() {
    const rows = wrapper.getElementsByClassName('product-row');

    if (rows.length > 1) {
        // Supprime la dernière ligne (toujours la dernière ajoutée)
        rows[rows.length - 1].remove();
        productIndex--;
    }

    // Vérifie si le bouton doit disparaître
    toggleRemoveButton();
});

// Fonction pour afficher/masquer le bouton
function toggleRemoveButton() {
    const rows = wrapper.getElementsByClassName('product-row');
    if (rows.length > 1) {
        removeBtn.style.display = 'inline-block';
    } else {
        removeBtn.style.display = 'none';
    }
}

// Initialiser l'état au chargement
toggleRemoveButton();
</script>

@endsection
