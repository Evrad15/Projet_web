

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
    }

    .badge-type {
        font-size: 0.75rem;
        padding: 0.4em 0.8em;
        border-radius: 20px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .bg-initial {
        background-color: #e2e8f0;
        color: #475569;
    }

    .bg-supply {
        background-color: #dcfce7;
        color: #166534;
    }

    /* Vert */
    .bg-sale {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Rouge */
    .bg-correction {
        background-color: #fef9c3;
        color: #854d0e;
    }

    /* Jaune */
    .bg-return {
        background-color: #dbeafe;
        color: #1e40af;
    }

    /* Bleu */
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h1 class="fw-bold mb-1">Mouvements de Stock</h1>
                <p class="mb-0 opacity-75">Traçabilité complète des entrées et sorties</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('dashboard.movements')); ?>" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Rechercher un produit..." value="<?php echo e(request('search')); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select bg-light border-0">
                        <option value="">Tous les types</option>
                        <option value="supply" <?php echo e(request('type') == 'supply' ? 'selected' : ''); ?>>Approvisionnement</option>
                        <option value="sale" <?php echo e(request('type') == 'sale' ? 'selected' : ''); ?>>Vente</option>
                        <option value="correction" <?php echo e(request('type') == 'correction' ? 'selected' : ''); ?>>Correction / Inventaire</option>
                        <option value="return" <?php echo e(request('type') == 'return' ? 'selected' : ''); ?>>Retour Client</option>
                        <option value="initial" <?php echo e(request('type') == 'initial' ? 'selected' : ''); ?>>Stock Initial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Filtrer</button>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search', 'type'])): ?>
                <div class="col-md-2">
                    <a href="<?php echo e(route('dashboard.movements')); ?>" class="btn btn-outline-secondary w-100">Réinitialiser</a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Produit</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Utilisateur</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?php echo e($m->created_at->format('d/m/Y H:i')); ?></td>
                            <td class="fw-bold <?php echo e($m->quantity < 0 ? 'text-danger' : 'text-success'); ?>">
                                <?php echo e($m->product->name ?? 'Produit supprimé'); ?>

                            </td>
                            <td>
                                <?php
                                $badges = [
                                'initial' => ['bg-initial', 'Initial'],
                                'supply' => ['bg-supply', 'Entrée'],
                                'sale' => ['bg-sale', 'Sortie'],
                                'correction' => ['bg-correction', 'Correction'],
                                'return' => ['bg-return', 'Retour'],
                                ];
                                $style = $badges[$m->type] ?? ['bg-secondary', $m->type];
                                ?>
                                <span class="badge-type <?php echo e($style[0]); ?>"><?php echo e($style[1]); ?></span>
                            </td>
                            <td>
                                <span class="fw-bold <?php echo e($m->quantity > 0 ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e($m->quantity > 0 ? '+' : ''); ?><?php echo e($m->quantity); ?>

                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width:25px;height:25px;font-size:10px;">
                                        <?php echo e(substr($m->user->name ?? '?', 0, 1)); ?>

                                    </div>
                                    <small><?php echo e($m->user->name ?? 'Système'); ?></small>
                                </div>
                            </td>
                            <td class="text-muted small"><?php echo e($m->description); ?></td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Aucun mouvement enregistré.</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4"><?php echo e($movements->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/movements.blade.php ENDPATH**/ ?>