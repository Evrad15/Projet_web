@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center my-4">
        <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <h2 class="fw-bold mb-0">Modifier le client : {{ $client->name }}</h2>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Nom complet</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Adresse Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Téléphone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="javascript:history.back()" class="btn btn-light">Annuler</a>
                            <button type="submit" class="btn btn-warning text-white px-4">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection