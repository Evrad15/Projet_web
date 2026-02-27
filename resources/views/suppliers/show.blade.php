@extends('layouts.app')

@section('content')
<h1>Supplier Details</h1>

<p><strong>Name:</strong> {{ $supplier->name }}</p>
<p><strong>Email:</strong> {{ $supplier->email }}</p>
<p><strong>Phone:</strong> {{ $supplier->phone }}</p>

<a href="{{ route('suppliers.edit', $supplier->id) }}">Edit</a>
<a href="{{ route('suppliers.index') }}">Back to List</a>
@endsection
