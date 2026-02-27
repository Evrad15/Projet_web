@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier la commande #{{ $order->id }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('stock.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fournisseur</label>
                        <select name="supplier_manager_id" class="form-select">
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $order->supplier_manager_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de livraison</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En cours</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livré</option>
                        </select>
                    </div>
                </div>

                <hr>
                <h5>Produits de la commande</h5>
                <div id="products-list">
                    @foreach($order->items as $index => $item)
                        <div class="row mb-2 product-row">
                            <div class="col-md-8">
                                <input type="text" name="products[{{ $index }}][name]" class="form-control" value="{{ $item->product->name ?? '' }}" placeholder="Nom du produit">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="products[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}" placeholder="Quantité">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                    <a href="{{ route('dashboard.stock') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Petit bonus JS pour supprimer une ligne de produit si besoin
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.product-row');
            if(rows.length > 1) {
                e.target.closest('.product-row').remove();
            } else {
                alert("Une commande doit avoir au moins un produit.");
            }
        }
    });
</script>
@endsection
