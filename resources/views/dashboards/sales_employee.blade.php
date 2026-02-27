@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

    .btn-action {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .floating-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1050;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        border: none;
        color: white;
        box-shadow: 0 4px 20px rgba(118, 75, 162, 0.4);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .floating-btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 25px rgba(118, 75, 162, 0.5);
    }

    .btn-badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #ef4444;
        color: white;
        border-radius: 50%;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        font-weight: bold;
        border: 2px solid white;
        transform: translate(25%, -25%);
    }

    .pulse {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0 rgba(118, 75, 162, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(118, 75, 162, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(118, 75, 162, 0);
        }
    }
</style>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Espace Commercial</h1>
            <p class="mb-0 opacity-75">Saisie des ventes et suivi client</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-light btn-action fw-bold">
                <i class="bi bi-people me-2"></i>Clients
            </a>
            <button type="button" id="manual-sale-btn" class="btn btn-light btn-action fw-bold text-primary" onclick="new bootstrap.Modal(document.getElementById('addSaleModal')).show()">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle Vente
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <!-- Cartes de statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Vos ventes du jour</h6>
                        <h4 class="fw-bold mb-0">{{ $todaySales }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Votre revenu du jour</h6>
                        <h4 class="fw-bold mb-0">{{ number_format($todayRevenue, 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche -->
    <form action="{{ route('dashboard.sales_employee') }}" method="GET" class="mb-4 d-flex justify-content-end">
        <div class="input-group" style="max-width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Rechercher par client ou produit..." value="{{ request('search') }}">
            @if(request('search'))
            <a href="{{ route('dashboard.sales_employee') }}" class="btn btn-secondary">Réinitialiser</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Client</th>
                            <th>Articles</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $sale->id }}</td>
                            <td>{{ $sale->client->name ?? 'N/A' }}</td>
                            <td>
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($sale->items as $item)
                                    <li>{{ $item->product->name ?? 'Produit supprimé' }} <span class="text-muted">(x{{ $item->quantity }})</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="fw-bold">{{ number_format($sale->total, 2) }} FCFA</td>
                            <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('sales.show', ['sale' => $sale->id, 'print' => 'true']) }}" class="btn btn-sm btn-outline-secondary" title="Imprimer le reçu">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($sale->created_at->diffInHours(now()) < 24)
                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette vente ? Le stock sera restauré.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler la vente">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    </form>
                                    @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Aucune vente enregistrée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $sales->links() }}
    </div>
</div>

<!-- Modal Nouvelle Vente -->
<div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
                @csrf
                <input type="hidden" name="client_order_id" id="client_order_id">

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addSaleModalLabel">
                        <i class="bi bi-cart-plus me-2 text-primary"></i>Enregistrer une nouvelle vente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm small mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label for="client_id" class="form-label small fw-bold text-muted text-uppercase">Client</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                            <select class="form-select bg-light border-0 py-2" id="client_id" name="client_id" required>
                                <option value="" selected disabled>Sélectionner un client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="solvency-alert-placeholder" class="mt-2"></div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <label class="form-label small fw-bold text-muted text-uppercase d-flex justify-content-between">
                        Articles du panier
                        <span class="badge bg-primary-subtle text-primary rounded-pill" id="item-count">0 article</span>
                    </label>

                    <div id="products-wrapper" class="mb-3">
                    </div>

                    <div class="d-flex justify-content-start mb-4">
                        <button type="button" id="add-product-row" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2">
                            <i class="bi bi-plus-lg me-1"></i> Ajouter une ligne
                        </button>
                        <button type="button" id="remove-product-row" class="btn btn-sm btn-outline-danger rounded-pill px-3" style="display:none;">
                            <i class="bi bi-dash-lg me-1"></i> Supprimer
                        </button>
                    </div>

                    <div class="card bg-light border-0 p-3" style="border-radius: 15px;">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label small fw-bold text-muted text-uppercase">Mode de règlement</label>
                                <select class="form-select border-0 py-2 shadow-sm" id="payment_method" name="payment_method" required>
                                    <option value="Espèces" selected>💵 Espèces</option>
                                    <option value="MTN Money">📱 MTN Money</option>
                                    <option value="ORANGE Money">📱 ORANGE Money</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="sale_type" class="form-label small fw-bold text-muted text-uppercase">Type de transaction</label>
                                <select class="form-select border-0 py-2 shadow-sm" id="sale_type" name="sale_type" required>
                                    <option value="comptant" selected>✅ Comptant (Soldé)</option>
                                    <option value="credit">⏳ À Crédit (Dette)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="amount_paid" class="form-label small fw-bold text-muted text-uppercase">Montant versé</label>
                                <div class="input-group shadow-sm rounded">
                                    <input type="number" class="form-control border-0 py-2" id="amount_paid" name="amount_paid" value="0" min="0" readonly required>
                                    <span class="input-group-text border-0 bg-white fw-bold small text-muted">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-6 text-end">
                                <div class="mb-1 text-muted small fw-bold text-uppercase">Total final</div>
                                <div class="fw-bold fs-3 text-dark"><span id="total-amount-display">0</span> <small class="fs-6">FCFA</small></div>
                                <div id="remaining-debt-container" class="mt-1" style="display:none;">
                                    <span class="badge bg-danger-subtle text-danger p-2 px-3 rounded-pill">
                                        Reste à payer : <span id="remaining-amount">0</span> FCFA
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span id="total-amount" style="display:none;">0</span>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">
                        <i class="bi bi-check-circle me-1"></i> Confirmer la vente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nouveau Client -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
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
                    <!-- Affichage des erreurs optimisé -->
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Oups ! Vérifiez vos informations.
                    </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Nom complet</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" name="name" value="{{ old('name') }}" placeholder="" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Adresse e-mail</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" class="form-control bg-light border-0 py-2" name="email" value="{{ old('email') }}" placeholder="" required>
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

<!-- Bouton Flottant Flux Commandes -->
@php $pendingGlobalCount = $availableSales->count(); @endphp
<button class="floating-btn {{ $pendingGlobalCount > 0 ? 'pulse' : '' }}" onclick="new bootstrap.Modal(document.getElementById('ordersQueueModal')).show()" title="Flux des commandes">
    <i class="bi bi-inbox-fill fs-4"></i>
    @if($pendingGlobalCount > 0)
    <span class="btn-badge">{{ $pendingGlobalCount }}</span>
    @endif
</button>

<!-- Modal Flux des Commandes (Globales vs Saisies) -->
<div class="modal fade" id="ordersQueueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-light rounded-circle me-3 d-none" id="btn-back-to-list" title="Retour à la liste">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </button>
                    <h5 class="modal-title fw-bold" id="ordersQueueModalLabel">Flux des Commandes</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- VUE 1 : LISTE DES COMMANDES -->
                <div id="orders-list-view">
                    <!-- Onglets -->
                    <ul class="nav nav-pills mb-4 nav-justified bg-light rounded-pill p-1" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-bold" id="pills-global-tab" data-bs-toggle="pill" data-bs-target="#pills-global" type="button" role="tab">
                                <i class="bi bi-globe me-2"></i>Commandes Globales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-bold" id="pills-taken-tab" data-bs-toggle="pill" data-bs-target="#pills-taken" type="button" role="tab">
                                <i class="bi bi-person-check me-2"></i>Mes Saisies
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu Onglets -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Onglet Global (Visible par tous) -->
                        <div class="tab-pane fade show active" id="pills-global" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Client</th>
                                            <th>Articles</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($availableSales as $sale)
                                        <tr>
                                            <td>{{ $sale->client->name }}</td>
                                            <td><span class="badge bg-secondary">{{ $sale->items->count() }}</span></td>
                                            <td class="fw-bold">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="small text-muted">{{ $sale->created_at->diffForHumans() }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 me-1 view-order-details"
                                                    data-order-ref="#{{ $sale->id }}"
                                                    data-client="{{ $sale->client->name }}"
                                                    data-items="{{ json_encode($sale->items->map(function($item){
                                                    return [
                                                        'name' => $item->product->name ?? 'Produit inconnu',
                                                        'quantity' => $item->quantity,
                                                        'price' => $item->unit_price ?? 0
                                                    ];
                                                })) }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                {{-- Formulaire pour "Saisir" la commande --}}
                                                <form action="{{ route('client-orders.assign', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment vous saisir de cette commande ?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                                        Saisir <i class="bi bi-clipboard-plus-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aucune commande en attente dans la file globale.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Onglet Saisies (Commandes de l'employé) -->
                        <div class="tab-pane fade" id="pills-taken" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Client</th>
                                            <th>Articles</th>
                                            <th>Total</th>
                                            <th>Statut</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($takenSales ?? [] as $sale)
                                        <tr>
                                            <td>{{ $sale->client->name }}</td>
                                            <td><span class="badge bg-secondary">{{ $sale->items->count() }}</span></td>
                                            <td class="fw-bold">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-info-subtle text-info text-capitalize">{{ $sale->status }}</span></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 me-1 view-order-details"
                                                    data-order-ref="#{{ $sale->id }}"
                                                    data-client="{{ $sale->client->name }}"
                                                    data-items="{{ json_encode($sale->items->map(function($item){
                                                    return [
                                                        'name' => $item->product->name ?? 'Produit inconnu',
                                                        'quantity' => $item->quantity,
                                                        'price' => $item->unit_price ?? 0
                                                    ];
                                                })) }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill convert-order-btn"
                                                    data-client-id="{{ $sale->client_id }}"
                                                    data-order-id="{{ $sale->id }}"
                                                    data-items="{{ json_encode($sale->items->map(function($item){
                                                    return [
                                                        'id' => $item->product_id,
                                                        'quantity' => $item->quantity
                                                    ];
                                                })) }}">
                                                    <i class="bi bi-cart-check-fill text-success"></i> ```
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Vous n'avez aucune commande en cours.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VUE 2 : DÉTAILS DE LA COMMANDE (Caché par défaut) -->
                <div id="order-details-view" class="d-none">
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="text-muted small text-uppercase mb-1">Client</h6>
                        <h5 class="fw-bold mb-0 text-primary" id="detail-client-name">Nom du client</h5>
                    </div>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-sm table-borderless mb-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-3">Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end pe-3">Prix Unitaire</th>
                                </tr>
                            </thead>
                            <tbody id="detail-items-body">
                                <!-- Rempli par JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Variables de couleurs */
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --soft-bg: #f8fafc;
    }

    /* Design du Modal */
    #addClientModal .modal-content {
        overflow: hidden;
    }

    /* Amélioration des Inputs */
    #addClientModal .form-control {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        /* Prévient le saut au focus */
    }

    #addClientModal .form-control:focus {
        background-color: #fff !important;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Labels stylés */
    #addClientModal .form-label {
        margin-bottom: 0.4rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    /* Bouton Gradient */
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

    /* Animation d'entrée */
    .modal.fade .modal-dialog {
        transform: scale(0.9);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DATA ---
        // On récupère les produits disponibles passés par le contrôleur
        @php
        $productsData = $products -> map(function($p) {
            return [
                'id' => $p -> id,
                'name' => $p -> name,
                'price' => $p -> price,
                'quantity' => $p -> quantity
            ];
        });
        @endphp
        const availableProducts = @json($productsData);


        // --- DOM ELEMENTS ---
        const wrapper = document.getElementById('products-wrapper');
        const addBtn = document.getElementById('add-product-row');
        const removeBtn = document.getElementById('remove-product-row');
        const totalAmountSpan = document.getElementById('total-amount');
        let productRowIndex = 0;

        // --- FUNCTIONS ---

        function resetSaleModal() {
            const saleForm = document.querySelector('#addSaleModal form');
            if (saleForm) saleForm.reset();

            const orderIdInput = document.getElementById('client_order_id');
            if (orderIdInput) orderIdInput.value = '';

            if (wrapper) wrapper.innerHTML = '';
            productRowIndex = 0;

            if (totalAmountSpan) totalAmountSpan.textContent = '0';
            if (removeBtn) removeBtn.style.display = 'none';

            const solvencyPlaceholder = document.getElementById('solvency-alert-placeholder');
            if (solvencyPlaceholder) solvencyPlaceholder.innerHTML = '';
        }

        function updateTotal() {
            let total = 0;
            const rows = wrapper.querySelectorAll('.product-row');
            rows.forEach(row => {
                const productSelect = row.querySelector('select');
                const quantityInput = row.querySelector('input[type="number"]');
                const selectedOption = productSelect.options[productSelect.selectedIndex];

                if (selectedOption && selectedOption.value && quantityInput.value > 0) {
                    const price = parseFloat(selectedOption.getAttribute('data-price'));
                    const quantity = parseInt(quantityInput.value, 10);
                    total += price * quantity;
                }
            });
            totalAmountSpan.textContent = new Intl.NumberFormat('fr-FR').format(total);
            totalAmountSpan.dataset.value = total; // Stocker la valeur brute
            totalAmountSpan.dispatchEvent(new Event('totalUpdated')); // Déclencher la mise à jour
        }

        function createProductRow(selectedProductId = null, selectedQuantity = null) {
            const index = productRowIndex++;
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 align-items-center product-row';

            // Création du select pour les produits
            const productOptions = availableProducts.map(p => {
                const isSelected = (selectedProductId && p.id == selectedProductId) ? 'selected' : '';
                return `<option value="${p.id}" data-price="${p.price}" ${isSelected}>${p.name} (Stock: ${p.quantity})</option>`;
            }).join('');

            const qtyValue = selectedQuantity || '';

            row.innerHTML = `
                <div class="col-md-8">
                    <select name="products[${index}][id]" class="form-select form-select-sm" required>
                        <option value="" disabled ${!selectedProductId ? 'selected' : ''}>Choisir un produit...</option>
                        ${productOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="products[${index}][quantity]" class="form-control form-control-sm" placeholder="Qté" min="1" value="${qtyValue}" required>
                </div>
            `;

            wrapper.appendChild(row);

            // Ajout des écouteurs pour mettre à jour le total
            row.querySelector('select').addEventListener('change', updateTotal);
            row.querySelector('input').addEventListener('input', updateTotal);

            // Gérer l'affichage du bouton de suppression
            removeBtn.style.display = wrapper.children.length > 0 ? 'inline-block' : 'none';
        }

        // --- EVENT LISTENERS ---

        // New listener for manual sale button
        const manualSaleBtn = document.getElementById('manual-sale-btn');
        if (manualSaleBtn) {
            manualSaleBtn.addEventListener('click', resetSaleModal);
        }

        addBtn.addEventListener('click', () => createProductRow());

        removeBtn.addEventListener('click', () => {
            if (wrapper.children.length > 0) {
                wrapper.removeChild(wrapper.lastElementChild);
                productRowIndex--;
                updateTotal();
            }
            if (wrapper.children.length === 0) {
                removeBtn.style.display = 'none';
            }
        });

        // Ajoute une première ligne au chargement de la modale
        document.getElementById('addSaleModal').addEventListener('shown.bs.modal', function() {
            if (wrapper.children.length === 0) {
                createProductRow();
            }
        });

        // --- MODAL ERROR HANDLING ---
        // On garde la logique pour afficher les modales en cas d'erreur
        @if($errors -> any() && !$errors -> has('name') && !$errors -> has('email'))
        var addSaleModal = new bootstrap.Modal(document.getElementById('addSaleModal'));
        addSaleModal.show();
        @endif

        @if($errors -> has('name') || $errors -> has('email'))
        var addClientModal = new bootstrap.Modal(document.getElementById('addClientModal'));
        addClientModal.show();
        @endif

        // --- LOGIQUE MODALE FLUX COMMANDES (Navigation dynamique) ---
        const listView = document.getElementById('orders-list-view');
        const detailsView = document.getElementById('order-details-view');
        const modalTitle = document.getElementById('ordersQueueModalLabel');
        const backBtn = document.getElementById('btn-back-to-list');
        const detailClientName = document.getElementById('detail-client-name');
        const detailItemsBody = document.getElementById('detail-items-body');

        // Clic sur "Voir produits" (l'oeil)
        document.querySelectorAll('.view-order-details').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderRef = this.dataset.orderRef;
                const clientName = this.dataset.client;
                const items = JSON.parse(this.dataset.items);

                // 1. Remplir les données
                modalTitle.textContent = `Détails Commande ${orderRef}`;
                detailClientName.textContent = clientName;

                detailItemsBody.innerHTML = '';
                items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="ps-3 py-2 fw-bold text-secondary">${item.name}</td>
                        <td class="text-center py-2"><span class="badge bg-white text-dark border">${item.quantity}</span></td>
                        <td class="text-end pe-3 py-2">${new Intl.NumberFormat('fr-FR').format(item.price)} FCFA</td>
                    `;
                    detailItemsBody.appendChild(row);
                });

                // 2. Basculer l'affichage
                listView.classList.add('d-none');
                detailsView.classList.remove('d-none');
                backBtn.classList.remove('d-none');
            });
        });

        // Clic sur "Retour" (la flèche)
        backBtn.addEventListener('click', function() {
            // 1. Basculer l'affichage inverse
            detailsView.classList.add('d-none');
            listView.classList.remove('d-none');
            backBtn.classList.add('d-none');

            // 2. Remettre le titre original
            modalTitle.textContent = 'Flux des Commandes';
        });

        // --- LOGIQUE CONVERSION COMMANDE -> VENTE ---
        document.querySelectorAll('.convert-order-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const clientId = this.dataset.clientId;
                const orderId = this.dataset.orderId;
                const items = JSON.parse(this.dataset.items);

                // Fonction pour ouvrir la modale de vente (à exécuter après la fermeture propre de la précédente)
                const openSaleModal = () => {
                    const addSaleModalEl = document.getElementById('addSaleModal');
                    const addSaleModal = bootstrap.Modal.getOrCreateInstance(addSaleModalEl);

                    const showModal = () => {
                        // Pré-remplir le client
                        document.getElementById('client_id').value = clientId;
                        document.getElementById('client_order_id').value = orderId;

                        // Vider et remplir les produits
                        wrapper.innerHTML = '';
                        productRowIndex = 0;
                        items.forEach(item => {
                            createProductRow(item.id, item.quantity);
                        });
                        updateTotal();
                        addSaleModal.show();
                    };

                    if (addSaleModalEl.classList.contains('show')) {
                        addSaleModalEl.addEventListener('hidden.bs.modal', showModal, {
                            once: true
                        });
                        addSaleModal.hide();
                    } else {
                        showModal();
                    }
                };

                // Gestion de la transition pour éviter le bug du backdrop (écran gris bloqué)
                const ordersQueueModalEl = document.getElementById('ordersQueueModal');
                const ordersQueueModal = bootstrap.Modal.getOrCreateInstance(ordersQueueModalEl);

                if (ordersQueueModalEl.classList.contains('show')) {
                    ordersQueueModalEl.addEventListener('hidden.bs.modal', openSaleModal, {
                        once: true
                    });
                    ordersQueueModal.hide();
                } else {
                    openSaleModal();
                }
            });
        });

        // Nettoyer le champ hidden quand la modale de vente se ferme pour éviter de supprimer une commande par erreur lors d'une future saisie manuelle
        document.getElementById('addSaleModal').addEventListener('hidden.bs.modal', function() {
            const orderIdInput = document.getElementById('client_order_id');
            if (orderIdInput) orderIdInput.value = '';
        });
    });

    // --- LOGIQUE VÉRIFICATION SOLVABILITÉ ---
    const clientSelect = document.getElementById('client_id');
    const solvencyPlaceholder = document.getElementById('solvency-alert-placeholder');

    if (clientSelect) {
        clientSelect.addEventListener('change', function() {
            const clientId = this.value;
            solvencyPlaceholder.innerHTML = ''; // Vider l'alerte précédente

            if (!clientId) return;

            fetch(`/clients/${clientId}/solvency`)
                .then(response => response.json())
                .then(data => {
                    if (data.is_over_limit) {
                        const alertDiv = `
                            <div class="alert alert-danger border-0 d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>Attention :</strong> Ce client a une dette de <strong>${data.balance_formatted}</strong>, dépassant la limite de ${data.limit_formatted}. La vente est risquée.
                                </div>
                            </div>
                        `;
                        solvencyPlaceholder.innerHTML = alertDiv;
                    } else if (data.balance > 0) {
                        const alertDiv = `
                            <div class="alert alert-warning border-0 d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                <div>
                                    Ce client a un solde impayé de <strong>${data.balance_formatted}</strong>.
                                </div>
                            </div>
                        `;
                        solvencyPlaceholder.innerHTML = alertDiv;
                    }
                })
                .catch(error => console.error('Erreur lors de la vérification de solvabilité:', error));
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
    const saleType = document.getElementById('sale_type');
    const amountPaid = document.getElementById('amount_paid');
    const totalSpan = document.getElementById('total-amount'); // Ton span actuel
    const totalDisplay = document.getElementById('total-amount-display');
    const remainingContainer = document.getElementById('remaining-debt-container');
    const remainingSpan = document.getElementById('remaining-amount');

    function updateCreditLogic() {
        // Récupérer la valeur brute depuis data-value, sinon nettoyer le texte formaté
        let total = parseFloat(totalSpan.dataset.value);
        if (isNaN(total)) {
            total = parseFloat(totalSpan.innerText.replace(/[\s\u00a0]/g, '').replace(',', '.')) || 0;
        }
        
        totalDisplay.innerText = new Intl.NumberFormat('fr-FR').format(total);

        if (saleType.value === 'comptant') {
            amountPaid.value = total;
            amountPaid.readOnly = true;
            remainingContainer.style.display = 'none';
        } else {
            amountPaid.readOnly = false;
            remainingContainer.style.display = 'block';
            
            const remaining = total - (parseFloat(amountPaid.value) || 0);
            remainingSpan.innerText = new Intl.NumberFormat('fr-FR').format(remaining > 0 ? remaining : 0);
        }
    }

    // Écouter les changements sur le type de vente
    saleType.addEventListener('change', updateCreditLogic);

    // Écouter quand l'employé saisit le montant versé
    amountPaid.addEventListener('input', updateCreditLogic);

    // Écouter l'événement personnalisé déclenché par updateTotal
    totalSpan.addEventListener('totalUpdated', updateCreditLogic);
    
    // Initialisation au chargement
    updateCreditLogic();
});

// Actualisation automatique de la page toutes les 30 secondes pour voir les nouvelles commandes
setInterval(function() {
    // On vérifie qu'aucune modale n'est ouverte (pour ne pas perdre de données en cours de saisie)
    if (!document.querySelector('.modal.show')) {
        window.location.reload();
    }
}, 30000); // 30000 ms = 30 secondes
</script>
@endsection