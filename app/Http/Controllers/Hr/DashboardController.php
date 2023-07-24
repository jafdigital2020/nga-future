<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index ()
    {
        $user = Auth::user();
        return view('hr.dashboard', compact('user'));
    }

}
