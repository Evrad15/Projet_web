<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\{
    EmployeeRegistrationController,
    AuthenticatedSessionController
};
use App\Http\Controllers\{
    ProductController,
    ClientController,
    ProfileController,
    SupplierController,
    SaleController,
    PaymentController,
    HomeController,
    StockController,
    AccountingController,
    ClientOrderController,
    ExpenseController,
    ReportController,
};

// =============================================================================
// ACCÈS PUBLIC
// =============================================================================

Route::get('/', fn() => redirect('/login'));

// =============================================================================
// ESPACE SÉCURISÉ — Tout utilisateur connecté et vérifié
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        // Si l'utilisateur n'est pas connecté, le match plantera.
        // Il est préférable de s'assurer qu'il est authentifié.
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return match (Auth::user()->role) {
            'admin'            => redirect()->route('dashboard.stock'), // ou une route admin dédiée
            'sales_manager'    => redirect()->route('dashboard.sales'),
            'sales_employee'   => redirect()->route('dashboard.sales_employee'),
            'accountant'       => redirect()->route('dashboard.accounting'),
            'stock_manager'    => redirect()->route('dashboard.stock'),
            'supplier_manager' => redirect()->route('supplier.orders'),
            'client'           => redirect()->route('dashboards.clients'),
            default            => redirect()->route('login'),
        };
    })->middleware(['auth'])->name('dashboard');

    // Profil (accessible à tous)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Produits en lecture seule (accessible à tous les rôles internes)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // =========================================================================
    // ESPACE COMMERCIAL — Responsable + Employé
    // =========================================================================

    Route::middleware(['role:sales_manager,sales_employee'])->group(function () {

        // --- Clients (lecture + création partagées) ---
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/solvency', [ClientController::class, 'checkSolvency'])->name('clients.solvency');
        Route::get('/register/client', [ClientRegistrationController::class, 'show'])->name('register.client');
        // --- Ventes : opérations communes (lecture + création + impression) ---
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    });

    // =========================================================================
    // ESPACE COMMERCIAL — Responsable uniquement
    // =========================================================================

    Route::middleware(['role:sales_manager'])->group(function () {

        // Tableau de bord manager
        Route::get('/dashboard/sales', [SaleController::class, 'index'])->name('dashboard.sales');
        Route::get('/dashboard/sales/employees', [SaleController::class, 'employees'])->name('dashboard.sales.employees');
        Route::get('/dashboard/sales/performance', [SaleController::class, 'performance'])->name('dashboard.sales.performance');
        Route::post('/dashboard/sales/employees', [SaleController::class, 'storeEmployee'])->name('employees.store');

        // Ventes : modification et suppression réservées au manager
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
        Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

        // ClientOrders : depuis le dashboard commandes
        Route::put('/client-orders/{id}', [SaleController::class, 'update'])->name('client-orders.update');
        Route::delete('/client-orders/{id}', [ClientOrderController::class, 'destroy'])->name('client-orders.destroy');

        // Clients : modification et suppression réservées au manager
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // =========================================================================
    // ESPACE COMMERCIAL — Employé uniquement
    // =========================================================================

    Route::middleware(['role:sales_employee'])->group(function () {

        // Tableau de bord employé (voit uniquement ses propres ventes)
        Route::get('/dashboard/my-sales', [SaleController::class, 'employeeIndex'])->name('dashboard.sales_employee');

        // Annulation d'une vente par l'employé (contrôleur doit vérifier la règle des 24h ET l'appartenance)
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

        // Assignation d'une commande globale (contrôleur doit vérifier qu'elle n'est pas déjà assignée)
        Route::post('/client-orders/{order}/assign', [SaleController::class, 'assignOrder'])->name('client-orders.assign');
    });

    // =========================================================================
    // ESPACE COMPTABLE
    // =========================================================================

    Route::middleware(['role:accountant'])->group(function () {

        Route::get('/dashboard/accounting', [AccountingController::class, 'index'])->name('dashboard.accounting');

        // Ventes en lecture seule pour le comptable (méthode dédiée dans le contrôleur)
        Route::get('/accounting/sales', [SaleController::class, 'accountantIndex'])->name('sales.index.accountant');

        // Dépenses
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        // Export de rapports
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });

    // =========================================================================
    // PAIEMENTS — Comptable et Responsable commercial
    // =========================================================================

    Route::middleware(['role:accountant,sales_manager'])->group(function () {
        Route::resource('payments', PaymentController::class);
    });

    // =========================================================================
    // ESPACE STOCK
    // =========================================================================

    Route::middleware(['role:stock_manager,admin'])->group(function () {

        Route::get('/dashboard/stock', [StockController::class, 'index'])->name('dashboard.stock');
        Route::get('/stock/index', [StockController::class, 'index'])->name('stock.index');

        Route::get('/dashboard/stock/movements', [StockController::class, 'movements'])->name('dashboard.movements');
        Route::get('/stock/order-dashboard', [StockController::class, 'ordersDashboard'])->name('stock.order.dashboard');
        Route::get('/stock/chart-data', [StockController::class, 'getChartData'])->name('stock.chart.data');
        Route::post('/stock/orders/reset-hidden', [StockController::class, 'resetHiddenOrders'])->name('stock.order.reset-hidden');
        Route::patch('/stock/orders/bulk-cancel', [StockController::class, 'bulkCancelOrder'])->name('stock.order.bulk.cancel');
        Route::patch('/stock/orders/bulk-hide', [StockController::class, 'bulkHideOrder'])->name('stock.order.bulk.hide');
        Route::post('/dashboard/stock/orders', [StockController::class, 'storeOrder'])->name('stock.order.store_order');
        Route::get('/stock/edit/{id}', [StockController::class, 'editOrder'])->name('stock.edit');
        Route::put('/stock/update/{id}', [StockController::class, 'updateOrder'])->name('stock.update');
        Route::delete('/stock/destroy/{id}', [StockController::class, 'destroyOrder'])->name('stock.destroy');
        Route::post('/dashboard/stock/products', [StockController::class, 'storeProduct'])->name('stock.products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::patch('/stock/deliver/{id}', [StockController::class, 'deliverOrder'])->name('stock.deliver');
    });

    // =========================================================================
    // ESPACE FOURNISSEUR
    // =========================================================================

    Route::middleware(['role:supplier_manager'])->group(function () {

        Route::get('/dashboard/supplier', [SupplierController::class, 'index'])->name('supplier.orders');
        Route::get('/suppliers/export-pdf', [SupplierController::class, 'exportOrdersPdf'])->name('suppliers.export.pdf');
    });

    // =========================================================================
    // PORTAIL CLIENT
    // =========================================================================

    Route::middleware(['role:client'])->group(function () {

        Route::get('/mon-espace', function () {
            $user     = Auth::user();
            $clientId = $user->client_id;

            // Sécurité : on refuse l'accès si le lien client n'est pas défini
            if (!$clientId) {
                abort(403, 'Votre compte n\'est pas lié à un profil client.');
            }

            $myPurchases = \App\Models\Sale::where('client_id', $clientId)->get();
            $totalSpent  = $myPurchases->sum('total');
            $totalPaid   = $myPurchases->sum('paid_amount');
            $balance     = $totalSpent - $totalPaid;

            return view('dashboards.clients', compact('myPurchases', 'totalSpent', 'balance'));
        })->name('dashboards.clients');

        Route::resource('client/orders', ClientOrderController::class)->names('orders');
        Route::get('/client/invoices', [ClientOrderController::class, 'indexInvoices'])->name('invoices.index');
        Route::get('/mes-factures/{sale}', [ClientOrderController::class, 'showInvoice'])->name('invoices.show');
    });
});
// CETTE SECTION DOIT ÊTRE HORS DU GROUPE MIDDLEWARE AUTH
// Car l'utilisateur n'a pas encore de compte au moment de cliquer sur le lien
Route::get('/invitation/accept', [EmployeeRegistrationController::class, 'create'])
    ->name('register.employee')
    ->middleware(['signed', 'guest', 'throttle:6,1']); // On garde 'signed' pour la sécurité

Route::post('/invitation/accept', [EmployeeRegistrationController::class, 'store'])
    ->name('register.employee.store')
    ->middleware(['signed', 'guest', 'throttle:6,1']);
// =============================================================================
// AUTHENTIFICATION
// =============================================================================

require __DIR__ . '/auth.php';
