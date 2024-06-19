<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $maxSessionDuration = 120; // in minutes
        $lastActivityKey = 'last_activity_time';

        if ($request->session()->has($lastActivityKey)) {
            $lastActivityTime = $request->session()->get($lastActivityKey);
            $currentTime = time();

            if ($currentTime - $lastActivityTime > ($maxSessionDuration * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('message', 'You have been automatically logged out due to inactivity.');
            }
        }

        $request->session()->put($lastActivityKey, time());

        return $next($request);
    }
}
