<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function export(Request $request, $type)
    {
        $startDate = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $endDate = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();
        $format = $request->input('format', 'pdf');

        if ($type === 'financial') {
            // Récupération des données
            $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
            $totalPayments = Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
            $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

            // Calcul du résultat (Basé sur les encaissements réels ou le CA facturé selon votre préférence, ici CA - Dépenses)
            $netResult = $totalSales - $totalExpenses;

            $data = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalSales' => $totalSales,
                'totalPayments' => $totalPayments,
                'totalExpenses' => $totalExpenses,
                'netResult' => $netResult,
                'expenses' => Expense::with('category')->whereBetween('expense_date', [$startDate, $endDate])->get(),
                'payments' => Payment::with('sale.client')->whereBetween('created_at', [$startDate, $endDate])->latest()->get(),
            ];

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('financial', $data);
                return $pdf->download('rapport_financier_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf');
            } else {
                return $this->exportCsv($data);
            }
        }

        return back()->with('error', 'Type de rapport non supporté.');
    }

    private function exportCsv($data)
    {
        $filename = "rapport_financier.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // En-tête
            fputcsv($file, ['RAPPORT FINANCIER', $data['startDate']->format('d/m/Y') . ' au ' . $data['endDate']->format('d/m/Y')]);
            fputcsv($file, []);

            // Résumé
            fputcsv($file, ['RESUME GLOBAL']);
            fputcsv($file, ['Chiffre d\'Affaires (Ventes)', $data['totalSales']]);
            fputcsv($file, ['Total Encaisse (Paiements)', $data['totalPayments']]);
            fputcsv($file, ['Total Depenses', $data['totalExpenses']]);
            fputcsv($file, ['Resultat Net (CA - Depenses)', $data['netResult']]);
            fputcsv($file, []);

            // Détails Dépenses
            fputcsv($file, ['DETAILS DEPENSES']);
            fputcsv($file, ['Date', 'Libelle', 'Categorie', 'Montant']);
            foreach ($data['expenses'] as $exp) {
                fputcsv($file, [$exp->expense_date, $exp->title, $exp->category->name ?? 'N/A', $exp->amount]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
