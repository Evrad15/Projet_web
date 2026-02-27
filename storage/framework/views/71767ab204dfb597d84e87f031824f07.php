

<?php $__env->startSection('content'); ?>
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
        overflow: visible !important;
    }

    .card-body {
        overflow: visible !important;
    }

    .table-responsive {
        overflow: visible !important;
    }

    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    /* ── Dropdown vanilla ── */
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    .custom-dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        z-index: 9999;
        background: #fff;
        border-radius: 12px;
        min-width: 170px;
        padding: 6px 0;
        list-style: none;
        margin: 4px 0 0;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
    }

    .custom-dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        padding: 9px 16px;
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        text-decoration: none;
        background: transparent;
        border: none;
        width: 100%;
        text-align: left;
        transition: background 0.15s;
    }

    .custom-dropdown-menu .dropdown-item:hover {
        background: #f9fafb;
    }

    .custom-dropdown-menu .dropdown-item.danger {
        color: #ef4444;
    }

    .custom-dropdown-menu .dropdown-item.danger:hover {
        background: #fff5f5;
    }

    .custom-dropdown-menu form {
        margin: 0;
        padding: 0;
    }

    .custom-dropdown-menu li {
        list-style: none;
    }
</style>

<div class="d-flex">
    <!-- SIDEBAR -->
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end" style="width: 260px; min-height: calc(100vh - 65px);">
        <span class="fs-5 fw-bold text-primary"><i class="bi bi-grid-1x2-fill me-2"></i>Menu</span>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard.sales') ? 'active' : 'link-dark'); ?> fw-bold">
                    <i class="bi bi-bag-check me-2"></i> Commandes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales.employees')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard.sales.employees') ? 'active' : 'link-dark'); ?> fw-bold">
                    <i class="bi bi-people me-2"></i> Employés
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales.performance')); ?>" class="nav-link link-dark fw-bold">
                    <i class="bi bi-graph-up-arrow me-2"></i> Performances
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('clients.index')); ?>" class="nav-link <?php echo e(request()->routeIs('clients.*') ? 'active' : 'link-dark'); ?> fw-bold">
                    <i class="bi bi-person-badge me-2"></i> Clients
                </a>
            </li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4" style="background-color: #f8f9fa;">

        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1">Gestion des ventes</h1>
                <p class="mb-0 opacity-75">Suivi des ventes et relation client</p>
            </div>
            <button type="button" class="btn btn-light text-primary fw-bold shadow-sm"
                data-bs-toggle="modal" data-bs-target="#statsModal">
                <i class="bi bi-bar-chart-fill me-2"></i>Statistiques Détaillées
            </button>
        </div>

        <!-- KPIs -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-75">Chiffre d'Affaires Total</h6>
                        <h2 class="fw-bold mb-0"><?php echo e(number_format($totalRevenue, 0, ',', ' ')); ?> FCFA</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-75">Volume des Ventes</h6>
                        <h2 class="fw-bold mb-0"><?php echo e($totalSalesCount); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase mb-2 opacity-75">Panier Moyen</h6>
                        <h2 class="fw-bold mb-0"><?php echo e(number_format($averageBasket, 0, ',', ' ')); ?> FCFA</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo e(url()->current()); ?>" class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0"
                                placeholder="Rechercher par client ou produit..." value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-calendar text-muted"></i></span>
                            <input type="date" name="date" class="form-control bg-light border-0" value="<?php echo e(request('date')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Réf.</th>
                                <th>Statut</th>
                                <th>Client</th>
                                <th>Articles</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo e($sale->order_number ?? $sale->id); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sale->status == 'completed'): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Complété</span>
                                    <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3"><?php echo e($sale->status); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($sale->client->name); ?></td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <li><?php echo e($item->product->name ?? 'Produit supprimé'); ?>

                                            <span class="text-muted">(x<?php echo e($item->quantity); ?>)</span>
                                        </li>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </ul>
                                </td>
                                <td class="fw-bold text-primary"><?php echo e(number_format($sale->total_amount ?? $sale->total, 0, ',', ' ')); ?> FCFA</td>
                                <td><?php echo e($sale->created_at->format('d/m/Y')); ?></td>
                                <td class="text-end pe-4">
                                    <div class="custom-dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm"
                                            type="button" onclick="toggleDropdown(this)">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="custom-dropdown-menu">
                                            <li>
                                                <button class="dropdown-item" type="button"
                                                    onclick="openModal('assignSaleModal<?php echo e($sale->id); ?>')">
                                                    <i class="bi bi-pencil me-2 text-warning"></i>Modifier
                                                </button>
                                            </li>
                                            <li>
                                                <form action="<?php echo e(route('client-orders.destroy', $sale->id)); ?>" method="POST"
                                                    onsubmit="return confirm('Supprimer cette commande ?');">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="dropdown-item danger">
                                                        <i class="bi bi-trash me-2"></i>Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    Aucune vente enregistrée.
                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4"><?php echo e($sales->links()); ?></div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
        <div class="modal fade text-start" id="assignSaleModal<?php echo e($sale->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header bg-warning-subtle border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil me-2 text-warning"></i>Modifier Vente #<?php echo e($sale->id); ?>

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?php echo e(route('client-orders.update', $sale->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Client</label>
                                <select name="client_id" class="form-select" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <option value="<?php echo e($client->id); ?>" <?php echo e($sale->client_id == $client->id ? 'selected' : ''); ?>>
                                        <?php echo e($client->name); ?>

                                    </option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Statut</label>
                                <select name="status" class="form-select">
                                    <option value="en attente" <?php echo e($sale->status == 'en attente' ? 'selected' : ''); ?>>En attente</option>
                                    <option value="en traitement" <?php echo e($sale->status == 'en traitement' ? 'selected' : ''); ?>>En traitement</option>
                                    <option value="completed" <?php echo e($sale->status == 'completed' ? 'selected' : ''); ?>>Complétée</option>
                                    <option value="Annulée" <?php echo e($sale->status == 'Annulée' ? 'selected' : ''); ?>>Annulée</option>
                                </select>
                            </div>
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">Articles</label>
                            <div class="bg-light p-3 rounded">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="row g-2 mb-2 align-items-center">
                                    <div class="col-md-7">
                                        <select name="products[<?php echo e($index); ?>][id]" class="form-select form-select-sm">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <option value="<?php echo e($product->id); ?>" <?php echo e($item->product_id == $product->id ? 'selected' : ''); ?>>
                                                <?php echo e($product->name); ?> (<?php echo e(number_format($product->price, 0, ',', ' ')); ?> FCFA)
                                            </option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="products[<?php echo e($index); ?>][quantity]"
                                            class="form-control form-control-sm"
                                            value="<?php echo e($item->quantity); ?>" min="1">
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-warning fw-bold px-4">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <!-- Modal Stats -->
        <div class="modal fade" id="statsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">Évolution des Ventes (30 derniers jours)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ── Dropdown vanilla (sans conflit Alpine) ──
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

    function openModal(id) {
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
        new bootstrap.Modal(document.getElementById(id)).show();
    }

    // ── Chart ──
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                datasets: [{
                    label: "Chiffre d'Affaires (FCFA)",
                    data: <?php echo json_encode($chartData, 15, 512) ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: '#764ba2',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/sales.blade.php ENDPATH**/ ?>