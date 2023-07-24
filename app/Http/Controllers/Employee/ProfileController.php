<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index ()
    {
        $user = Auth::user();
        return view('emp.empprofile.index', compact('user'));
    }
}
