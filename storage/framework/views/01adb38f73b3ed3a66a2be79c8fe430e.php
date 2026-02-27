

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

    .product-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
    }

    .product-image-placeholder {
        height: 180px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ced4da;
        font-size: 3rem;
    }

    .product-card .card-title {
        font-size: 1rem;
        font-weight: 600;
    }

    #cart-items-display .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
    }

    .sticky-cart {
        position: sticky;
        top: 20px;
    }

    .payment-option-card {
        cursor: pointer;
        border: 2px solid #f8f9fa;
        transition: all 0.3s;
    }

    .payment-option-card.active {
        border-color: #667eea;
        background-color: #f0f4ff;
    }
</style>

<div class="container py-4">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Passer une nouvelle commande</h1>
            <p class="mb-0 opacity-75">Sélectionnez vos articles et choisissez votre mode de règlement.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('dashboards.clients')); ?>" class="btn btn-outline-light fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <form action="<?php echo e(route('orders.store')); ?>" method="POST" id="order-form">
        <?php echo csrf_field(); ?>
        <div class="row g-5">

            <div class="col-lg-7">
                <div class="mb-4">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="product-search" class="form-control border-0 py-2" placeholder="Rechercher un produit...">
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isEmpty()): ?>
                    <div class="alert alert-info">Aucun produit disponible.</div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-md-2 g-4" id="product-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="col product-item">
                            <div class="card h-100 product-card border-0 shadow-sm">
                                <div class="product-image-placeholder text-primary opacity-50">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1 text-dark"><?php echo e($product->name); ?></h5>
                                    <p class="text-primary fw-bold fs-5 mb-2"><?php echo e(number_format($product->price, 0, ',', ' ')); ?> FCFA</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="input-group input-group-sm w-50">
                                            <input type="number" class="form-control quantity-input text-center" value="1" min="1" max="<?php echo e($product->quantity); ?>">
                                            <button class="btn btn-primary add-to-cart-btn" type="button" 
                                                data-product-id="<?php echo e($product->id); ?>" 
                                                data-product-name="<?php echo e($product->name); ?>" 
                                                data-product-price="<?php echo e($product->price); ?>" 
                                                data-product-stock="<?php echo e($product->quantity); ?>">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Stock: <?php echo e($product->quantity); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="col-lg-5">
                <div class="card sticky-cart shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-cart3 me-2 text-primary"></i>Récapitulatif</h5>
                    </div>
                    
                    <div class="card-body px-4" id="cart-items-display">
                        <p class="text-muted text-center py-4">Votre panier est vide.</p>
                    </div>

                    <div id="cart-hidden-inputs"></div>

                    <div class="px-4 pb-4">
                        <hr class="my-4">
                        
                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">Mode de règlement</label>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_type" id="type_comptant" value="comptant" checked autocomplete="off">
                                <label class="btn btn-outline-primary w-100 py-2 fw-bold" for="type_comptant">
                                    <i class="bi bi-cash-stack me-1"></i> Comptant
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="payment_type" id="type_credit" value="credit" autocomplete="off">
                                <label class="btn btn-outline-warning w-100 py-2 fw-bold" for="type_credit">
                                    <i class="bi bi-clock-history me-1"></i> À Crédit
                                </label>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Sous-total</span>
                                <span id="cart-subtotal">0 FCFA</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total à payer</span>
                                <span id="cart-total" class="text-primary">0 FCFA</span>
                            </div>
                            <div id="credit-note" class="mt-2 small text-warning fw-bold" style="display:none;">
                                <i class="bi bi-info-circle me-1"></i> Commande soumise à validation de crédit.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow" id="validate-order-btn" disabled>
                            Confirmer ma commande
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cart = {};
        const cartDisplayContainer = document.getElementById('cart-items-display');
        const cartHiddenInputsContainer = document.getElementById('cart-hidden-inputs');
        const cartTotalSpan = document.getElementById('cart-total');
        const cartSubtotalSpan = document.getElementById('cart-subtotal');
        const validateOrderBtn = document.getElementById('validate-order-btn');
        const productSearchInput = document.getElementById('product-search');
        const productItems = document.querySelectorAll('.product-item');
        const paymentRadios = document.querySelectorAll('input[name="payment_type"]');
        const creditNote = document.getElementById('credit-note');

        // Gérer le changement de type de paiement
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                creditNote.style.display = (this.value === 'credit') ? 'block' : 'none';
            });
        });

        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const qtyInput = this.previousElementSibling;
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productPrice = parseFloat(this.dataset.productPrice);
                const productStock = parseInt(this.dataset.productStock);
                const quantity = parseInt(qtyInput.value, 10);

                if (quantity > 0) {
                    const currentQty = cart[productId] ? cart[productId].quantity : 0;
                    if (currentQty + quantity > productStock) {
                        alert('Stock insuffisant !');
                        return;
                    }

                    if (cart[productId]) {
                        cart[productId].quantity += quantity;
                    } else {
                        cart[productId] = { name: productName, price: productPrice, quantity: quantity };
                    }
                    updateCart();
                    qtyInput.value = 1;
                }
            });
        });

        function updateCart() {
            cartDisplayContainer.innerHTML = '';
            cartHiddenInputsContainer.innerHTML = '';
            let total = 0;
            let index = 0;

            const ids = Object.keys(cart);
            if (ids.length === 0) {
                cartDisplayContainer.innerHTML = '<p class="text-muted text-center py-4">Votre panier est vide.</p>';
                validateOrderBtn.disabled = true;
            } else {
                const list = document.createElement('div');
                list.className = 'list-group list-group-flush';

                ids.forEach(id => {
                    const item = cart[id];
                    total += item.price * item.quantity;

                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item bg-transparent border-0 px-0 d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        <div>
                            <div class="fw-bold small">${item.name}</div>
                            <small class="text-muted">${item.quantity} x ${item.price.toLocaleString()} FCFA</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-from-cart" data-id="${id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                    list.appendChild(listItem);

                    // Inputs cachés
                    const inputId = `<input type="hidden" name="products[${index}][id]" value="${id}">`;
                    const inputQty = `<input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">`;
                    cartHiddenInputsContainer.innerHTML += inputId + inputQty;
                    index++;
                });
                cartDisplayContainer.appendChild(list);
                validateOrderBtn.disabled = false;
            }

            const formattedTotal = total.toLocaleString('fr-FR') + ' FCFA';
            cartTotalSpan.textContent = formattedTotal;
            cartSubtotalSpan.textContent = formattedTotal;

            // Suppression
            document.querySelectorAll('.remove-from-cart').forEach(btn => {
                btn.addEventListener('click', function() {
                    delete cart[this.dataset.id];
                    updateCart();
                });
            });
        }

        // Recherche
        productSearchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            productItems.forEach(item => {
                const name = item.querySelector('.card-title').textContent.toLowerCase();
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\mopao\Application_Web_de_gestion_des_ventes_et_inventaire\resources\views/orders/create.blade.php ENDPATH**/ ?>