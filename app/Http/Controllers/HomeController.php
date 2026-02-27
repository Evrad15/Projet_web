<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = auth()->user()->role;

        switch ($role) {
            case 'sales_manager':
                return redirect()->route('dashboard.sales');
            case 'sales_employee':
                return redirect()->route('dashboard.sales_employee');
            case 'stock_manager':
                return redirect()->route('dashboard.stock');
            case 'accountant':
                return redirect()->route('dashboard.accounting');
            case 'supplier_manager':
                return redirect()->route('supplier.orders');
            case 'cashier':
                return redirect()->route('dashboard');
            default:
                return view('home');
        }
    }
}
