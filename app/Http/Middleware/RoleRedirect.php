<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            return match ($role) {
                'sales_manager'           => redirect()->route('dashboard.sales'),
                'accountant'              => redirect()->route('dashboard.accounting'),
                'stock_manager'           => redirect()->route('dashboard.stock'),
                'supplier_manager'        => redirect()->route('dashboard.supplier'),
                'sales_employee'          => redirect()->route('dashboard.sales_employee'),
                'admin'                   => redirect()->route('dashboards.admin'),
                default                  => redirect()->route('home'),
            };
        }

        return $next($request);
    }
}
