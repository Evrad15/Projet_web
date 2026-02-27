

<?php $__env->startSection('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .modal-content {
        border: none;
        border-radius: 15px;
    }

    .product-row {
        background: #fdfdfd;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    /* Alignement items dropdown custom */
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
</style>

<div class="container py-4">

    <!-- ================= HEADER MODERNE ================= -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Inventaire</h1>
            <p class="mb-0 opacity-75">Gérez vos produits et approvisionnements en temps réel</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Nouveau bouton : Ajouter Produit -->
            <button class="btn btn-outline-light btn-action fw-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-box-seam"></i> + Produit
            </button>

            <button class="btn btn-outline-light btn-action fw-bold" data-bs-toggle="modal" data-bs-target="#statsModal">
                <i class="bi bi-graph-up-arrow"></i> Flux & Stats
            </button>

            <a href="<?php echo e(route('stock.order.dashboard')); ?>" class="btn btn-light btn-action fw-bold text-primary">
                <i class="bi bi-cart-check"></i> Commandes
            </a>

            <button class="btn btn-dark btn-action" data-bs-toggle="modal" data-bs-target="#orderModal">
                <i class="bi bi-plus-circle"></i> Nouvelle Commande
            </button>
        </div>
    </div>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
    $hasOrderErrors = $errors->has('error')
        || $errors->has('supplier_manager_id')
        || $errors->has('delivery_date')
        || collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'products.'));
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOrderErrors): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has('error')): ?>
        <div><?php echo e($errors->first('error')); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <ul class="mb-0 ps-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error !== $errors->first('error')): ?>
            <li><?php echo e($error); ?></li>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </ul>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Insère ceci juste après le .page-header et avant le tableau -->
    <div class="row g-3 mb-4">
        <!-- Carte 1 : Total Produits -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Produits distincts</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['total_products']); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte 2 : Volume Total -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="bi bi-stack fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Volume de stock</h6>
                        <h4 class="fw-bold mb-0"><?php echo e($stats['total_stock']); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte 3 : Valeur Stock -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info-subtle text-info p-3 rounded-3 me-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Valeur estimée</h6>
                        <h4 class="fw-bold mb-0"><?php echo e(number_format($stats['stock_value'], 2)); ?> FCFA</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte 4 : Alertes -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm <?php echo e($stats['low_stock'] > 0 ? 'bg-danger-subtle' : ''); ?>">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 <?php echo e($stats['low_stock'] > 0 ? 'bg-danger text-white' : 'bg-secondary-subtle text-secondary'); ?> p-3 rounded-3 me-3">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted small mb-1">Alertes stock bas</h6>
                        <h4 class="fw-bold mb-0 <?php echo e($stats['low_stock'] > 0 ? 'text-danger' : ''); ?>"><?php echo e($stats['low_stock']); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BARRE DE RECHERCHE ET FILTRES -->
    <div class="d-flex justify-content-end mb-4">
        <form method="GET" action="<?php echo e(route('dashboard.stock')); ?>" class="d-flex gap-2 align-items-center">
            <select name="status" class="form-select border-0 shadow-sm" style="width: 180px;" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="available" <?php echo e(request('status') == 'available' ? 'selected' : ''); ?>>Disponible</option>
                <option value="low" <?php echo e(request('status') == 'low' ? 'selected' : ''); ?>>Stock Bas</option>
                <option value="rupture" <?php echo e(request('status') == 'rupture' ? 'selected' : ''); ?>>Rupture</option>
            </select>
            <div class="input-group shadow-sm" style="width: 300px;">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0" placeholder="Rechercher un produit..." value="<?php echo e(request('search')); ?>">
            </div>
        </form>
    </div>

    <!-- ================= TABLEAU DE BORD ================= -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;">
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border-0 py-2 px-3 fw-bold" style="border-radius: 8px; font-family: monospace;">
                                    <?php echo e($product->formatted_id); ?>

                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark"><?php echo e($product->name); ?></div>
                                <small class="text-muted"><?php echo e(Str::limit($product->description, 40) ?? 'Aucune description'); ?></small>
                            </td>
                            <td><span class="fw-bold"><?php echo e($product->quantity); ?></span></td>
                            <td><span class="badge bg-light text-dark border"><?php echo e(number_format($product->price, 2)); ?> Fcfa</span></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->quantity <= 0): ?>
                                    <span class="status-badge bg-danger-subtle text-danger">Rupture</span>
                                    <?php elseif($product->quantity < 5): ?>
                                        <span class="status-badge bg-warning-subtle text-warning">Stock Bas</span>
                                        <?php else: ?>
                                        <span class="status-badge bg-success-subtle text-success">Disponible</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><?php echo e($product->created_at->format('d M Y')); ?></td>
                            <td class="text-end pe-4">
                                <div class="custom-dropdown" style="position: relative; display: inline-block;">
                                    <button
                                        class="btn btn-link text-dark p-0"
                                        type="button"
                                        onclick="toggleDropdown(this)"
                                    >
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="custom-dropdown-menu border-0 shadow-lg" style="display:none; position:absolute; right:0; top:100%; z-index:9999; background:#fff; border-radius:10px; min-width:160px; padding:6px 0; list-style:none; margin:0;">
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                onclick="openEditModal(this); return false;"
                                                data-id="<?php echo e($product->id); ?>"
                                                data-name="<?php echo e($product->name); ?>"
                                                data-price="<?php echo e($product->price); ?>"
                                                data-quantity="<?php echo e($product->quantity); ?>"
                                                data-description="<?php echo e($product->description ?? ''); ?>">
                                                <i class="bi bi-pencil me-2 text-primary"></i> Modifier
                                            </a>
                                        </li>
                                        <li>
                                            <form action="<?php echo e(route('products.destroy', $product->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="dropdown-item text-danger w-100 text-start border-0 bg-transparent" onclick="return confirm('Supprimer ce produit ?')">
                                                    <i class="bi bi-trash me-2"></i> Supprimer
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Aucun produit en stock. Créez une commande pour commencer.</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($products->links()); ?>

    </div>
</div>

<!-- Modal Ajout Produit -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #ffffff;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Enregistrer un Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form action="<?php echo e(route('stock.products.store')); ?>" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled=true;" autocomplete="new-password">
                    <?php echo csrf_field(); ?>

                    <!-- Nom du Produit -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted uppercase tracking-wider">Nom du produit</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" placeholder="Entrez un produit" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Prix -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted uppercase tracking-wider">Prix Unitaire</label>
                            <div class="input-group">
                                <input type="number" name="price" step="0.01" class="form-control bg-light border-0 py-2.5 rounded-start-3" placeholder="0.00" required>
                                <span class="input-group-text bg-light border-0 text-muted small">FCFA</span>
                            </div>
                        </div>
                        <!-- Quantité Initiale -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted uppercase tracking-wider">Stock Initial</label>
                            <input type="number" name="quantity" class="form-control bg-light border-0 py-2.5 rounded-3" placeholder="0" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted uppercase tracking-wider">Description (Optionnel)</label>
                        <textarea name="description" class="form-control bg-light border-0 rounded-3" rows="2" placeholder="Détails..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2.5 fw-bold shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="bi bi-check-lg me-1"></i> Valider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modification Produit -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background: #ffffff;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Modifier le Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form id="editProductForm" method="POST" autocomplete="off" data-action="<?php echo e(route('products.update', ':id')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <!-- Nom du Produit -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted uppercase tracking-wider">Nom du produit</label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-light border-0 py-2.5 rounded-3" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Prix -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted uppercase tracking-wider">Prix Unitaire</label>
                            <div class="input-group">
                                <input type="number" name="price" id="edit_price" step="0.01" class="form-control bg-light border-0 py-2.5 rounded-start-3" required>
                                <span class="input-group-text bg-light border-0 text-muted small">FCFA</span>
                            </div>
                        </div>
                        <!-- Quantité -->
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted uppercase tracking-wider">Stock Actuel</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control bg-light border-0 py-2.5 rounded-3" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted uppercase tracking-wider">Description</label>
                        <textarea name="description" id="edit_description" class="form-control bg-light border-0 rounded-3" rows="2"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning py-2.5 fw-bold shadow-sm text-white" style="border-radius: 12px; border: none;">
                            <i class="bi bi-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Statistiques -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Analyse des Flux</h5>
                    <p class="text-muted small mb-0">Comparatif Ventes vs Approvisionnements (30 derniers jours)</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="position-relative" style="height: 400px; width: 100%;">
                    <canvas id="fluxChart"></canvas>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e(route('dashboard.movements')); ?>" class="btn btn-light rounded-pill px-4 fw-bold text-primary">
                        <i class="bi bi-table me-2"></i>Voir l'historique détaillé
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALE COMMANDE ================= -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Créer une commande d'achat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('stock.order.store_order')); ?>" method="POST" autocomplete="new-password">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4 italic">Sélectionnez les produits enregistrés pour votre commande.</p>

                    <!-- LISTE PRODUITS DYNAMIQUE -->
                    <label class="form-label fw-bold small text-muted uppercase tracking-widest">Détails des produits</label>
                    <div id="products-wrapper">
                        <div class="product-row mb-4 d-flex gap-3 align-items-start">
                            <div class="flex-grow-1 suggestion-container">
                                <input type="text" name="products[0][name]"
                                    class="form-control product-input border-0 py-2 shadow-sm bg-light"
                                    placeholder="Nom du produit..." autocomplete="off" required
                                    value="<?php echo e(old('products.0.name')); ?>">

                                <div class="suggestion-menu shadow-lg"></div>
                                <div class="error-msg text-danger font-bold uppercase italic" style="display:none;">
                                    <i class="bi bi-exclamation-circle"></i> Produit non enregistré
                                </div>
                            </div>
                            <div style="width: 120px;">
                                <input type="number" name="products[0][quantity]" placeholder="Qté"
                                    class="form-control border-0 py-2 shadow-sm bg-light" required min="1"
                                    value="<?php echo e(old('products.0.quantity')); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" id="add-product" class="btn btn-sm btn-outline-primary rounded-pill">+ Ligne</button>
                        <button type="button" id="remove-product" class="btn btn-sm btn-outline-danger rounded-pill" style="display:none;">- Supprimer</button>
                    </div>

                    <!-- CHAMPS FOURNISSEUR ET DATE (BIEN LÀ !) -->
                    <div class="row pt-3 border-top">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted uppercase tracking-widest">Fournisseur</label>
                            <select name="supplier_manager_id" class="form-select border-0 shadow-sm py-2 bg-light" required>
                                <option value="">Choisir...</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <option value="<?php echo e($s->id); ?>" <?php echo e((string) old('supplier_manager_id') === (string) $s->id ? 'selected' : ''); ?>>
                                    <?php echo e($s->name); ?>

                                </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-muted uppercase tracking-widest">Date de livraison</label>
                            <input type="date" name="delivery_date" class="form-control border-0 shadow-sm py-2 bg-light" required value="<?php echo e(old('delivery_date')); ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none;">
                        Confirmer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ---- DROPDOWN VANILLA (sans Bootstrap JS / sans conflit Alpine) ----
        function toggleDropdown(btn) {
            const menu = btn.nextElementSibling;
            const isOpen = menu.style.display === 'block';

            // Fermer tous les dropdowns ouverts
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');

            // Ouvrir celui-ci s'il était fermé
            if (!isOpen) menu.style.display = 'block';
        }

        // Fermer si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');
            }
        });

        // Ouvre le modal d'édition via Bootstrap JS directement
        function openEditModal(btn) {
            // Fermer le dropdown
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.style.display = 'none');

            // Remplir le formulaire
            const id          = btn.getAttribute('data-id');
            const form        = document.getElementById('editProductForm');
            form.action       = form.getAttribute('data-action').replace(':id', id);

            document.getElementById('edit_name').value        = btn.getAttribute('data-name');
            document.getElementById('edit_price').value       = btn.getAttribute('data-price');
            document.getElementById('edit_quantity').value    = btn.getAttribute('data-quantity');
            document.getElementById('edit_description').value = btn.getAttribute('data-description') || '';

            // Ouvrir le modal Bootstrap
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
        const hasOrderErrors = <?php echo json_encode($hasOrderErrors, 15, 512) ?>;
        if (hasOrderErrors) {
            const orderModalElement = document.getElementById('orderModal');
            if (orderModalElement) {
                const orderModal = bootstrap.Modal.getOrCreateInstance(orderModalElement);
                orderModal.show();
            }
        }

        const products = <?php echo json_encode($products -> pluck('name'), 15, 512) ?>;
        const wrapper = document.getElementById('products-wrapper');
        const addBtn = document.getElementById('add-product');
        const removeBtn = document.getElementById('remove-product');

        function initSuggestion(input) {
            const menu = input.nextElementSibling;
            const error = menu.nextElementSibling;

            input.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                menu.innerHTML = '';
                if (!val) {
                    menu.style.display = 'none';
                    return;
                }

                const matches = products.filter(p => p.toLowerCase().includes(val));
                if (matches.length > 0) {
                    error.style.display = 'none';
                    menu.style.display = 'block';
                    matches.forEach(match => {
                        const div = document.createElement('div');
                        div.textContent = match;
                        div.onclick = function() {
                            input.value = match;
                            menu.style.display = 'none';
                        };
                        menu.appendChild(div);
                    });
                } else {
                    menu.style.display = 'none';
                    error.style.display = 'block';
                }
            });
            document.addEventListener('click', (e) => {
                if (e.target !== input) menu.style.display = 'none';
            });
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
                <div class="error-msg text-danger font-bold uppercase italic" style="display:none;"><i class="bi bi-exclamation-circle"></i> Produit non enregistré</div>
            </div>
            <div style="width: 120px;"><input type="number" name="products[${rowIdx}][quantity]" placeholder="Qté" class="form-control border-0 py-2 shadow-sm bg-light" required min="1"></div>`;
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

        // --- LOGIQUE DU GRAPHIQUE STATISTIQUE (Chart.js) ---
        const statsModal = document.getElementById('statsModal');
        let myChart = null;

        if (statsModal) {
            statsModal.addEventListener('shown.bs.modal', function() {
                if (myChart) return; // Empêche de recharger le graph s'il existe déjà

                fetch('<?php echo e(route("stock.chart.data")); ?>')
                    .then(response => response.json())
                    .then(data => {
                        const ctx = document.getElementById('fluxChart').getContext('2d');

                        // Dégradé ROUGE pour les Sorties (Ventes)
                        const gradientSales = ctx.createLinearGradient(0, 0, 0, 300);
                        gradientSales.addColorStop(0, 'rgba(239, 68, 68, 0.4)'); // Rouge semi-transparent
                        gradientSales.addColorStop(1, 'rgba(239, 68, 68, 0.0)'); // Transparent

                        // Dégradé VERT pour les Entrées (Approvisionnements)
                        const gradientSupply = ctx.createLinearGradient(0, 0, 0, 300);
                        gradientSupply.addColorStop(0, 'rgba(34, 197, 94, 0.4)'); // Vert semi-transparent
                        gradientSupply.addColorStop(1, 'rgba(34, 197, 94, 0.0)'); // Transparent

                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                        label: 'Sorties (Ventes)',
                                        data: data.sales,
                                        borderColor: '#ef4444', // Rouge vif
                                        backgroundColor: gradientSales,
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4, // Courbe lisse
                                        pointRadius: 3,
                                        pointHoverRadius: 6
                                    },
                                    {
                                        label: 'Entrées (Approvisionnements)',
                                        data: data.supplies,
                                        borderColor: '#22c55e', // Vert vif
                                        backgroundColor: gradientSupply,
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 3,
                                        pointHoverRadius: 6
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 10000,
                                        grid: {
                                            borderDash: [2, 2]
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                },
                                interaction: {
                                    mode: 'nearest',
                                    axis: 'x',
                                    intersect: false
                                }
                            }
                        });
                    })
                    .catch(err => console.error("Erreur chargement graph:", err));
            });
        }
    });
</script>

<style>
    .suggestion-container {
        position: relative;
    }

    .suggestion-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: white;
        z-index: 2000;
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/stock.blade.php ENDPATH**/ ?>