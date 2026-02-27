@extends('layouts.app')

@section('content')
<h1>Sales</h1>
<a href="{{ route('sales.create') }}">Add Sale</a>

@if(session('success'))
    <div style="color:green;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->client->name ?? 'N/A' }}</td>
            <td>{{ $sale->total }}</td>
            <td>{{ $sale->status }}</td>
            <td>
                <a href="{{ route('sales.edit', $sale->id) }}">Edit</a>
                <a href="{{ route('sales.show', $sale->id) }}">View</a>
                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
