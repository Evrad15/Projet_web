@extends('layouts.app')

@section('content')
<h1>Add Payment</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('payments.store') }}" method="POST">
    @csrf

    <label>Sale:</label>
    <select name="sale_id">
        <option value="">-- Select Sale --</option>
        @foreach($sales as $sale)
            <option value="{{ $sale->id }}">Sale #{{ $sale->id }}</option>
        @endforeach
    </select><br><br>

    <label>Amount:</label>
    <input type="text" name="amount" value="{{ old('amount') }}"><br><br>

    <label>Method:</label>
    <input type="text" name="method" value="{{ old('method') }}"><br><br>

    <button type="submit">Save</button>
</form>
@endsection
