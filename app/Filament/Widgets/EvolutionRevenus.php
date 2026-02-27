<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EvolutionRevenus extends ChartWidget
{
    protected ?string $heading = 'Revenus Annuels (Cumul Mensuel)';
    protected static ?int $sort = 2;
    // On met 'half' pour qu'il soit à côté de l'autre graphique
    protected int | string | array $columnSpan = 'half'; 

    protected function getData(): array
    {
        // 1. On récupère le total par mois pour l'année en cours
        $ventesParMois = Sale::select(
            DB::raw('SUM(total) as aggregate'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('aggregate', 'month')
        ->toArray();

        // 2. On s'assure d'avoir les 12 mois, même ceux à 0
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = $ventesParMois[$m] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenu (FCFA)',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 5,
                ],
            ],
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
