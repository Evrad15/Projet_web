<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\ClientOrder;
use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // ── Liste des commandes clients (ClientOrder) ──
        $query = ClientOrder::with(['client', 'items.product']);

        if (auth()->user()->role === 'sales_employee') {
            $query->where('assigned_to', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('items.product', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->latest()->paginate(10)->withQueryString();

        // ── KPIs basés sur Sale ──
        $totalRevenue    = Sale::sum('total');
        $totalSalesCount = Sale::count();
        $averageBasket   = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;

        // ── Chart basé sur Sale ──
        $salesHistory = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartLabels = $salesHistory->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));
        $chartData   = $salesHistory->pluck('total');

        $clients  = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('dashboards.sales', compact(
            'sales', 'totalRevenue', 'totalSalesCount', 'averageBasket',
            'clients', 'products', 'chartLabels', 'chartData'
        ));
    }

    public function employees()
    {
        $employees = User::where('role', 'sales_employee')->latest()->paginate(10);
        return view('dashboards.employees', compact('employees'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'sales_employee',
        ]);

        return redirect()->route('dashboard.sales.employees')->with('success', 'Employé ajouté avec succès.');
    }

    public function performance()
    {
        $employees = User::where('role', 'sales_employee')->get();

        $stats = $employees->map(function ($user) {
            $query = Sale::where('sales_employee_id', $user->id);
            return [
                'name'          => $user->name,
                'sales_count'   => (clone $query)->count(),
                'total_revenue' => (clone $query)->sum('total'),
                'clients_count' => (clone $query)->distinct('client_id')->count('client_id'),
                'revenue_today' => (clone $query)->whereDate('created_at', now()->today())->sum('total'),
                'revenue_week'  => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total'),
                'revenue_month' => (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total'),
            ];
        })->sortByDesc('total_revenue');

        $chartLabels = $stats->pluck('name')->take(5)->values()->all();
        $chartData   = $stats->pluck('revenue_month')->take(5)->values()->all();

        return view('dashboards.performance', compact('stats', 'chartLabels', 'chartData'));
    }

    public function employeeIndex(Request $request)
    {
        $query = Sale::where('sales_employee_id', auth()->id())->with(['client', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', fn($s) => $s->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('items.product', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $sales         = $query->latest()->paginate(10)->withQueryString();
        $todaySales    = Sale::where('sales_employee_id', auth()->id())->whereDate('created_at', today())->count();
        $todayRevenue  = Sale::where('sales_employee_id', auth()->id())->whereDate('created_at', today())->sum('total');
        $clients       = Client::orderBy('name')->get();
        $products      = Product::where('quantity', '>', 0)->orderBy('name')->get();
        $availableSales = ClientOrder::where('status', 'en attente')->whereNull('assigned_to')->with(['client', 'items'])->latest()->get();
        $takenSales     = ClientOrder::where('assigned_to', auth()->id())->where('status', 'en traitement')->with(['client', 'items'])->latest()->get();

        return view('dashboards.sales_employee', compact('sales', 'todaySales', 'todayRevenue', 'clients', 'products', 'availableSales', 'takenSales'));
    }

    public function accountantIndex(Request $request)
    {
        $sales = Sale::with(['client', 'items.product'])->latest()->paginate(20)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $clients  = Client::all();
        $products = Product::all();
        return view('sales.create', compact('clients', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'payment_method'      => 'required|string',
            'amount_paid'         => 'required|numeric|min:0',
            'client_order_id'     => 'nullable',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                $finalTotal = 0;

                $sale = Sale::create([
                    'client_id'         => $validated['client_id'],
                    'sales_employee_id' => auth()->id(),
                    'total'             => 0,
                    'paid_amount'       => $validated['amount_paid'],
                    'status'            => 'pending',
                    'client_order_id'   => $request->client_order_id,
                ]);

                foreach ($validated['products'] as $item) {
                    $product = Product::findOrFail($item['id']);
                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->name}.");
                    }

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'price'      => $product->price,
                    ]);

                    $product->decrement('quantity', $item['quantity']);
                    $finalTotal += $product->price * $item['quantity'];
                }

                $status = ($validated['amount_paid'] >= $finalTotal) ? 'completed' : 'credit';
                $sale->update(['total' => $finalTotal, 'status' => $status]);

                if ($request->filled('client_order_id')) {
                    ClientOrder::where('id', $request->client_order_id)->update([
                        'status'       => $status,
                        'total_amount' => $finalTotal,
                    ]);
                }

                return redirect()->route(
                    auth()->user()->role === 'sales_employee' ? 'dashboard.sales_employee' : 'dashboard.sales'
                )->with('success', 'Vente enregistrée avec succès.');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Sale $sale)
    {
        if (auth()->user()->role === 'sales_employee' && $sale->sales_employee_id !== auth()->id()) {
            return redirect()->route('dashboard.sales_employee')->with('error', 'Accès interdit.');
        }

        if (request()->has('print')) {
            return view('sales.print', compact('sale'));
        }

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $clients  = Client::all();
        $products = Product::all();
        return view('sales.edit', compact('sale', 'clients', 'products'));
    }

    /**
     * Update porte sur ClientOrder (onglet Commandes du dashboard).
     */
    public function update(Request $request, $id)
    {
        $order = ClientOrder::find($id);

        if ($order) {
            $request->validate([
                'client_id'           => 'required|exists:clients,id',
                'status'              => 'nullable|string',
                'products'            => 'nullable|array',
                'products.*.id'       => 'required_with:products|exists:products,id',
                'products.*.quantity' => 'required_with:products|integer|min:1',
            ]);

            // Mettre à jour l'entête
            $order->update([
                'client_id' => $request->client_id,
                'status'    => $request->status ?? $order->status,
            ]);

            // Mettre à jour les items si fournis
            if ($request->filled('products')) {
                $order->items()->delete();
                $newTotal = 0;

                foreach ($request->products as $item) {
                    $product = Product::findOrFail($item['id']);
                    $subtotal = $product->price * $item['quantity'];
                    $newTotal += $subtotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity'   => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal'   => $subtotal,
                    ]);
                }

                $order->update(['total_amount' => $newTotal]);
            }

            return redirect()->route('dashboard.sales')->with('success', 'Commande mise à jour.');
        }

        // Fallback : Sale classique
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                    StockMovement::create([
                        'product_id'  => $item->product_id,
                        'user_id'     => auth()->id(),
                        'type'        => 'correction',
                        'quantity'    => $item->quantity,
                        'description' => "Correction Vente #{$sale->id}",
                    ]);
                }
            }

            $sale->items()->delete();
            $finalTotal = 0;

            foreach ($validated['products'] as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                if ($product->quantity < $itemData['quantity']) {
                    throw new \Exception("Stock insuffisant pour {$product->name}.");
                }

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $itemData['quantity'],
                    'price'      => $product->price,
                ]);

                $product->decrement('quantity', $itemData['quantity']);
                $finalTotal += $product->price * $itemData['quantity'];
            }

            $sale->update(['client_id' => $validated['client_id'], 'total' => $finalTotal]);
            DB::commit();

            return redirect()->route('dashboard.sales')->with('success', 'Vente mise à jour.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Sale $sale)
    {
        $user = auth()->user();
        if ($user->role === 'sales_employee' && $sale->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Délai dépassé.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                    StockMovement::create([
                        'product_id'  => $item->product_id,
                        'user_id'     => auth()->id(),
                        'type'        => 'return',
                        'quantity'    => $item->quantity,
                        'description' => "Annulation Vente #{$sale->id}",
                    ]);
                }
            }
            $sale->delete();
        });

        return redirect()->back()->with('success', 'Vente annulée.');
    }

    public function assignOrder(ClientOrder $order)
    {
        if ($order->status !== 'en attente' || $order->assigned_to !== null) {
            return redirect()->back()->with('error', 'Commande déjà prise.');
        }

        $order->update([
            'assigned_to' => auth()->id(),
            'status'      => 'en traitement',
        ]);

        return redirect()->route('dashboard.sales_employee')->with('success', "Commande #{$order->id} assignée.");
    }
}