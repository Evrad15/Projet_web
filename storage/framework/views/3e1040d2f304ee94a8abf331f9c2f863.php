

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

    .table thead {
        background-color: #f8f9fa;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
</style>

<div class="d-flex">
    <!-- SIDEBAR -->
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end" style="width: 260px; min-height: calc(100vh - 65px);">
        <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <span class="fs-5 fw-bold text-primary"><i class="bi bi-grid-1x2-fill me-2"></i>Menu</span>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard.sales') ? 'active' : 'link-dark'); ?> fw-bold">
                    <i class="bi bi-cart-check me-2"></i> Commandes
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
                <a href="<?php echo e(route('clients.index')); ?>" class="nav-link link-dark fw-bold">
                    <i class="bi bi-person-badge me-2"></i> Clients
                </a>
            </li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="flex-grow-1 p-4" style="background-color: #f8f9fa;">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h1 class="fw-bold mb-1">Équipe Commerciale</h1>
                    <p class="mb-0 opacity-75">Gestion des employés et accès</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-light text-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="bi bi-person-plus-fill me-2"></i>Ajouter un employé
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date d'ajout</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            <?php echo e(substr($employee->name, 0, 1)); ?>

                                        </div>
                                        <span class="fw-bold"><?php echo e($employee->name); ?></span>
                                    </div>
                                </td>
                                <td><?php echo e($employee->email); ?></td>
                                <td><span class="badge bg-info-subtle text-info rounded-pill px-3">Commercial</span></td>
                                <td><?php echo e($employee->created_at->format('d/m/Y')); ?></td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-secondary rounded-circle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Aucun employé commercial trouvé.</td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4"><?php echo e($employees->links()); ?></div>
    </div>

    <!-- Modal Nouvel Employé -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form action="<?php echo e(route('employees.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold">Nouvel Employé Commercial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                        <div class="alert alert-danger border-0 rounded-4 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Veuillez vérifier les informations saisies.
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Nom complet</label>
                            <input type="text" class="form-control bg-light border-0 py-2" name="name" value="<?php echo e(old('name')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Adresse e-mail</label>
                            <input type="email" class="form-control bg-light border-0 py-2" name="email" value="<?php echo e(old('email')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Mot de passe (temporaire)</label>
                            <input type="password" class="form-control bg-light border-0 py-2" name="password" required placeholder="Minimum 8 caractères">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-link text-muted text-decoration-none fw-semibold" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2" style="background: var(--primary-gradient); border: none;">Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if($errors -> any()): ?>
    var addEmployeeModal = new bootstrap.Modal(document.getElementById('addEmployeeModal'));
    addEmployeeModal.show();
    <?php endif; ?>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/employees.blade.php ENDPATH**/ ?>