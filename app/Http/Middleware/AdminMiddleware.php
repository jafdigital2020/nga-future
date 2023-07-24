<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     if(Auth::check())
    //     {
    //         if(Auth::user()->role_as == '1') // Admin
    //         {
    //             return $next($request);
    //         }
    //         else if(Auth::user()->role_as == '2') // HR
    //         {
    //             return $next($request);
    //         }
    //         else if(Auth::user()->role_as == '3') // Employee
    //         {
    //             return $next($request);
    //         }
    //         else
    //         {
    //             return redirect('/home')->with('status', 'Welcome To One JAF');
    //         }
    //     }
    //     else
    //     {
    //         return redirect('/login')->with('status', 'Please Login');
    //     }  
    // }

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role_as == User::ROLE_ADMIN) {
            return $next($request);
        } else {
            return redirect('/login')->with('status', 'Welcome To One JAF');
        }
    }
}
