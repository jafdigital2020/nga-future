<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index ()
    {
        return view('hr.payslip.index');
    }
}
