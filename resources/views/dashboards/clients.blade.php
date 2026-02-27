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
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Masquer la barre de défilement */
    ::-webkit-scrollbar {
        display: none;
    }
    body {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1">Mon Espace Client</h1>
                <p class="mb-0 opacity-75">Bienvenue, {{ Auth::user()->name }}</p>
            </div>
            <div>
                <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-person-circle me-2"></i>Compte Client
                </span>
            </div>
        </div>
    </div>

    <!-- KPIs Financiers -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100 p-3 bg-light">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small">Total Dépensé</h6>
                    <h2 class="fw-bolder display-5 my-2">{{ number_format($totalSpent ?? 0, 0, ',', ' ') }} <small class="fs-5">FCFA</small></h2>
                    <p class="small text-muted mb-0">Montant total de vos achats</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 p-3 {{ ($balance ?? 0) > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase small">Solde Actuel</h6>
                    <h2 class="fw-bolder display-5 my-2 {{ ($balance ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($balance ?? 0, 0, ',', ' ') }} <small class="fs-5">FCFA</small>
                    </h2>
                    <p class="small text-muted mb-0">
                        {{ ($balance ?? 0) > 0 ? 'Montant total à payer' : 'Votre compte est à jour, merci !' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row g-4 mb-5">
        <!-- Nouvelle Commande -->
        <div class="col-md-4">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-cart-plus"></i>
                    </div>
                    <h5 class="fw-bold">Passer une commande</h5>
                    <p class="text-muted small mb-4">Parcourez notre catalogue et ajoutez des produits à votre panier.</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary w-100 fw-bold rounded-pill">
                        Commander <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Mes Commandes -->
        <div class="col-md-4">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="icon-box bg-success-subtle text-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <h5 class="fw-bold">Mes Commandes</h5>
                    <p class="text-muted small mb-4">Suivez l'état de vos commandes en cours et leur livraison.</p>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-success w-100 fw-bold rounded-pill">
                        Voir l'historique
                    </a>
                </div>
            </div>
        </div>
        <!-- Factures -->
        <div class="col-md-4">
            <div class="card h-100 p-3">
                <div class="card-body">
                    <div class="icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <h5 class="fw-bold">Mes Factures</h5>
                    <p class="text-muted small mb-4">Consultez et téléchargez vos factures pour votre comptabilité.</p>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-warning w-100 fw-bold rounded-pill">
                        Accéder aux documents
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection