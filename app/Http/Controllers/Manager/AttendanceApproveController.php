<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceApproveController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $cutOff = $request->input('cut_off');
        
        // Get the IDs of the subordinates of the current supervisor
        $subordinateIds = User::where('reporting_to', $user->id)->pluck('id');
    
        // Initialize the query
        $data = ApprovedAttendance::whereIn('users_id', $subordinateIds);
        
        // Apply status filter
        if (empty($status)) {
            $status = 'Pending';
            $data->where('status', $status);
        } else {
            $data->where('status', 'like', "%$status%");
        }
        
        // Determine counts based on user role
        if ($user->isAdmin() || $user->isHR()) {
            // Admins and HR can see all records
            $pendingCount = ApprovedAttendance::where('status', 'Pending')->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')->count();
        } else {
            // Counts based on subordinates only
            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
        }
        
        // Apply additional search filters
        if (!empty($name)) {
            $data->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            });
        }
    
        if (!empty($cutOff)) {
            $data->where('cut_off', 'like', "%$cutOff%");
        }
    
        // Apply date range filter on start_date
        if (!empty($startDate) && !empty($endDate)) {
            $data->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            });
        }
        
        // Get the filtered attendance records
        $attendance = $data->get();
        
        return view('manager.timesheet.timesheet', compact('attendance', 'pendingCount', 'user', 'approvedCount', 'declinedCount'));
    }
    
    public function viewTimesheet(Request $request, $id)
    {
        $att = ApprovedAttendance::findOrFail($id);
    
        $employeeName = $request->input('employee_name');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $cutOff = $request->input('cut_off');
    
        $query = EmployeeAttendance::where('users_id', $att->users_id);
    
        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
    
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $attendanceRecords = $query->get();

        $totalLateSeconds = 0;
        $totalSeconds = 0;
    
        foreach ($attendanceRecords as $attendance) {
            // Calculate total work time
            $timeTotal = explode(':', $attendance->timeTotal);
            if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
                $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                $totalSeconds += $seconds;
            }
        
            // Calculate total late time
            $totalLate = explode(':', $attendance->totalLate);
            if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
                $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                $totalLateSeconds += $seconds;
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
        
        return view('manager.timesheet.viewtimesheet', [
            'att' => $att,
            'attendanceRecords' => $attendanceRecords,
            'totalLate' => $totalLate,
            'total' => $totalTime,
        ]);
    }
    
    
    public function approve($id)
    {
        $att = ApprovedAttendance::findOrFail($id);
        $user = $att->user;
        $currentUser = auth()->user();

        $user->save();

        $att->status = 'Approved';
        $att->approved_by = $currentUser->id;
        $att->save();

        Alert::success('Attendance Approved');
        return redirect()->back();
    }

    public function decline($id)
    {
        $att = ApprovedAttendance::findOrFail($id);
        $user = $att->user;
    
        $att->status = 'Declined';
        $att->save();
    
        Alert::success('Attendance Declined');
        return redirect()->back();
    }

    public function updateAttendance(Request $request, $id)

    {
        $approved = ApprovedAttendance::findOrFail($id);

        $approved->start_date = $request->input('start_date');
        $approved->end_date = $request->input('end_date');
        $approved->totalHours = $request->input('totalHours');
        $approved->totalLate = $request->input('totalLate');
        $approved->save();

        Alert::success('Attendance Updated');
        return redirect()->back();
    }
    
    public function destroyAttendance($id)
    {
        $appr = ApprovedAttendance::findOrFail($id);

        $appr->delete();

        Alert::success('Attendance deleted successfully');
        return redirect()->back();
    }

    // Attendance Record & Edit


    public function attendanceRecord(Request $request)
    {
        $user = auth()->user();
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
        
        // Get the IDs of the subordinates of the current supervisor
        $subordinateIds = User::where('reporting_to', $user->id)->pluck('id');
    
        // Query to fetch users (subordinates) with their attendance records filtered by month, year, and optionally department
        $usersQuery = User::whereIn('id', $subordinateIds) // Only include subordinates of the supervisor
            ->with(['employeeAttendance' => function ($query) use ($month, $year, $department) {
                $query->whereMonth('date', $month)
                      ->whereYear('date', $year);
                if ($department) {
                    $query->whereHas('user', function ($query) use ($department) {
                        $query->where('department', $department);
                    });
                }
            }])
            ->when($employeeName, function ($query) use ($employeeName) {
                $query->where(function ($subQuery) use ($employeeName) {
                    $subQuery->where('fName', 'like', '%' . $employeeName . '%')
                             ->orWhere('lName', 'like', '%' . $employeeName . '%');
                });
            });
    
        // Execute the query to fetch the users with their attendance records
        $users = $usersQuery->get();
    
        return view('manager.attendance.attendancerecord', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
        ]);
    }
    
    // EDIT Attendance

    public function empreport(Request $request)
    {
        $user = auth()->user();
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
        $date = $request->get('date'); // Keep single date search
        $startDate = $request->get('start_date'); 
        $endDate = $request->get('end_date'); 

        $subordinateIds = User::where('reporting_to', $user->id)->pluck('id');
    
        // Query users with their attendance records filtered by date range, specific date, and optionally department
        $usersQuery = User::whereIn('id', $subordinateIds)
            ->with(['employeeAttendance' => function ($query) use ($startDate, $endDate, $date, $month, $year, $department) {
                // Filter by date range if both start_date and end_date are provided
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                } elseif ($date) {
                    // If a specific date is selected, filter attendance by that exact date
                    $query->whereDate('date', $date);
                } else {
                    // Otherwise, filter by the selected month and year
                    $query->whereMonth('date', $month)->whereYear('date', $year);
                }
    
                if ($department) {
                    $query->whereHas('user', function ($query) use ($department) {
                        $query->where('department', $department);
                    });
                }
            }])
            ->when($employeeName, function ($query) use ($employeeName) {
                // Split the input into parts
                $names = explode(' ', $employeeName);
            
                // If the input contains more than one part, assume the last part is the last name
                if (count($names) > 1) {
                    $lName = array_pop($names); // Last part as the last name
                    $fName = implode(' ', $names); // Combine the remaining parts as the first name
            
                    // Check for exact match of combined fName and lName
                    $query->where(function ($query) use ($fName, $lName) {
                        $query->where('fName', 'like', '%' . $fName . '%')
                              ->where('lName', 'like', '%' . $lName . '%');
                    });
                } else {
                    // If only one part is provided, search in both fields
                    $query->where(function ($query) use ($employeeName) {
                        $query->where('fName', 'like', '%' . $employeeName . '%')
                              ->orWhere('lName', 'like', '%' . $employeeName . '%');
                    });
                }
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
    
        return view('manager.attendance.tableview', [
            'filteredData' => $users,
            'month' => $month,
            'year' => $year,
            'departments' => $departments,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
            'totalLate' => $totalLate,
            'total' => $totalTime,
            'selectedDate' => $date, // Pass the selected date to the view
            'selectedStartDate' => $startDate, // Pass the selected start date to the view
            'selectedEndDate' => $endDate, // Pass the selected end date to the view
        ]);
    }

    public function updateTableAttendance(Request $request, $id)
    {
        $attupdate = EmployeeAttendance::findOrFail($id);

        $attupdate->timeIn = $request->input('timeIn');
        $attupdate->breakIn = $request->input('breakIn');
        $attupdate->breakOut = $request->input('breakOut');
        $attupdate->timeOut = $request->input('timeOut');
        $attupdate->totalLate = $request->input('totalLate');
        $attupdate->timeTotal = $request->input('totalHours');
        $attupdate->status = 'Edited';
        $attupdate->save();

        Alert::success('Attendance Updated');
        return redirect()->back();
    }

    public function destroyTableAttendance($id)
    {
        $appr = EmployeeAttendance::findOrFail($id);

        $appr->delete();

        Alert::success('Attendance deleted successfully');
        return redirect()->back();
    }
    
}
