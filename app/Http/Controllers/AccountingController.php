<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        // 1. Vérification de la présence de la colonne paid_amount pour éviter les crashs
        $hasPaidAmount = Schema::hasColumn('sales', 'paid_amount');
        $paidCol = $hasPaidAmount ? 'paid_amount' : '0';

        // ── Paiements (avec filtres) ─────────────────────────────────
        $paymentsQuery = Payment::with(['client', 'sale'])
            ->when($request->search_client, fn($q, $s) =>
                $q->whereHas('client', fn($c) => $c->where('name', 'like', "%$s%"))
            )
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to,   fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest();

        $payments = $paymentsQuery->paginate(15)->withQueryString();

        // ── Impayés (ventes non soldées) ─────────────────────────────
        // Utilisation de 'total' car c'est le nom dans ton SQL
        $impayes = Sale::with('client')
            ->whereRaw("(total - COALESCE($paidCol, 0)) > 0")
            ->latest()
            ->get();

        $impayesCount = $impayes->count();
        $totalImpayes = $impayes->sum(fn($s) => $s->total - ($s->paid_amount ?? 0));

        // ── Dépenses ─────────────────────────────────────────────────
        $depenses      = Expense::with('category')->latest()->get();
        $depensesMonth = Expense::whereMonth('expense_date', now()->month)
                                ->whereYear('expense_date', now()->year)
                                ->sum('amount');
        $depensesYear  = Expense::whereYear('expense_date', now()->year)->sum('amount');

        // ── Ventes (lecture seule) ────────────────────────────────────
        $allSales = Sale::with('client')->latest()->paginate(20);

        // ── KPIs ─────────────────────────────────────────────────────
        $caMonth = Sale::whereMonth('created_at', now()->month)
                       ->whereYear('created_at', now()->year)
                       ->sum('total'); // Corrigé : total_amount -> total
        
        $totalEncaisse = Payment::exists() ? Payment::sum('amount') : 0;

        // Statuts paiements (Basés sur la colonne 'total' de ton SQL)
        $ventesPayees     = Sale::whereRaw("COALESCE($paidCol, 0) >= total AND total > 0")->count();
        $ventesImPayees   = Sale::whereRaw("COALESCE($paidCol, 0) = 0")->count();
        $ventesPartielles = Sale::whereRaw("COALESCE($paidCol, 0) > 0 AND COALESCE($paidCol, 0) < total")->count();

        // Derniers paiements (dashboard)
        $recentPayments = Payment::with(['client'])->latest()->limit(8)->get();

        // ── Graphique 6 mois ─────────────────────────────────────────
        $chartLabels   = [];
        $chartCA       = [];
        $chartEncaisse = [];
        $chartDepenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[]   = $month->translatedFormat('M Y');
            
            $chartCA[]       = Sale::whereMonth('created_at', $month->month)
                                    ->whereYear('created_at', $month->year)
                                    ->sum('total'); // Corrigé ici aussi
            
            $chartEncaisse[] = Payment::whereMonth('created_at', $month->month)
                                        ->whereYear('created_at', $month->year)
                                        ->sum('amount');
            
            $chartDepenses[] = Expense::whereMonth('expense_date', $month->month)
                                        ->whereYear('expense_date', $month->year)
                                        ->sum('amount');
        }

        $expenseCategories = ExpenseCategory::orderBy('name')->get();

        return view('dashboards.accounting', compact(
            'payments', 'impayes', 'impayesCount', 'totalImpayes',
            'depenses', 'depensesMonth', 'depensesYear', 'allSales',
            'caMonth', 'totalEncaisse', 'ventesPayees', 'ventesImPayees',
            'ventesPartielles', 'recentPayments', 'chartLabels', 'chartCA',
            'chartEncaisse', 'chartDepenses', 'expenseCategories'
        ));
    }
}