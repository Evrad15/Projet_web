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

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: #fffbeb;
        color: #d97706;
    }

    .status-completed {
        background-color: #f0fdf4;
        color: #16a34a;
    }

    .status-cancelled {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .status-credit {
        background-color: #fff7ed;
        color: #c2410c;
    }
</style>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Mes Achats et Commandes</h1>
            <p class="mb-0 opacity-75">Suivi de vos achats à crédit, terminés et commandes en attente.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboards.clients') }}" class="btn btn-outline-light fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Tableau de bord
            </a>
            <a href="{{ route('orders.create') }}" class="btn btn-light text-primary fw-bold">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle Commande
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="credit-tab" data-bs-toggle="tab" data-bs-target="#credit-sales" type="button" role="tab" aria-controls="credit-sales" aria-selected="true">
                        Achats à Crédit <span class="badge bg-warning ms-1">{{ $creditSales->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-orders" type="button" role="tab" aria-controls="pending-orders" aria-selected="false">
                        Commandes en attente <span class="badge bg-info ms-1">{{ $pendingOrders->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-sales" type="button" role="tab" aria-controls="completed-sales" aria-selected="false">
                        Historique des achats <span class="badge bg-light text-dark ms-1">{{ $completedSales->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="orderTabsContent">
            <!-- Onglet Achats à Crédit -->
            <div class="tab-pane fade show active" id="credit-sales" role="tabpanel" aria-labelledby="credit-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Facture N°</th>
                                <th>Date</th>
                                <th>Montant Total</th>
                                <th>Montant Payé</th>
                                <th class="text-danger">Reste à Payer</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($creditSales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold">FACT-{{ $sale->id }}</td>
                                <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="fw-bold text-danger">{{ number_format($sale->total - $sale->paid_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#saleDetailsModal{{ $sale->id }}" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Modal Détails Achat Crédit -->
                                    <div class="modal fade" id="saleDetailsModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content" style="border-radius: 15px;">
                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                    <div>
                                                        <h5 class="modal-title fw-bold">Détails de l'Achat</h5>
                                                        <span class="text-primary fw-bold">Facture N°{{ $sale->id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="bg-light p-3 rounded-3 mb-4">
                                                        <div class="row text-start">
                                                            <div class="col-md-3"><small class="text-muted d-block">Date</small><span class="fw-bold">{{ $sale->created_at->format('d/m/Y') }}</span></div>
                                                            <div class="col-md-3"><small class="text-muted d-block">Total</small><span class="fw-bold text-primary">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</span></div>
                                                            <div class="col-md-3"><small class="text-muted d-block">Payé</small><span class="fw-bold text-success">{{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA</span></div>
                                                            <div class="col-md-3"><small class="text-muted d-block">Reste</small><span class="fw-bold text-danger">{{ number_format($sale->total - $sale->paid_amount, 0, ',', ' ') }} FCFA</span></div>
                                                        </div>
                                                    </div>
                                                    <h6 class="fw-bold mb-3 text-start">Articles achetés</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <thead class="text-muted small">
                                                            <tr>
                                                                <th>Produit</th>
                                                                <th class="text-center">Qté</th>
                                                                <th class="text-end">P.U.</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($sale->items as $item)
                                                            <tr class="align-middle">
                                                                <td class="fw-bold">{{ $item->product->name ?? 'Produit supprimé' }}</td>
                                                                <td class="text-center">{{ $item->quantity }}</td>
                                                                <td class="text-end">{{ number_format($item->price ?? 0, 0, ',', ' ') }} FCFA</td>
                                                                <td class="text-end fw-bold">{{ number_format(($item->price ?? 0) * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer border-0"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-check-circle-fill fs-1 d-block mb-3 text-success"></i>
                                    Vous n'avez aucune dette en cours. Bravo !
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($creditSales->hasPages())
                <div class="p-3">{{ $creditSales->withQueryString()->links() }}</div>
                @endif
            </div>

            <!-- Onglet Commandes en attente -->
            <div class="tab-pane fade" id="pending-orders" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Référence</th>
                                <th>Date</th>
                                <th>Montant Total</th>
                                <th>Statut</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingOrders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $order->order_number ?? $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                <td><span class="status-badge status-pending">{{ $order->status }}</span></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#orderDetailsModal{{ $order->id }}" title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler cette commande ? Les produits seront remis en stock.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Annuler la commande">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal Détails Commande En Attente -->
                                    <div class="modal fade" id="orderDetailsModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content" style="border-radius: 15px;">
                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                    <div>
                                                        <h5 class="modal-title fw-bold">Détails Commande</h5>
                                                        <span class="text-primary fw-bold">#{{ $order->order_number ?? $order->id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="bg-light p-3 rounded-3 mb-4">
                                                        <div class="row text-start">
                                                            <div class="col-md-4"><small class="text-muted d-block">Date</small><span class="fw-bold">{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
                                                            <div class="col-md-4"><small class="text-muted d-block">Statut</small><span class="status-badge status-pending">{{ $order->status }}</span></div>
                                                            <div class="col-md-4"><small class="text-muted d-block">Total</small><span class="fw-bold text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span></div>
                                                        </div>
                                                    </div>
                                                    <h6 class="fw-bold mb-3 text-start">Articles commandés</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <thead class="text-muted small">
                                                            <tr>
                                                                <th>Produit</th>
                                                                <th class="text-center">Qté</th>
                                                                <th class="text-end">P.U.</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($order->items as $item)
                                                            <tr class="align-middle">
                                                                <td class="fw-bold">{{ $item->product->name ?? 'Produit supprimé' }}</td>
                                                                <td class="text-center">{{ $item->quantity }}</td>
                                                                <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                                                <td class="text-end fw-bold">{{ number_format($item->subtotal ?? ($item->unit_price * $item->quantity), 0, ',', ' ') }} FCFA</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer border-0"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-check fs-1 d-block mb-3"></i>
                                    Vous n'avez aucune commande en attente de validation.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pendingOrders->hasPages())
                <div class="p-3">{{ $pendingOrders->withQueryString()->links() }}</div>
                @endif
            </div>

            <!-- Onglet Achats Terminés -->
            <div class="tab-pane fade" id="completed-sales" role="tabpanel" aria-labelledby="completed-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Facture N°</th>
                                <th>Date</th>
                                <th>Montant Total</th>
                                <th>Statut</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedSales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold">FACT-{{ $sale->id }}</td>
                                <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
                                <td><span class="status-badge status-completed">Payée</span></td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#completedSaleModal{{ $sale->id }}" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <!-- Modal Détails Achat Terminé -->
                                    <div class="modal fade" id="completedSaleModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content" style="border-radius: 15px;">
                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                    <div>
                                                        <h5 class="modal-title fw-bold">Détails de l'Achat</h5>
                                                        <span class="text-primary fw-bold">Facture N°{{ $sale->id }}</span>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="bg-light p-3 rounded-3 mb-4">
                                                        <div class="row text-start">
                                                            <div class="col-md-4"><small class="text-muted d-block">Date</small><span class="fw-bold">{{ $sale->created_at->format('d/m/Y') }}</span></div>
                                                            <div class="col-md-4"><small class="text-muted d-block">Total</small><span class="fw-bold text-primary">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</span></div>
                                                            <div class="col-md-4"><small class="text-muted d-block">Statut</small><span class="status-badge status-completed">Payée</span></div>
                                                        </div>
                                                    </div>
                                                    <h6 class="fw-bold mb-3 text-start">Articles achetés</h6>
                                                    <table class="table table-sm table-borderless">
                                                        <thead class="text-muted small">
                                                            <tr>
                                                                <th>Produit</th>
                                                                <th class="text-center">Qté</th>
                                                                <th class="text-end">P.U.</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($sale->items as $item)
                                                            <tr class="align-middle">
                                                                <td class="fw-bold">{{ $item->product->name ?? 'Produit supprimé' }}</td>
                                                                <td class="text-center">{{ $item->quantity }}</td>
                                                                <td class="text-end">{{ number_format($item->price ?? 0, 0, ',', ' ') }} FCFA</td>
                                                                <td class="text-end fw-bold">{{ number_format(($item->price ?? 0) * $item->quantity, 0, ',', ' ') }} FCFA</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer border-0"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
                                    Vous n'avez aucun achat terminé.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($completedSales->hasPages())
                <div class="p-3">{{ $completedSales->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection