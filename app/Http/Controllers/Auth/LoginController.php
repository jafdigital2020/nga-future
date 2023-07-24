<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
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
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function authenticated()
    {
        if (Auth::user()->role_as == '1') // 1 ADMIN
        {
            return redirect('admin/dashboard')->with('status', 'Welcome to One JAF Admin');
        }
        else if (Auth::user()->role_as == '2') // 2 HR 
        {
            return redirect('hr/dashboard')->with('status', 'Welcome to One JAF HR');
        }
        else if (Auth::user()->role_as == '3') // 3 Employee
        {
            return redirect('emp/dashboard')->with('status', 'Welcome To One JAF');
        }
        else // Redirect all other roles to /home with a custom message
        {
            return redirect('/home')->with('status', 'Please coordinate with our HR department to access your account. Thank you.');
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
