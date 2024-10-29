<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function attendance()
    {
        return view('admin.request.attendance');
    }
}
