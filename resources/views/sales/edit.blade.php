@extends('layouts.app')

@section('content')
<h1>Edit Sale</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('sales.update', $sale->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Client:</label>
    <select name="client_id">
        @foreach($clients as $client)
            <option value="{{ $client->id }}" @if($sale->client_id == $client->id) selected @endif>{{ $client->name }}</option>
        @endforeach
    </select><br><br>

    <label>Status:</label>
    <input type="text" name="status" value="{{ old('status', $sale->status) }}"><br><br>

    <button type="submit">Update</button>
</form>
@endsection
