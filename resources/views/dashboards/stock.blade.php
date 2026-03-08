@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* ===================== PAGE HEADER ===================== */
    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .page-header h1 {
        font-size: clamp(1.3rem, 4vw, 2rem);
    }

    /* Sur mobile : header en colonne */
    @media (max-width: 767px) {
        .page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .page-header .header-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            width: 100%;
        }

        .page-header .header-actions .btn {
            font-size: 0.75rem;
            padding: 0.45rem 0.6rem;
            white-space: nowrap;
            justify-content: center;
            display: flex;
            align-items: center;
            gap: 4px;
        }
    }

    /* ===================== CARDS ===================== */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    /* ===================== STATS GRID ===================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 991px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .stats-grid .card-body {
            padding: 0.85rem !important;
        }

        .stats-grid .stat-icon {
            padding: 0.6rem !important;
        }

        .stats-grid h4 {
            font-size: 1.1rem;
        }
    }

    /* ===================== FILTRES / RECHERCHE ===================== */
    @media (max-width: 767px) {
        .filter-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem;
        }

        .filter-bar select,
        .filter-bar .input-group {
            width: 100% !important;
        }
    }

    /* ===================== TABLEAU → CARDS MOBILE ===================== */
    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    /* Masquer le tableau sur mobile, afficher les cartes */
    @media (max-width: 767px) {
        .desktop-table { display: none !important; }
        .mobile-cards  { display: flex !important; flex-direction: column; gap: 0.75rem; padding: 0.75rem; }
    }
    @media (min-width: 768px) {
        .desktop-table { display: block !important; }
        .mobile-cards  { display: none !important; }
    }

    /* Carte produit mobile */
    .product-mobile-card {
        background: #fff;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 4px 10px;
    }

    .product-mobile-card .prod-id {
        font-size: 0.7rem;
        color: #94a3b8;
        font-family: monospace;
        font-weight: 600;
    }

    .product-mobile-card .prod-name {
        font-weight: 700;
        font-size: 0.97rem;
        color: #1e293b;
    }

    .product-mobile-card .prod-desc {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 6px;
    }

    .product-mobile-card .prod-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .product-mobile-card .meta-pill {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 2px 8px;
        font-size: 0.72rem;
        color: #475569;
        font-weight: 500;
    }

    .product-mobile-card .prod-date {
        font-size: 0.68rem;
        color: #94a3b8;
        text-align: right;
        margin-bottom: 6px;
    }

    .product-mobile-card .prod-actions {
        grid-row: 1 / span 3;
        grid-column: 2;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
    }

    /* ===================== BOUTONS ===================== */
    .btn-action {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    /* ===================== BADGES STATUT ===================== */
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* ===================== MODAL ===================== */
    .modal-content {
        border: none;
        border-radius: 15px;
    }

    /* Sur mobile : modal plein bas */
    @media (max-width: 575px) {
        .modal-dialog:not(.modal-xl) {
            margin: 0;
            align-items: flex-end;
            min-height: 100%;
            display: flex;
        }

        .modal-dialog:not(.modal-xl) .modal-content {
            border-radius: 20px 20px 0 0;
            width: 100%;
        }

        .modal-xl .modal-content {
            border-radius: 15px;
            margin: 1rem;
        }
    }

    /* ===================== PRODUCT ROW (commande) ===================== */
    .product-row {
        background: #fdfdfd;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    @media (max-width: 575px) {
        .product-row {
            flex-direction: column !important;
            gap: 0.5rem !important;
        }

        .product-row > div[style*="width: 120px"] {
            width: 100% !important;
        }
    }

    /* ===================== DROPDOWN ===================== */
    .custom-dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        font-size: 0.875rem;
        color: #4a5568;
        white-space: nowrap;
        background: transparent;
        border: none;
        width: 100%;
        cursor: pointer;
    }

    .custom-dropdown-menu .dropdown-item:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
    }

    .custom-dropdown-menu form {
        margin: 0;
        padding: 0;
    }

    /* ===================== SUGGESTION ===================== */
    .suggestion-container { position: relative; }

    .suggestion-menu {
        position: absolute;
        top: 100%; left: 0;
        width: 100%;
        background-color: white;
        z-index: 2000;
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        display: none;
    }

    .suggestion-menu div {
        padding: 10px 15px;
        cursor: pointer;
        font-size: 13px;
        color: #4a5568;
        border-bottom: 1px solid #f8fafc;
    }

    .suggestion-menu div:hover {
        background-color: #f0f4ff;
        color: #667eea;
    }

    .error-msg {
        font-size: 9px;
        position: absolute;
        bottom: -15px;
        left: 5px;
    }
</style>

<div class="container py-4">

    <!-- ================= HEADER ================= -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Inventaire</h1>
            <p class="mb-0 opacity-75">Gérez vos produits et approvisionnements en temps réel</p>
        </div>
        <div class="header-actions d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-light btn-action fw-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-box-seam"></i> <span>+ Produit</span>
            </button>
            <button class="btn btn-outline-light btn-action fw-bold" data-bs-toggle="modal" data-bs-target="#statsModal">
                <i class="bi bi-graph-up-arrow"></i> <span>Flux & Stats</span>
            </button>
            <a href="{{ route('stock.order.dashboard') }}" class="btn btn-light btn-action fw-bold text-primary">
                <i class="bi bi-cart-check"></i> <span>Commandes</span>
            </a>
            <button class="btn btn-dark btn-action" data-bs-toggle="modal" data-bs-target="#orderModal">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Nouvelle </span>Commande
            </button>
        </div>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @php
    $hasOrderErrors = $errors->has('error')
        || $errors->has('supplier_manager_id')
        || $errors->has('delivery_date')
        || collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'products.'));
    @endphp

    @if($hasOrderErrors)
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        @if($errors->has('error'))
            <div>{{ $errors->first('error') }}</div>
        @endif
        @if($errors->any())
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                @if($error !== $errors->first('error'))
                <li>{{ $error }}</li>
                @endif
            @endforeach
        </ul>
        @endif
    </div>
    @endif

    <!-- ================= STATS CARDS ================= -->
    <div class="stats-grid">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Produits distincts</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['total_products'] }}</h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                    <i class="bi bi-stack fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Volume de stock</h6>
                    <h4 class="fw-bold mb-0">{{ $stats['total_stock'] }}</h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon flex-shrink-0 bg-info-subtle text-info p-3 rounded-3 me-3">
                    <i class="bi bi-currency-dollar fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Valeur estimée</h6>
                    <h4 class="fw-bold mb-0" style="font-size: clamp(0.9rem, 2.5vw, 1.3rem);">
                        {{ number_format($stats['stock_value'], 2) }} FCFA
                    </h4>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm {{ $stats['low_stock'] > 0 ? 'bg-danger-subtle' : '' }}">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon flex-shrink-0 {{ $stats['low_stock'] > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary' }} p-3 rounded-3 me-3">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted small mb-1">Alertes stock bas</h6>
                    <h4 class="fw-bold mb-0 {{ $stats['low_stock'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= FILTRES ================= -->
    <div class="d-flex justify-content-end mb-4">
        <form method="GET" action="{{ route('dashboard.stock') }}" class="filter-bar d-flex gap-2 align-items-center w-100 justify-content-end">
            <select name="status" class="form-select border-0 shadow-sm" style="width: 180px;" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                <option value="low"       {{ request('status') == 'low'       ? 'selected' : '' }}>Stock Bas</option>
                <option value="rupture"   {{ request('status') == 'rupture'   ? 'selected' : '' }}>Rupture</option>
            </select>
            <div class="input-group shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0" placeholder="Rechercher un produit..." value="{{ request('search') }}">
            </div>
        </form>
    </div>

    <!-- ================= TABLEAU (desktop) ================= -->
    <div class="card desktop-table">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nom du Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Statut</th>
                            <th>Ajouté le</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border-0 py-2 px-3 fw-bold" style="border-radius:8px;font-family:monospace;">
                                    {{ $product->formatted_id }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $product->name }}</div>
                                <small class="text-muted">{{ Str::limit($product->description, 40) ?? 'Aucune description' }}</small>
                            </td>
                            <td><span class="fw-bold">{{ $product->quantity }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ number_format($product->price, 2) }} Fcfa</span></td>
                            <td>
                                @if($product->quantity <= 0)
                                    <span class="status-badge bg-danger-subtle text-danger">Rupture</span>
                                @elseif($product->quantity < 5)
                                    <span class="status-badge bg-warning-subtle text-warning">Stock Bas</span>
                                @else
                                    <span class="status-badge bg-success-subtle text-success">Disponible</span>
                                @endif
                            </td>
                            <td>{{ $product->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="custom-dropdown" style="position:relative;display:inline-block;">
                                    <button class="btn btn-link text-dark p-0" type="button" onclick="toggleDropdown(this)">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="custom-dropdown-menu border-0 shadow-lg" style="display:none;position:absolute;right:0;top:100%;z-index:9999;background:#fff;border-radius:10px;min-width:160px;padding:6px 0;list-style:none;margin:0;">
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                onclick="openEditModal(this); return false;"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-quantity="{{ $product->quantity }}"
                                                data-description="{{ $product->description ?? '' }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i> Modifier
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent"
                                                    onclick="return confirm('Supprimer ce produit ?')">
                                                    <i class="bi bi-trash me-2"></i> Supprimer
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Aucun produit en stock. Créez une commande pour commencer.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= CARTES PRODUITS (mobile) ================= -->
    <div class="mobile-cards">
        @forelse($products as $product)
        <div class="product-mobile-card">
            <div>
                <div class="prod-id">{{ $product->formatted_id }}</div>
                <div class="prod-name">{{ $product->name }}</div>
                <div class="prod-desc">{{ Str::limit($product->description, 50) ?? 'Aucune description' }}</div>
                <div class="prod-meta">
                    <span class="meta-pill">Qté : <strong>{{ $product->quantity }}</strong></span>
                    <span class="meta-pill">{{ number_format($product->price, 2) }} FCFA</span>
                    @if($product->quantity <= 0)
                        <span class="status-badge bg-danger-subtle text-danger">Rupture</span>
                    @elseif($product->quantity < 5)
                        <span class="status-badge bg-warning-subtle text-warning">Stock Bas</span>
                    @else
                        <span class="status-badge bg-success-subtle text-success">Disponible</span>
                    @endif
                </div>
            </div>
            <div class="prod-actions">
                <div class="prod-date">{{ $product->created_at->format('d M Y') }}</div>
                <div class="custom-dropdown" style="position:relative;display:inline-block;">
                    <button class="btn btn-link text-dark p-0" type="button" onclick="toggleDropdown(this)">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="custom-dropdown-menu border-0 shadow-lg" style="display:none;position:absolute;right:0;top:100%;z-index:9999;background:#fff;border-radius:10px;min-width:160px;padding:6px 0;list-style:none;margin:0;">
                        <li>
                            <a class="dropdown-item" href="#"
                                onclick="openEditModal(this); return false;"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-quantity="{{ $product->quantity }}"
                                data-description="{{ $product->description ?? '' }}">
                                <i class="bi bi-pencil me-2 text-primary"></i> Modifier
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent"
                                    onclick="return confirm('Supprimer ce produit ?')">
                                    <i class="bi bi-trash me-2"></i> Supprimer
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">Aucun produit en stock. Créez une commande pour commencer.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>

<!-- ================= MODAL AJOUT PRODUIT ================= -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;background:#ffffff;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Enregistrer un Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('stock.products.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true;" autocomplete="new-password">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nom du produit</label>
                        <input type="text" name="name" class="form-control bg-light border-0 rounded-3" placeholder="Entrez un produit" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Prix Unitaire</label>
                            <div class="input-group">
                                <input type="number" name="price" step="0.01" class="form-control bg-light border-0" placeholder="0.00" required>
                                <span class="input-group-text bg-light border-0 text-muted small">FCFA</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Stock Initial</label>
                            <input type="number" name="quantity" class="form-control bg-light border-0 rounded-3" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Description (Optionnel)</label>
                        <textarea name="description" class="form-control bg-light border-0 rounded-3" rows="2" placeholder="Détails..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm" style="border-radius:12px;background:var(--primary-gradient);border:none;">
                            <i class="bi bi-check-lg me-1"></i> Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL MODIFICATION PRODUIT ================= -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;background:#ffffff;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Modifier le Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProductForm" method="POST" autocomplete="off" data-action="{{ route('products.update', ':id') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nom du produit</label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-light border-0 rounded-3" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Prix Unitaire</label>
                            <div class="input-group">
                                <input type="number" name="price" id="edit_price" step="0.01" class="form-control bg-light border-0" required>
                                <span class="input-group-text bg-light border-0 text-muted small">FCFA</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Stock Actuel</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control bg-light border-0 rounded-3" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Description</label>
                        <textarea name="description" id="edit_description" class="form-control bg-light border-0 rounded-3" rows="2"></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning py-2 fw-bold shadow-sm text-white" style="border-radius:12px;border:none;">
                            <i class="bi bi-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL STATISTIQUES ================= -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Analyse des Flux</h5>
                    <p class="text-muted small mb-0">Comparatif Ventes vs Approvisionnements (30 derniers jours)</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="position-relative" style="height:400px;width:100%;">
                    <canvas id="fluxChart"></canvas>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard.movements') }}" class="btn btn-light rounded-pill px-4 fw-bold text-primary">
                        <i class="bi bi-table me-2"></i>Voir l'historique détaillé
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL COMMANDE ================= -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius:20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Créer une commande d'achat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stock.order.store_order') }}" method="POST" autocomplete="new-password">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Sélectionnez les produits enregistrés pour votre commande.</p>

                    <label class="form-label fw-bold small text-muted">Détails des produits</label>
                    <div id="products-wrapper">
                        <div class="product-row mb-4 d-flex gap-3 align-items-start">
                            <div class="flex-grow-1 suggestion-container">
                                <input type="text" name="products[0][name]"
                                    class="form-control product-input border-0 py-2 shadow-sm bg-light"
                                    placeholder="Nom du produit..." autocomplete="off" required
                                    value="{{ old('products.0.name') }}">
                                <div class="suggestion-menu shadow-lg"></div>
                                <div class="error-msg text-danger" style="display:none;">
                                    <i class="bi bi-exclamation-circle"></i> Produit non enregistré
                                </div>
                            </div>
                            <div style="width:120px;">
                                <input type="number" name="products[0][quantity]" placeholder="Qté"
                                    class="form-control border-0 py-2 shadow-sm bg-light" required min="1"
                                    value="{{ old('products.0.quantity') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" id="add-product" class="btn btn-sm btn-outline-primary rounded-pill">+ Ligne</button>
                        <button type="button" id="remove-product" class="btn btn-sm btn-outline-danger rounded-pill" style="display:none;">- Supprimer</button>
                    </div>

                    <div class="row pt-3 border-top">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Fournisseur</label>
                            <select name="supplier_manager_id" class="form-select border-0 shadow-sm py-2 bg-light" required>
                                <option value="">Choisir...</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ (string) old('supplier_manager_id') === (string) $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted">Date de livraison</label>
                            <input type="date" name="delivery_date" class="form-control border-0 shadow-sm py-2 bg-light" required value="{{ old('delivery_date') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background:var(--primary-gradient);border:none;">
                        Confirmer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ---- DROPDOWN ----
    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        const isOpen = menu.style.display === 'block';
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
        if (!isOpen) menu.style.display = 'block';
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
        }
    });

    // ---- MODAL EDIT ----
    function openEditModal(btn) {
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
        const id   = btn.getAttribute('data-id');
        const form = document.getElementById('editProductForm');
        form.action = form.getAttribute('data-action').replace(':id', id);
        document.getElementById('edit_name').value        = btn.getAttribute('data-name');
        document.getElementById('edit_price').value       = btn.getAttribute('data-price');
        document.getElementById('edit_quantity').value    = btn.getAttribute('data-quantity');
        document.getElementById('edit_description').value = btn.getAttribute('data-description') || '';
        new bootstrap.Modal(document.getElementById('editProductModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Ré-ouvrir modal commande si erreurs
        const hasOrderErrors = @json($hasOrderErrors);
        if (hasOrderErrors) {
            const el = document.getElementById('orderModal');
            if (el) bootstrap.Modal.getOrCreateInstance(el).show();
        }

        // ---- AUTOCOMPLÉTION COMMANDE ----
        const products = @json($products->pluck('name'));
        const wrapper  = document.getElementById('products-wrapper');
        const addBtn   = document.getElementById('add-product');
        const removeBtn = document.getElementById('remove-product');

        function initSuggestion(input) {
            const menu  = input.nextElementSibling;
            const error = menu.nextElementSibling;

            input.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                menu.innerHTML = '';
                if (!val) { menu.style.display = 'none'; return; }

                const matches = products.filter(p => p.toLowerCase().includes(val));
                if (matches.length > 0) {
                    error.style.display = 'none';
                    menu.style.display  = 'block';
                    matches.forEach(match => {
                        const div = document.createElement('div');
                        div.textContent = match;
                        div.onclick = () => { input.value = match; menu.style.display = 'none'; };
                        menu.appendChild(div);
                    });
                } else {
                    menu.style.display  = 'none';
                    error.style.display = 'block';
                }
            });

            document.addEventListener('click', e => { if (e.target !== input) menu.style.display = 'none'; });
        }

        initSuggestion(document.querySelector('.product-input'));

        let rowIdx = 1;
        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'product-row mb-4 d-flex gap-3 align-items-start';
            row.innerHTML = `
            <div class="flex-grow-1 suggestion-container">
                <input type="text" name="products[${rowIdx}][name]" class="form-control product-input border-0 py-2 shadow-sm bg-light" placeholder="Nom du produit..." autocomplete="off" required>
                <div class="suggestion-menu shadow-lg"></div>
                <div class="error-msg text-danger" style="display:none;"><i class="bi bi-exclamation-circle"></i> Produit non enregistré</div>
            </div>
            <div style="width:120px;"><input type="number" name="products[${rowIdx}][quantity]" placeholder="Qté" class="form-control border-0 py-2 shadow-sm bg-light" required min="1"></div>`;
            wrapper.appendChild(row);
            initSuggestion(row.querySelector('.product-input'));
            rowIdx++;
            removeBtn.style.display = 'inline-block';
        });

        removeBtn.addEventListener('click', () => {
            if (wrapper.children.length > 1) {
                wrapper.removeChild(wrapper.lastElementChild);
                if (wrapper.children.length === 1) removeBtn.style.display = 'none';
            }
        });

        // ---- GRAPHIQUE STATS ----
        const statsModal = document.getElementById('statsModal');
        let myChart = null;

        if (statsModal) {
            statsModal.addEventListener('shown.bs.modal', function() {
                if (myChart) return;
                fetch('{{ route("stock.chart.data") }}')
                    .then(r => r.json())
                    .then(data => {
                        const ctx = document.getElementById('fluxChart').getContext('2d');
                        const gSales = ctx.createLinearGradient(0,0,0,300);
                        gSales.addColorStop(0, 'rgba(239,68,68,0.4)');
                        gSales.addColorStop(1, 'rgba(239,68,68,0)');
                        const gSupply = ctx.createLinearGradient(0,0,0,300);
                        gSupply.addColorStop(0, 'rgba(34,197,94,0.4)');
                        gSupply.addColorStop(1, 'rgba(34,197,94,0)');
                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [
                                    { label:'Sorties (Ventes)',           data:data.sales,   borderColor:'#ef4444', backgroundColor:gSales,  borderWidth:2, fill:true, tension:0.4, pointRadius:3, pointHoverRadius:6 },
                                    { label:'Entrées (Approvisionnements)',data:data.supplies,borderColor:'#22c55e', backgroundColor:gSupply, borderWidth:2, fill:true, tension:0.4, pointRadius:3, pointHoverRadius:6 }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend:{position:'top'}, tooltip:{mode:'index',intersect:false} },
                                scales: {
                                    y: { beginAtZero:true, max:10000, grid:{borderDash:[2,2]} },
                                    x: { grid:{display:false} }
                                },
                                interaction: { mode:'nearest', axis:'x', intersect:false }
                            }
                        });
                    })
                    .catch(err => console.error("Erreur chargement graph:", err));
            });
        }
    });
</script>

@endsection
