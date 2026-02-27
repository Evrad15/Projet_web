<?php

namespace App\Filament\Widgets;

use App\Models\Sale;   // Ton modèle Sale (Ventes)
use App\Models\Client; // Ton modèle Client
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Le plus petit chiffre = Priorité n°1
    protected function getStats(): array
    {
        return [
            // REVENU TOTAL
            Stat::make('Revenue Total', number_format(Sale::sum('total'), 0, ',', ' ') . ' FCFA')
                ->description('Toutes les ventes cumulées')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 18]), // Assure-toi d'avoir des chiffres ici



            // NOMBRE DE VENTES
            Stat::make('Total des ventes', Sale::count())
                ->description('Nombre de transactions effectuées')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            // NOMBRE DE CLIENTS
            Stat::make('Clients Total', Client::count())
                ->description('Clients enregistrés en base')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
