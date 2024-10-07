<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
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
     * Handle post-authentication redirection based on user role.
     *
     * @return \Illuminate\Http\RedirectResponse
     ** @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        if (Auth::user()->role_as == '1') { // 1 ADMIN
            return redirect('admin/dashboard')->with('status', 'Welcome to One JAF Admin');
        } elseif (Auth::user()->role_as == '2') { // 2 HR 
            return redirect('hr/dashboard')->with('status', 'Welcome to One JAF HR');
        } elseif (Auth::user()->role_as == '3') { // 3 Employee
            return redirect('emp/dashboard')->with('status', 'Welcome To One JAF');
        } elseif (Auth::user()->role_as == '4') { // 4 Operations Manager
            return redirect('manager/dashboard')->with('status', 'Welcome To One JAF');
        } elseif (Auth::user()->role_as == '5') { // 5 IT Manager
            return redirect('manager/dashboard')->with('status', 'Welcome To One JAF');
        } elseif (Auth::user()->role_as == '6') { // 6 Marketing Manager
            return redirect('manager/dashboard')->with('status', 'Welcome To One JAF');
            
        } else { // Redirect all other roles to /
            return redirect('/')->with('status', 'Please coordinate with our HR department to access your account. Thank you.');
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
