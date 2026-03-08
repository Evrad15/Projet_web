@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* ===================== PAGE HEADER ===================== */
    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 1.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    }

    .page-header h1 {
        font-size: clamp(1.2rem, 4vw, 1.8rem);
    }

    @media (max-width: 767px) {
        .page-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
            padding: 1.25rem;
        }

        .page-header .header-actions {
            display: flex;
            gap: 0.5rem;
            width: 100%;
        }

        .page-header .header-actions .btn {
            flex: 1;
            font-size: 0.82rem;
            padding: 0.45rem 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
    }

    /* ===================== CONTROL PANEL ===================== */
    .control-panel {
        background: white;
        padding: 1.25rem;
        border-radius: 15px;
        margin-bottom: 1.5rem;
        border: 1px solid #edf2f7;
    }

    @media (max-width: 767px) {
        .control-panel .row {
            flex-direction: column;
        }

        .control-panel .col-md-5,
        .control-panel .col-md-3 {
            width: 100%;
        }
    }

    /* ===================== CARD & TABLE ===================== */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table thead {
        background-color: #f8fafc;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    /* ===================== TABLEAU → CARTES MOBILE ===================== */
    @media (max-width: 767px) {
        .desktop-table { display: none !important; }
        .mobile-cards  { display: flex !important; flex-direction: column; gap: 0.75rem; padding: 0.75rem; }
    }
    @media (min-width: 768px) {
        .desktop-table { display: block !important; }
        .mobile-cards  { display: none !important; }
    }

    /* Carte commande mobile */
    .order-mobile-card {
        background: #fff;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .order-mobile-card .order-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .order-mobile-card .order-ref {
        font-weight: 700;
        color: #667eea;
        font-size: 1rem;
    }

    .order-mobile-card .order-dates {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 0.78rem;
        color: #64748b;
    }

    .order-mobile-card .order-dates span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .order-mobile-card .order-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ===================== BOUTONS ===================== */
    .btn-action {
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* ===================== BADGES STATUT ===================== */
    .status-badge {
        padding: 0.4rem 0.85rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .status-pending   { background: #fffbeb; color: #d97706; }
    .status-delivered { background: #f0fdf4; color: #16a34a; }
    .status-canceled  { background: #fef2f2; color: #dc2626; }

    /* ===================== MODAL ===================== */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        border-radius: 20px 20px 0 0;
    }

    /* Modal plein-bas sur mobile */
    @media (max-width: 575px) {
        .modal-dialog {
            margin: 0;
            align-items: flex-end;
            min-height: 100%;
            display: flex;
        }

        .modal-content {
            border-radius: 20px 20px 0 0;
            width: 100%;
        }
    }

    /* ===================== PRINT ===================== */
    @media print {
        .no-print, .btn, .control-panel, .page-header { display: none !important; }
        .printable { visibility: visible; width: 100%; position: absolute; left: 0; top: 0; }
        .table { border: 1px solid #000 !important; }
    }
</style>

<div class="container py-4">

    <!-- ================= HEADER ================= -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1"><i class="bi bi-truck me-2"></i>Espace Fournisseur</h1>
            <p class="mb-0 opacity-75" style="font-style:italic;">Suivi des flux et commandes d'approvisionnement</p>
        </div>
        <div class="header-actions d-flex gap-2 no-print">
            <a href="{{ route('suppliers.export.pdf', request()->query()) }}" class="btn btn-light btn-action text-success">
                <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
            </a>
            <button class="btn btn-dark btn-action" onclick="window.print()">
                <i class="bi bi-printer"></i> <span>Imprimer</span>
            </button>
        </div>
    </div>

    <!-- ================= PANNEAU DE CONTRÔLE ================= -->
    <div class="control-panel shadow-sm no-print">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <form method="GET" action="{{ route('supplier.orders') }}" class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search"
                        class="form-control ps-5 py-2 bg-light border-0"
                        placeholder="Rechercher un produit..."
                        value="{{ request('search') }}">
                </form>
            </div>
            <div class="col-md-3">
                <form method="GET" id="filterForm" action="{{ route('supplier.orders') }}">
                    <select name="sort_by" class="form-select py-2 bg-light border-0 fw-semibold" onchange="this.form.submit()">
                        <option value="">Trier par...</option>
                        <option value="order_date"    {{ request('sort_by') == 'order_date'    ? 'selected' : '' }}>Date commande</option>
                        <option value="delivery_date" {{ request('sort_by') == 'delivery_date' ? 'selected' : '' }}>Date livraison</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= TABLEAU (desktop) ================= -->
    <div class="card printable desktop-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Réf. Commande</th>
                        <th>Émission</th>
                        <th>Livraison Prévue</th>
                        <th>État</th>
                        <th class="text-end pe-4 no-print">Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $sale)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#CMD-{{ $sale->id }}</td>
                        <td>
                            <i class="bi bi-calendar-event me-2 text-muted"></i>
                            {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}
                        </td>
                        <td>
                            <i class="bi bi-truck me-2 text-muted"></i>
                            {{ \Carbon\Carbon::parse($sale->delivery_date ?? now())->format('d/m/Y') }}
                        </td>
                        <td>
                            @php
                                $statusClass = match(strtolower($sale->status)) {
                                    'livrée', 'delivered' => 'status-delivered',
                                    'annulée', 'canceled' => 'status-canceled',
                                    default               => 'status-pending'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $sale->status }}</span>
                        </td>
                        <td class="text-end pe-4 no-print">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                data-bs-toggle="modal" data-bs-target="#orderModal{{ $sale->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted" style="font-style:italic;">
                            Aucune commande répertoriée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= CARTES (mobile) ================= -->
    <div class="mobile-cards">
        @forelse($orders as $sale)
        @php
            $statusClass = match(strtolower($sale->status)) {
                'livrée', 'delivered' => 'status-delivered',
                'annulée', 'canceled' => 'status-canceled',
                default               => 'status-pending'
            };
        @endphp
        <div class="order-mobile-card">
            <div class="order-top">
                <div class="order-ref">#CMD-{{ $sale->id }}</div>
                <span class="status-badge {{ $statusClass }}">{{ $sale->status }}</span>
            </div>
            <div class="order-dates">
                <span><i class="bi bi-calendar-event"></i> Émission : {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</span>
                <span><i class="bi bi-truck"></i> Livraison : {{ \Carbon\Carbon::parse($sale->delivery_date ?? now())->format('d/m/Y') }}</span>
            </div>
            <div class="order-bottom">
                <span class="text-muted" style="font-size:0.75rem;">{{ $sale->items->count() }} article(s)</span>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                    data-bs-toggle="modal" data-bs-target="#orderModal{{ $sale->id }}">
                    <i class="bi bi-eye me-1"></i> Voir
                </button>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted" style="font-style:italic;">Aucune commande répertoriée.</div>
        @endforelse
    </div>

    <!-- ================= MODALS (partagés desktop + mobile) ================= -->
    @foreach($orders as $sale)
    <div class="modal fade text-start" id="orderModal{{ $sale->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0">Détails Commande #{{ $sale->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block" style="font-style:italic;">Date</small>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block" style="font-style:italic;">Statut</small>
                                <span class="fw-bold text-uppercase" style="font-size:0.8rem">{{ $sale->status }}</span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm">
                        <thead class="bg-white">
                            <tr>
                                <th class="border-0">Produit</th>
                                <th class="border-0 text-end">Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product->name ?? 'Produit supprimé' }}</td>
                                <td class="py-2 text-end fw-bold">{{ $item->quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="mt-4 no-print">
        {{ $orders->links() }}
    </div>
</div>
@endsection
