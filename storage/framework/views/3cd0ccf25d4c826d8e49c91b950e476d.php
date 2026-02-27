

<?php $__env->startSection('content'); ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .page-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Barre de contrôle stylisée */
    .control-panel {
        background: white;
        padding: 1.25rem;
        border-radius: 15px;
        margin-bottom: 1.5rem;
        border: 1px solid #edf2f7;
    }

    .table thead {
        background-color: #f8fafc;
        color: #64748b;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

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

    /* Badges de statut */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fffbeb;
        color: #d97706;
    }

    .status-delivered {
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-canceled {
        background: #fef2f2;
        color: #dc2626;
    }

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

    @media print {

        .no-print,
        .btn,
        .control-panel,
        .page-header {
            display: none !important;
        }

        .printable {
            visibility: visible;
            width: 100%;
            position: absolute;
            left: 0;
            top: 0;
        }

        .table {
            border: 1px solid #000 !important;
        }
    }
</style>

<div class="container py-4">
    <!-- HEADER -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1"><i class="bi bi-truck me-2"></i>Espace Fournisseur</h1>
            <p class="mb-0 opacity-75 italic">Suivi des flux et commandes d'approvisionnement</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('suppliers.export.pdf', request()->query())); ?>" class="btn btn-light btn-action text-success">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <button class="btn btn-dark btn-action" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
        </div>
    </div>

    <!-- PANNEAU DE CONTRÔLE -->
    <div class="control-panel shadow-sm">
        <div class="row g-3 align-items-center">
            <div class="col-md-5">
                <form method="GET" action="<?php echo e(route('supplier.orders')); ?>" class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" class="form-control ps-5 py-2 bg-light border-0" placeholder="Rechercher un produit..." value="<?php echo e(request('search')); ?>">
                </form>
            </div>
            <div class="col-md-3">
                <form method="GET" id="filterForm" action="<?php echo e(route('supplier.orders')); ?>">
                    <select name="sort_by" class="form-select py-2 bg-light border-0 fw-600" onchange="this.form.submit()">
                        <option value="">Trier par...</option>
                        <option value="order_date" <?php echo e(request('sort_by') == 'order_date' ? 'selected' : ''); ?>>Date commande</option>
                        <option value="delivery_date" <?php echo e(request('sort_by') == 'delivery_date' ? 'selected' : ''); ?>>Date livraison</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- TABLEAU DES COMMANDES -->
    <div class="card printable">
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#CMD-<?php echo e($sale->id); ?></td>
                        <td><i class="bi bi-calendar-event me-2 text-muted"></i><?php echo e(\Carbon\Carbon::parse($sale->created_at)->format('d/m/Y')); ?></td>
                        <td><i class="bi bi-truck me-2 text-muted"></i><?php echo e(\Carbon\Carbon::parse($sale->delivery_date ?? now())->format('d/m/Y')); ?></td>
                        <td>
                            <?php
                            $statusClass = match(strtolower($sale->status)) {
                            'livrée', 'delivered' => 'status-delivered',
                            'en_cours', 'pending' => 'status-pending',
                            default => 'status-pending'
                            };
                            ?>
                            <span class="status-badge <?php echo e($statusClass); ?>"><?php echo e($sale->status); ?></span>
                        </td>
                        <td class="text-end pe-4 no-print">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo e($sale->id); ?>">
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- MODAL -->
                            <div class="modal fade text-start" id="orderModal<?php echo e($sale->id); ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="fw-bold mb-0">Détails Commande #<?php echo e($sale->id); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="bg-light p-3 rounded-3 mb-3">
                                                <div class="row text-center">
                                                    <div class="col-6 border-end">
                                                        <small class="text-muted d-block italic">Date</small>
                                                        <span class="fw-bold"><?php echo e(\Carbon\Carbon::parse($sale->created_at)->format('d/m/Y')); ?></span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block italic">Statut</small>
                                                        <span class="fw-bold text-uppercase" style="font-size:0.8rem"><?php echo e($sale->status); ?></span>
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
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                    <tr>
                                                        <td class="py-2"><?php echo e($item->product->name ?? 'Produit supprimé'); ?></td>
                                                        <td class="py-2 text-end fw-bold"><?php echo e($item->quantity); ?></td>
                                                    </tr>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted italic">Aucune commande répertoriée.</td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 no-print">
        <?php echo e($orders->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/supplier.blade.php ENDPATH**/ ?>