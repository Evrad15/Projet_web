@extends('layouts.app')

@section('content')
<h1>Suppliers</h1>
<a href="{{ route('suppliers.create') }}">Add Supplier</a>

@if(session('success'))
    <div style="color:green;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suppliers as $supplier)
        <tr>
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->email }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>
                <a href="{{ route('suppliers.edit', $supplier->id) }}">Edit</a>
                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
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
