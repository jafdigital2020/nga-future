<?php

namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HRAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
    
        // Query users with their attendance records filtered by month, year, and optionally department
        $usersQuery = User::query()
            ->with(['employeeAttendance' => function ($query) use ($month, $year, $department) {
                $query->whereMonth('date', $month)->whereYear('date', $year);
                if ($department) {
                    $query->whereHas('user', function ($query) use ($department) {
                        $query->where('department', $department);
                    });
                }
            }])
            ->when($employeeName, function ($query) use ($employeeName) {
                $query->where('name', 'like', '%' . $employeeName . '%');
            });
    
        // Apply department filter directly on the users query
        if ($department) {
            $usersQuery->where('department', $department);
        }
    
        $users = $usersQuery->get();
    
        // Fetch distinct departments for the dropdown filter
        $departments = User::select('department')->distinct()->get();
    
        return view('hr.attendance.index', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'departments' => $departments,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
        ]);
    }

    public function tableview()
    {
        $users = EmployeeAttendance::all();
        return view('hr.attendance.table', compact('users'));
    }

    public function updateTable(Request $request)
    {
        if($request->ajax()) {
            if($request->action == 'edit') {
                $data = array(
                    'timeIn'    => $request->timeIn,
                    'breakIn'   => $request->breakIn,
                    'breakOut'  => $request->breakOut,
                    'timeOut'   => $request->timeOut
                );

                // Retrieve current data
                $currentData = DB::table('attendance')->where('id', $request->id)->first();

                // Compute timeTotal
                $currentTotalSeconds = strtotime($currentData->timeTotal);
                $newTimeTotalSeconds = (strtotime($data['timeOut']) - strtotime($data['timeIn'])) - (strtotime($data['breakOut']) - strtotime($data['breakIn']));
                $timeTotalDiffSeconds = $newTimeTotalSeconds - $currentTotalSeconds;

                // Update timeTotal
                $data['timeTotal'] = gmdate("H:i:s", strtotime($currentData->timeTotal) + $timeTotalDiffSeconds);

                // Calculate totalLate difference
                $referenceTime = strtotime('10:00:00 AM');
                $newTimeIn = strtotime($data['timeIn']);
                $totalLateDiffSeconds = max(0, $newTimeIn - $referenceTime);

                // Update totalLate
                $data['totalLate'] = gmdate("H:i:s", $totalLateDiffSeconds);

                // Update database
                DB::table('attendance')
                    ->where('id', $request->id)
                    ->update($data);
            }

            if($request->action == 'delete') {
                DB::table('attendance')
                    ->where('id', $request->id)
                    ->delete();
            }

            return response()->json($request);
        }
    }

    public function empreport(Request $request)
    {
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
    
        // Query users with their attendance records filtered by month, year, and optionally department
        $usersQuery = User::query()
            ->with(['employeeAttendance' => function ($query) use ($month, $year, $department) {
                $query->whereMonth('date', $month)->whereYear('date', $year);
                if ($department) {
                    $query->whereHas('user', function ($query) use ($department) {
                        $query->where('department', $department);
                    });
                }
            }])
            ->when($employeeName, function ($query) use ($employeeName) {
                $query->where('name', 'like', '%' . $employeeName . '%');
            });
    
        // Apply department filter directly on the users query
        if ($department) {
            $usersQuery->where('department', $department);
        }
    
        $users = $usersQuery->get();
    
        // Calculate totals for filtered data
        $totalLateSeconds = 0;
        $totalSeconds = 0;
    
        foreach ($users as $user) {
            foreach ($user->employeeAttendance as $attendance) {
                $timeTotal = explode(':', $attendance->timeTotal);
                if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
                    $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                    $totalSeconds += $seconds;
                }
    
                $totalLate = explode(':', $attendance->totalLate);
                if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
                    $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                    $totalLateSeconds += $seconds;
                }
            }
        }
    
        // Convert total seconds to hours:minutes:seconds format
        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalSeconds = $totalSeconds % 60;
        $totalTime = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);
    
        $totalLateHours = floor($totalLateSeconds / 3600);
        $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
        $totalLateSeconds = $totalLateSeconds % 60;
        $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $totalLateSeconds);
    
        // Fetch distinct departments for the dropdown filter
        $departments = User::select('department')->distinct()->get();
    
        return view('hr.attendance.attendancetable', [
            'filteredData' => $users, // Assuming your Blade template expects 'filteredData'
            'month' => $month,
            'year' => $year,
            'departments' => $departments,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
            'totalLate' => $totalLate,
            'total' => $totalTime, // Assuming 'total' represents total time worked
        ]);
    }
    
    private function getMonthNumber($monthName)
    {
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return array_search($monthName, $monthNames) + 1;
    }
}
