@extends('layouts.app')

@section('content')
<h1>Ajouter une commande</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('suppliers.store') }}" method="POST">
    @csrf
    <label>Produit:</label>
    <input type="text" name="name" value="{{ old('name') }}"><br><br>

    <label>Quantité:</label>
    <input type="number" name="quantity" value="{{ old('quantity') }}"><br><br>

    <label>:</label>
    <input type="text" name="phone" value="{{ old('phone') }}"><br><br>

    <button type="submit">Save</button>
</form>
@endsection
