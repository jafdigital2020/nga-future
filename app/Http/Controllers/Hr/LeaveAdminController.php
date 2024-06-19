<?php

namespace App\Http\Controllers\Hr;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveAdminController extends Controller
{
    public function index ()
    {
        $emp = User::all();
        return view ('hr.leave.index', compact('emp'));
    }
}
