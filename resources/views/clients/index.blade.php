@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    }

    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    /* Styles pour la modale d'ajout de client */
    #addClientModal .modal-content {
        border-radius: 24px;
        overflow: hidden;
    }

    #addClientModal .form-control {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    #addClientModal .form-control:focus {
        background-color: #fff !important;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    #addClientModal .form-label {
        margin-bottom: 0.4rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .btn-primary-gradient {
        background: var(--primary-gradient) !important;
        color: white;
        padding: 10px 24px;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .modal.fade .modal-dialog {
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>

<div class="d-flex">
    <!-- SIDEBAR -->
    @if(auth()->user()->role === 'sales_manager')
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end" style="width: 260px; min-height: calc(100vh - 65px);">
        <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <span class="fs-5 fw-bold text-primary"><i class="bi bi-grid-1x2-fill me-2"></i>Menu</span>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard.sales') }}" class="nav-link link-dark fw-bold">
                    <i class="bi bi-cart-check me-2"></i> Commandes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard.sales.employees') }}" class="nav-link link-dark fw-bold">
                    <i class="bi bi-people me-2"></i> Employés
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard.sales.performance') }}" class="nav-link link-dark fw-bold">
                    <i class="bi bi-graph-up-arrow me-2"></i> Performances
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('clients.index') }}" class="nav-link active fw-bold">
                    <i class="bi bi-person-badge me-2"></i> Clients
                </a>
            </li>
        </ul>
    </div>
    @endif

    <!-- CONTENT -->
    <div class="flex-grow-1 p-4" style="background-color: #f8f9fa;">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h1 class="fw-bold mb-1">Gestion des Clients</h1>
                    <p class="mb-0 opacity-75">Base de données clientèle</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-light text-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="bi bi-person-plus-fill me-2"></i>Nouveau Client
                </button>
            </div>
        </div>

        <form action="{{ route('clients.index') }}" method="GET" class="mb-4 d-flex justify-content-end">
            <div class="input-group" style="max-width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                @if(request('search'))
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Réinitialiser</a>
                @endif
            </div>
        </form>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $client->name }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->phone ?? '-' }}</td>
                                <td>{{ Str::limit($client->address, 40) ?? '-' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle me-1" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce client ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4">{{ $clients->links() }}</div>
    </div>

    <!-- Modal Nouveau Client -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 d-inline-block">
                            <i class="bi bi-person-plus-fill text-primary fs-4"></i>
                        </div>
                        <h5 class="modal-title fw-bold" id="addClientModalLabel">Nouveau Client</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 small mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Oups ! Vérifiez vos informations.
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 py-2" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Adresse e-mail</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" class="form-control bg-light border-0 py-2" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" class="form-control bg-light border-0 py-2" name="password" required placeholder="Minimum 8 caractères">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Adresse</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 py-2" name="address" value="{{ old('address') }}" placeholder="Ex: 123 Rue de la Paix, Douala">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-muted">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="text" class="form-control bg-light border-0 py-2" name="phone" value="{{ old('phone') }}" placeholder="+237 6 ...">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary-gradient border-0 fw-bold">
                            <span>Créer le profil</span> <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    @if($errors -> any())
    var addClientModal = new bootstrap.Modal(document.getElementById('addClientModal'));
    addClientModal.show();
    @endif
</script>
@endsection