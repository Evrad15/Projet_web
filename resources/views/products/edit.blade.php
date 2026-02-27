@extends('layouts.app')

@section('content')
<h1>Edit Product</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label>
    <input type="text" name="name" value="{{ old('name', $product->name) }}"><br><br>

    <label>Price:</label>
    <input type="text" name="price" value="{{ old('price', $product->price) }}"><br><br>

    <label>Stock:</label>
    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"><br><br>

    <label>Supplier:</label>
    <select name="supplier_id">
        <option value="">-- Select Supplier --</option>
        @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" @if($product->supplier_id == $supplier->id) selected @endif>{{ $supplier->name }}</option>
        @endforeach
    </select><br><br>

    <button type="submit">Update</button>
</form>
@endsection
