@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter une nouvelle vente</h1>

    <form action="{{ route('sales.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="client_id" class="form-label">Client</label>
            <select name="client_id" id="client_id" class="form-control" required>
                <option value="">-- Choisir un client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @error('client_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="product_id" class="form-label">Produit</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">-- Choisir un produit --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantité</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" name="total" id="total" class="form-control" step="0.01" required>
            @error('total')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('dashboard.sales') }}" class="btn btn-secondary">Annuler</a>
    </form>
    <style>
    .container h1 {
        margin-bottom: 30px;
        font-size: 28px;
    }

    .form-label {
        font-weight: 500;
    }

    .btn {
        min-width: 120px;
    }
</style>
</div>
@endsection
