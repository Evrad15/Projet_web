@extends('layouts.app')

@section('content')
<h1>Edit Supplier</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Name:</label>
    <input type="text" name="name" value="{{ old('name', $supplier->name) }}"><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"><br><br>

    <label>Phone:</label>
    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"><br><br>

    <button type="submit">Update</button>
</form>
@endsection
