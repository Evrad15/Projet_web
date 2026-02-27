@extends('layouts.app')

@section('content')
<h1>Product Details</h1>

<p><strong>Name:</strong> {{ $product->name }}</p>
<p><strong>Price:</strong> {{ $product->price }}</p>
<p><strong>Stock:</strong> {{ $product->stock }}</p>
<p><strong>Supplier:</strong> {{ $product->supplier ? $product->supplier->name : 'N/A' }}</p>

<a href="{{ route('products.edit', $product->id) }}">Edit</a>
<a href="{{ route('products.index') }}">Back to List</a>
@endsection
