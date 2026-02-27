<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildSchema()); ?>

</div>
<?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\vendor\filament\schemas\resources\views/components/grid.blade.php ENDPATH**/ ?>