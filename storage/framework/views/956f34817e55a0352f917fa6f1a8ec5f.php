

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
                <a href="<?php echo e(route('dashboard.sales')); ?>" class="nav-link link-dark fw-bold">
                    <i class="bi bi-cart-check me-2"></i> Commandes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales.employees')); ?>" class="nav-link link-dark fw-bold">
                    <i class="bi bi-people me-2"></i> Employés
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="<?php echo e(route('dashboard.sales.performance')); ?>" class="nav-link active fw-bold">
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
        <div class="page-header d-flex align-items-center">
            <a href="javascript:history.back()" class="btn btn-light btn-sm rounded-circle me-3 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Retour">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h1 class="fw-bold mb-1">Supervision des Performances</h1>
                <p class="mb-0 opacity-75">Analyse comparative de l'équipe commerciale</p>
            </div>
        </div>

        <!-- Graphique -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-4">Top 5 - Chiffre d'Affaires du Mois</h5>
                <div style="height: 300px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau Détaillé -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Détail par Employé</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Employé</th>
                            <th class="text-center">Clients Gérés</th>
                            <th class="text-center">Ventes Totales</th>
                            <th class="text-end">CA Jour</th>
                            <th class="text-end">CA Semaine</th>
                            <th class="text-end">CA Mois</th>
                            <th class="text-end pe-4">CA Global</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?php echo e($stat['name']); ?></td>
                            <td class="text-center"><span class="badge bg-secondary rounded-pill"><?php echo e($stat['clients_count']); ?></span></td>
                            <td class="text-center"><?php echo e($stat['sales_count']); ?></td>
                            <td class="text-end text-muted"><?php echo e(number_format($stat['revenue_today'], 0, ',', ' ')); ?></td>
                            <td class="text-end text-muted"><?php echo e(number_format($stat['revenue_week'], 0, ',', ' ')); ?></td>
                            <td class="text-end fw-bold text-dark"><?php echo e(number_format($stat['revenue_month'], 0, ',', ' ')); ?></td>
                            <td class="text-end pe-4 fw-bold text-success"><?php echo e(number_format($stat['total_revenue'], 0, ',', ' ')); ?> FCFA</td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucune donnée disponible.</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                datasets: [{
                    label: "CA du Mois (FCFA)",
                    data: <?php echo json_encode($chartData, 15, 512) ?>,
                    backgroundColor: 'rgba(118, 75, 162, 0.7)',
                    borderColor: '#764ba2',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/dashboards/performance.blade.php ENDPATH**/ ?>