@extends('layouts.app')

@section('content')
<h1>Products</h1>
<a href="{{ route('products.create') }}">Add Product</a>

@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Supplier</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->price }}</td>
            <td>{{ $product->stock }}</td>
            <td>{{ $product->supplier ? $product->supplier->name : 'N/A' }}</td>
            <td>
                <a href="{{ route('products.edit', $product->id) }}">Edit</a>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
