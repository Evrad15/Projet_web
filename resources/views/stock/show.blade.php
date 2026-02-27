@extends('layouts.app')

@section('content')
<h1>Sale Details</h1>

<p><strong>ID:</strong> {{ $sale->id }}</p>
<p><strong>Client:</strong> {{ $sale->client->name ?? 'N/A' }}</p>
<p><strong>Total:</strong> {{ $sale->total }}</p>
<p><strong>Status:</strong> {{ $sale->status }}</p>

<h3>Items</h3>
<ul>
    @foreach($sale->saleItems as $item)
        <li>{{ $item->product->name ?? 'Unknown' }} - Quantity: {{ $item->quantity }} - Price: {{ $item->price }}</li>
    @endforeach
</ul>

<a href="{{ route('sales.edit', $sale->id) }}">Edit</a>
<a href="{{ route('sales.index') }}">Back to List</a>
@endsection
