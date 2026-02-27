@extends('layouts.app')

@section('content')
<h1>Edit Payment</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('payments.update', $payment->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Sale:</label>
    <select name="sale_id">
        @foreach($sales as $sale)
            <option value="{{ $sale->id }}" @if($payment->sale_id == $sale->id) selected @endif>Sale #{{ $sale->id }}</option>
        @endforeach
    </select><br><br>

    <label>Amount:</label>
    <input type="text" name="amount" value="{{ old('amount', $payment->amount) }}"><br><br>

    <label>Method:</label>
    <input type="text" name="method" value="{{ old('method', $payment->method) }}"><br><br>

    <button type="submit">Update</button>
</form>
@endsection
