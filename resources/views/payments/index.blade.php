@extends('layouts.app')

@section('content')
<h1>Payments</h1>
<a href="{{ route('payments.create') }}">Add Payment</a>

@if(session('success'))
    <div style="color:green;">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sale</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr>
            <td>{{ $payment->id }}</td>
            <td>{{ $payment->sale->id ?? 'N/A' }}</td>
            <td>{{ $payment->amount }}</td>
            <td>{{ $payment->method }}</td>
            <td>
                <a href="{{ route('payments.edit', $payment->id) }}">Edit</a>
                <a href="{{ route('payments.show', $payment->id) }}">View</a>
                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline;">
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
