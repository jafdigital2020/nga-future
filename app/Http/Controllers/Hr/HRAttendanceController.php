<?php

namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HRAttendanceController extends Controller
{
    public function index (Request $request)
    {
        $EmployeeAttendance = EmployeeAttendance::latest()->first();
        $att = Auth::user()->id;
        $total = EmployeeAttendance::sum('timeTotal');
        $all = DB::table('attendance')->get();
        $empatt = DB::table('attendance')->where('users_id', auth()->user()->id)->get();
        $latest = EmployeeAttendance::where('users_id', Auth::user()->id)->latest()->first();
        $data = EmployeeAttendance::where('users_id', Auth::user()->id)->get();

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
            $timeTotal = explode(':', $row->timeTotal);
            if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
                $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                $totalSeconds += $seconds;
            }
        }

        foreach ($filteredData as $row) {
            $totalLate = explode(':', $row->totalLate);
            if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
                $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                $totalLateSeconds += $seconds;
            }
        }
        
        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalSeconds = $totalSeconds % 60;
        
        $totalTime = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);
        
        $totalLateHours = floor($totalLateSeconds / 3600);
        $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
        $totalLateSeconds = $totalLateSeconds % 60;
        
        $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $totalLateSeconds);
        return view('hr.attendance.index', compact('att', 'empatt', 'all', 'total', 'latest', 'data', 'filteredData', 'totalTime', 'totalLate'));
    }

    public function store(Request $request)
    {
        $request->user()->checkIn();

        return redirect('/hr/attendance');
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/hr/attendance');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();


        return redirect('/hr/attendance');
    }

    public function update(Request $request)
    {
        $request->user()->checkOut();

        return redirect('/hr/attendance');
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
    //     $totalLateSeconds = 0;

    //     foreach ($filteredData as $row) {
    //         $timeTotal = explode(':', $row->timeTotal);
    //         if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
    //             $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
    //             $totalSeconds += $seconds;
    //         }
    //     }

    //     foreach ($filteredData as $row) {
    //         $totalLate = explode(':', $row->totalLate);
    //         if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
    //             $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
    //             $totalLateSeconds += $seconds;
    //         }
    //     }
        
    //     $totalHours = floor($totalSeconds / 3600);
    //     $totalMinutes = floor(($totalSeconds % 3600) / 60);
    //     $totalSeconds = $totalSeconds % 60;
        
    //     $totalTime = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);
        
    //     $totalLateHours = floor($totalLateSeconds / 3600);
    //     $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
    //     $totalLateSeconds = $totalLateSeconds % 60;
        
    //     $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $totalLateSeconds);
        

    //     return view('hr.attendance.index', compact('filteredData', 'totalTime', 'totalLate'));
    // }



    public function empreport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        $selectedMonth = $request->input('month');
    
        $data = DB::table('attendance');
    
        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->input('filter') == 'last_30_days') {
            $data->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
        } elseif ($request->input('filter') == 'last_15_days') {
            $data->where('date', '>=', Carbon::now()->subDays(15)->toDateString());
        } elseif ($request->input('filter') == 'last_year') {
            $data->where('date', '>=', Carbon::now()->subYear()->toDateString());
        }
    
        if ($selectedMonth && $selectedMonth != '-') {
            $year = date('Y');
            $monthNumber = $this->getMonthNumber($selectedMonth);
            $data->whereRaw('MONTH(date) = ?', [$monthNumber]);
            $data->whereRaw('YEAR(date) = ?', [$year]);
        }
    
        if ($userId) {
            $data->where('users_id', '=', $userId);
        }
    
        $filteredData = $data->get();

        $totalSeconds = 0;
        $totalLateSeconds = 0;

        foreach ($filteredData as $row) {
            $timeTotal = explode(':', $row->timeTotal);
            if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
                $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                $totalSeconds += $seconds;
            }
        }

        foreach ($filteredData as $row) {
            $totalLate = explode(':', $row->totalLate);
            if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
                $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                $totalLateSeconds += $seconds;
            }
        }
        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalSeconds = $totalSeconds % 60;

        $total = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

        $totalLateHours = floor($totalLateSeconds / 3600);
        $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
        $totalLateSeconds = $totalLateSeconds % 60;

        $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $totalLateSeconds);

        $users = User::all();
        $months = ['-', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return view('hr.attendance.empreport', compact('filteredData', 'total', 'users', 'totalLate', 'months'));
    }

    private function getMonthNumber($monthName)
    {
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return array_search($monthName, $monthNames) + 1;
    }
    
}
