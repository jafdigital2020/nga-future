<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $authUserId = $user->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $dataQuery = EmployeeAttendance::where('users_id', $authUserId);

        if ($startDate && $endDate) {
            $dataQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $filteredData = $dataQuery->get();

        $department = $user->department;
        $supervisor = User::getSupervisorForDepartment($department, $user);

        if ($request->ajax()) {
            return response()->json($filteredData);
        }

        $total = EmployeeAttendance::sum('timeTotal');
        $all = DB::table('attendance')->get();
        $empatt = DB::table('attendance')->where('users_id', $authUserId)->get();
        $latest = EmployeeAttendance::where('users_id', $authUserId)->latest()->first();

        return view('manager.dashboard', compact('user', 'empatt', 'all', 'total', 'latest', 'filteredData', 'supervisor'));
    }


    public function getUserAttendance()
    {
        $authUserId = Auth::id();
        $attendanceData = EmployeeAttendance::where('users_id', $authUserId)
            ->select('date', 'timeIn', 'timeOut', 'timeTotal', 'totalLate')
            ->get();
        
        return response()->json($attendanceData);

    }

    public function store(Request $request)
    {
        $request->user()->checkIn();

        return redirect('/manager/dashboard');
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/manager/dashboard');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();

        return redirect('/manager/dashboard');
    }


    public function update(Request $request)
    {
        $request->user()->checkOut();

        return redirect('/manager/dashboard');
    }
}
