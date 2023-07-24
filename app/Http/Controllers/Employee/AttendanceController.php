<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Admin\EmployeeController;

class AttendanceController extends Controller
{
    public function index ()
    {
        $EmployeeAttendance = EmployeeAttendance::latest()->first();
        $att = Auth::user()->id;
        $total = EmployeeAttendance::sum('timeTotal');
        $all = DB::table('attendance')->get();
        $empatt = DB::table('attendance')->where('users_id', auth()->user()->id)->get();
        $latest = EmployeeAttendance::where('users_id', Auth::user()->id)->latest()->first();
        // $att_id = EmployeeAttendance::latest()->first()->id;
        return view('emp.attendance.index', compact('att', 'empatt', 'all', 'total', 'latest'));
    }

    public function store(Request $request)
    {
        $request->user()->checkIn();

        return redirect('/emp/attendance');
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/emp/attendance');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();

        return redirect('/emp/attendance');
    }


    public function update(Request $request)
    {
        $request->user()->checkOut();

        return redirect('/emp/attendance');
    }

    // public function total(Request $request, $id)
    // {
    //     $attendance = EmployeeAttendance::find($id);
    //     $attendance->timeTotal = DB::table('attendance')->where('id')->sum(DB::raw('timeIn - timeOut'));
    //     $attendance->save();

    //     return back();
    // }

    public function countdown()
    {
        $countdownDuration = 3600; // 1 hour in seconds
        return view('emp.attendance.index', compact('countdownDuration'));
    }

    // public function report(Request $request)
    // {
    //     $authUserId = auth()->user()->id;
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     $data = EmployeeAttendance::where('users_id', $authUserId);

    //     if ($startDate && $endDate) {
    //         $data->whereBetween('date', [$startDate, $endDate]);
    //     } elseif ($request->input('filter') == 'last_30_days') {
    //         $data->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
    //     } elseif ($request->input('filter') == 'last_15_days') {
    //         $data->where('date', '>=', Carbon::now()->subDays(15)->toDateString());
    //     } elseif ($request->input('filter') == 'last_year') {
    //         $data->where('date', '>=', Carbon::now()->subYear()->toDateString());
    //     }

    //     $filteredData = $data->get();
    //     $totalSeconds = 0;
    //     $totalLateSeconds =0;

    //     foreach ($filteredData as $row) {
    //         $timeTotal = strtotime($row->timeTotal);
    //         $totalSeconds += $timeTotal;
    //     }

    //     foreach ($filteredData as $row) {
    //         $totalLate = strtotime($row->totalLate);
    //         $totalLateSeconds += $totalLate;
    //     }

    //     $totalTime = gmdate("H:i:s", $totalSeconds);
    //     $totalLate = gmdate("H:i:s", $totalLateSeconds);

    //     return view('emp.attendance.report', compact('filteredData', 'totalTime', 'totalLate'));
    // }

    public function report(Request $request)
    {
        $authUserId = auth()->user()->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $data = EmployeeAttendance::where('users_id', $authUserId);

        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->input('filter') == 'last_30_days') {
            $data->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
        } elseif ($request->input('filter') == 'last_15_days') {
            $data->where('date', '>=', Carbon::now()->subDays(15)->toDateString());
        } elseif ($request->input('filter') == 'last_year') {
            $data->where('date', '>=', Carbon::now()->subYear()->toDateString());
        }

        $filteredData = $data->get();
        $totalSeconds = 0;
        $totalLateSeconds = 0;

        foreach ($filteredData as $row) {
            $timeTotal = strtotime($row->timeTotal);
            $totalSeconds += $timeTotal;
        }

        foreach ($filteredData as $row) {
            $totalLate = strtotime($row->totalLate);
            $totalLateSeconds += $totalLate;
        }

        $totalTime = gmdate("H:i:s", $totalSeconds);
        $totalLate = gmdate("H:i:s", $totalLateSeconds);

        return view('emp.attendance.report', compact('filteredData', 'totalTime', 'totalLate'));
    }
}
