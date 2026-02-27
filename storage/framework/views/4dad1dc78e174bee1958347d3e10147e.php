<!-- resources/views/suppliers/orders_pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mes Commandes PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Mes Commandes</h2>
    <table>
        <thead>
            <tr>
                <th>ID Commande</th>
                <th>Date Commande</th>
                <th>Date Livraison</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <tr>
                <td><?php echo e($order->id); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($order->created_at)->format('d/m/Y')); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')); ?></td>
                <td><?php echo e($order->status); ?></td>
            </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/suppliers/orders_pdf.blade.php ENDPATH**/ ?>