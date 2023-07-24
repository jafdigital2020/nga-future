<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    public function index()
    {
        $authUserId = auth()->user()->id;
        $userslip = DB::table('employee_salaries')->where('users_id', $authUserId)->get();
        return view('emp.payslip.index', compact('userslip'));
    }

    public function view($payslip_id)
    {
        $viewsalary = EmployeeSalary::with('user')->find($payslip_id);
        return view('emp.payslip.view', compact('viewsalary'));
    }
}
