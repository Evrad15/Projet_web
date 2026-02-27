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

    .filter-card {
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
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

    .bulk-actions {
        position: fixed;
        bottom: 30px;
        right: 30px;
        display: flex;
        gap: 12px;
        z-index: 999;
        background: white;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 1px solid #ddd;
    }

    .btn-action {
        border-radius: 8px;
        transition: all 0.3s;
    }
</style>

<div class="container py-4">

    <!-- ================= HEADER MODERNE ================= -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h1 class="fw-bold mb-1">Historique des Commandes</h1>
                <p class="mb-0 opacity-75">Suivez et gérez vos approvisionnements fournisseurs</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(session()->has('hidden_orders'))
            <form action="{{ route('stock.order.reset-hidden') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light btn-action fw-bold text-dark">
                    <i class="bi bi-eye"></i> Réafficher tout
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <!-- ================= BARRE DE RECHERCHE ET FILTRES ================= -->
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('stock.order.dashboard') }}" method="GET" class="row g-3" autocomplete="new-password">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Fournisseur ou produit..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Statut</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="En_cours" {{ request('status') == 'En_cours' ? 'selected' : '' }}>En cours </option>
                        <option value="livrée" {{ request('status') == 'livrée' ? 'selected' : '' }}>Livrée</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Trier par date</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Plus récent au plus ancien</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Plus ancien au plus récent</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Appliquer</button>
                </div>
            </form>
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="alert alert-info border-0 shadow-sm">Aucune commande pour le moment.</div>
    @else
    <!-- ================= FORMULAIRE ACTIONS GROUPÉES ================= -->
    <form id="bulk-action-form" method="POST">
        @csrf
        @method('PATCH')
        <div id="bulk-actions" class="bulk-actions d-none">
            <span class="me-2 small fw-bold text-muted d-none d-md-inline">Action groupée :</span>
            <button type="submit" id="btn-bulk-cancel" formaction="{{ route('stock.order.bulk.cancel') }}" class="btn btn-danger btn-sm rounded-pill px-3">Annuler</button>
            <button type="submit" id="btn-bulk-hide" formaction="{{ route('stock.order.bulk.hide') }}" class="btn btn-secondary btn-sm rounded-pill px-3">Masquer</button>
        </div>
        <div id="hidden-inputs-container"></div>
    </form>

    <!-- ================= TABLEAU DES COMMANDES ================= -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4"><input type="checkbox" class="form-check-input" id="select-all"></th>
                            <th>ID</th>
                            <th>Fournisseur</th>
                            <th>Date Livraison</th>
                            <th>Statut</th>
                            <th>Produits commandés</th>
                            <th class="text-end pe-4">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}" data-status="{{ $order->status }}">
                            </td>
                            <td class="text-muted fw-bold">#{{ $order->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $order->supplier->name ?? 'N/A' }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</td>
                            <td>
                                @if($order->status === 'En_cours' || $order->status === 'pending')
                                <span class="status-badge bg-warning-subtle text-warning">En cours</span>
                                @elseif($order->status === 'livrée' || $order->status === 'delivered')
                                <span class="status-badge bg-success-subtle text-success">Livrée</span>
                                @else
                                <span class="status-badge bg-secondary-subtle text-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                <ul class="mb-0 small ps-3">
                                    @foreach($order->items as $item)
                                    <li>{{ $item->product->name ?? 'Produit supprimé' }} <span class="text-muted">(x{{ $item->quantity }})</span></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-trigger" data-bs-toggle="modal" data-bs-target="#manageOrderModal{{ $order->id }}"
                                    style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background:">

                                    <i class="bi bi-arrows-angle-expand"></i>

                                </div>
                            </td>


                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

    <!-- BOUCLE POUR LES MODALES ET FORMULAIRES (HORS DU TABLEAU) -->
    @foreach($orders as $order)
    <!-- Formulaires cachés -->
    <form id="delete-form-{{ $order->id }}" action="{{ route('stock.destroy', $order->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
    <form id="deliver-form-{{ $order->id }}" action="{{ route('stock.deliver', $order->id) }}" method="POST" class="d-none">@csrf @method('PATCH')</form>

    <!-- LA MODAL (Structure stricte) -->
    <div class="modal fade" id="manageOrderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">

                <form action="{{ route('stock.update', $order->id) }}" method="POST" autocomplete="new-password">
                    @csrf
                    @method('PUT')

                    <!-- HEADER AVEC DÉGRADÉ SUBTIL -->
                    <div class="modal-header border-0 py-4 px-4" style="background: #f8fafc;">
                        <div>
                            <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -1px;">Commande #{{ $order->id }}</h4>
                            <span class="badge {{ $order->status === 'livrée' ? 'bg-success' : 'bg-warning' }} rounded-pill small" style="font-size: 10px;">
                                {{ strtoupper($order->status) }}
                            </span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- SECTION INFOS GÉNÉRALES -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="p-3 rounded-4 bg-light border-0 shadow-sm transition-all">
                                    <label class="text-[10px] fw-bold text-muted uppercase tracking-widest d-block mb-2 italic">Expéditeur / Fournisseur</label>
                                    <div class="view-mode d-flex align-items-center">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>
                                        <span class="fw-bold text-dark">{{ $order->supplier->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="edit-mode d-none">
                                        <select name="supplier_manager_id" class="form-select border-0 bg-white shadow-sm py-2">
                                            @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ $order->supplier_manager_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-4 bg-light border-0 shadow-sm">
                                    <label class="text-[10px] fw-bold text-muted uppercase tracking-widest d-block mb-2 italic">Réception Prévue</label>
                                    <div class="view-mode d-flex align-items-center">
                                        <i class="bi bi-calendar3 me-2 text-primary"></i>
                                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</span>
                                    </div>
                                    <div class="edit-mode d-none">
                                        <input type="date" name="delivery_date" class="form-control border-0 bg-white shadow-sm py-2" value="{{ $order->delivery_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLEAU DES PRODUITS -->
                        <div class="px-2">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-ul me-2"></i>Détails des articles</h6>
                            <div class="table-responsive rounded-4 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="text-muted small uppercase tracking-widest" style="font-size: 11px;">
                                            <th class="ps-4 py-3">Produit</th>
                                            <th class="py-3 text-center" style="width: 150px;">Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $index => $item)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <span class="view-mode fw-bold text-secondary">{{ $item->product->name ?? 'Produit inconnu' }}</span>
                                                <div class="edit-mode d-none">
                                                    <input type="text" name="products[{{ $index }}][name]" class="form-control form-control-sm border-0 bg-light py-2" value="{{ $item->product->name ?? '' }}">
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="view-mode badge bg-primary-subtle text-primary rounded-pill px-3 py-2" style="min-width: 50px;">
                                                    {{ $item->quantity }}
                                                </span>
                                                <div class="edit-mode d-none px-3">
                                                    <input type="number" name="products[{{ $index }}][quantity]" class="form-control form-control-sm text-center border-0 bg-light py-2" value="{{ $item->quantity }}">
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER AVEC EFFET BLUR -->
                    <div class="modal-footer border-0 py-4 px-4 bg-light d-flex justify-content-between">
                        <div class="d-flex gap-2">
                            @if($order->status !== 'livrée')
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3 fw-bold" onclick="event.preventDefault(); if(confirm('Supprimer définitivement ?')) document.getElementById('delete-form-{{ $order->id }}').submit();">
                                <i class="bi bi-trash3"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="event.preventDefault(); if(confirm('Valider la livraison ?')) document.getElementById('deliver-form-{{ $order->id }}').submit();" style="background: #10b981; border: none;">
                                <i class="bi bi-box-seam me-2"></i>Livrer
                            </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold toggle-edit-btn" style="border: 2px solid #6366f1; color: #6366f1;">
                                <i class="bi bi-pencil-square me-2"></i>Modifier
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary d-none save-btn rounded-pill px-4 fw-bold shadow-lg" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border: none;">
                                Sauvegarder
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>
<style>
    .modal-content {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .bg-primary-subtle {
        background-color: rgba(99, 102, 241, 0.1) !important;
    }

    .btn-sm {
        font-size: 0.75rem !important;
        letter-spacing: 0.5px;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        background: white !important;
    }

    /* Effet au survol des lignes du tableau */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.01) !important;
    }
</style>
<script>
    document.querySelectorAll('.toggle-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal-content');
            const isEditing = modal.querySelectorAll('.edit-mode')[0].classList.contains('d-none');

            if (isEditing) {
                // Passer en mode EDITION
                modal.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
                modal.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
                modal.querySelector('.save-btn').classList.remove('d-none');
                this.innerHTML = '<i class="bi bi-x-circle"></i> Annuler';
                this.classList.replace('btn-outline-primary', 'btn-outline-secondary');
            } else {
                // Revenir en mode VUE
                modal.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
                modal.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
                modal.querySelector('.save-btn').classList.add('d-none');
                this.innerHTML = '<i class="bi bi-pencil-square"></i> Modifier';
                this.classList.replace('btn-outline-secondary', 'btn-outline-primary');
            }
        });
    });

    function confirmDelete(id) {
        if (confirm('Voulez-vous vraiment supprimer cette commande ?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    function confirmDelivery(id) {
        if (confirm('Confirmer la réception ? Cela augmentera le stock des produits.')) {
            document.getElementById('deliver-form-' + id).submit();
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bulk-action-form');
        const bulkActions = document.getElementById('bulk-actions');
        const hiddenContainer = document.getElementById('hidden-inputs-container');
        const btnCancel = document.getElementById('btn-bulk-cancel');
        const btnHide = document.getElementById('btn-bulk-hide');
        const checkboxes = document.querySelectorAll('.order-checkbox');
        const selectAll = document.getElementById('select-all');

        function updateBulkActions() {
            const checked = document.querySelectorAll('.order-checkbox:checked');
            bulkActions.classList.toggle('d-none', checked.length === 0);
            hiddenContainer.innerHTML = '';
            let hasPending = false;

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order_ids[]';
                input.value = cb.value;
                hiddenContainer.appendChild(input);
                if (cb.dataset.status === 'En_cours' || cb.dataset.status === 'pending') hasPending = true;
            });
            btnCancel.style.display = hasPending ? 'inline-block' : 'none';
            btnHide.style.display = checked.length > 0 ? 'inline-block' : 'none';
        }

        [btnCancel, btnHide].forEach(btn => {
            btn.addEventListener('click', function() {
                form.action = this.getAttribute('formaction');
            });
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateBulkActions));
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkActions();
            });
        }
    });
</script>
@endsection