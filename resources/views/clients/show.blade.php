@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">Détail du Client</h2>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($client->name, 0, 1) }}
                        </div>
                        <h3 class="fw-bold mb-1">{{ $client->name }}</h3>
                        <p class="text-muted mb-0">Client</p>
                    </div>

                    <div class="list-group list-group-flush text-start mb-4 rounded-3">
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Email</span>
                            <span class="fw-bold">{{ $client->email }}</span>
                        </div>
                        <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Téléphone</span>
                            <span class="fw-bold">{{ $client->phone ?? 'Non renseigné' }}</span>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning text-white">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection