@extends('layouts.app')

@section('content')
<h1>Payment Details</h1>

<p><strong>ID:</strong> {{ $payment->id }}</p>
<p><strong>Sale:</strong> Sale #{{ $payment->sale->id ?? 'N/A' }}</p>
<p><strong>Amount:</strong> {{ $payment->amount }}</p>
<p><strong>Method:</strong> {{ $payment->method }}</p>

<a href="{{ route('payments.edit', $payment->id) }}">Edit</a>
<a href="{{ route('payments.index') }}">Back to List</a>
@endsection
