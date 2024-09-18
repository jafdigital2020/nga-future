<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function estimate()
    {
        return view('admin.sales.estimate');
    }

    public function invoice()
    {
        return view('admin.sales.invoice');
    }

    public function payment()
    {
        return view('admin.sales.payment');
    }

    public function expense()
    {
        return view('admin.sales.expense');
    }

    public function tax()
    {
        return view('admin.sales.tax');
    }
}
