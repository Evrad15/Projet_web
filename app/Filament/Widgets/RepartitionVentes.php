<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RepartitionVentes extends ChartWidget
{
    protected static ?int $sort = 2; // S'affichera après le widget n°1
    protected ?string $heading = 'Repartition Ventes';

    protected function getData(): array
    {
        $data = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total'))
            ->groupBy('products.id', 'products.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Ventes totales',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => $data->map(fn($row) => '#' . substr(md5($row->name), 0, 6))->toArray(),
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Assure-toi que c'est bien écrit ici
    }
}
