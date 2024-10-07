<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function shiftDaily()
    {
        return view('admin.shiftschedule.daily');
    }

    public function shiftList()
    {
      return view('admin.shiftschedule.list');
    }
}
