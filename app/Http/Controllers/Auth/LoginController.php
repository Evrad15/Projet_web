<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request; // obligatoire
use Illuminate\Support\Facades\Auth; // parfois utile
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        $routes = [
            'sales_manager'    => 'dashboard.sales',
            'accountant'       => 'dashboard.accounting',
            'stock_manager'    => 'dashboard.stock',
            'supplier_manager' => 'dashboard.supplier',
            'sales_employee'   => 'dashboard.sales_employee',
            'client'           => 'dashboard.client',
        ];

        $routeName = $routes[$user->role] ?? null;

        if ($routeName && Route::has($routeName)) {
            return redirect()->route($routeName);
        }

        return redirect('/login');
    }
}
