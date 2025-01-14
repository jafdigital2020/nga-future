<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceEditHistory;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\AttendanceApprovedNotification;

class AttendanceReportController extends Controller
{

    public function index(Request $request)
    {
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
        $date = $request->get('date'); // Keep single date search
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query users with their attendance records filtered by date range, specific date, and optionally department
        $usersQuery = User::query()
            ->where('status', 'active')
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
                $query->with('edited');
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

        $users = $usersQuery->orderBy('fName')->orderBy('lName')->get();

        // Fetch distinct departments for the dropdown filter
        $departments = User::select('department')->distinct()->get();

        return view('admin.attendance.index', [
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
        $users = EmployeeAttendance::orderBy('fName', 'ASC')
                                    ->orderBy('lName', 'ASC')
                                    ->get();
        return view('admin.attendance.table', compact('users'));
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

    private function getMonthNumber($monthName)
    {
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return array_search($monthName, $monthNames) + 1;
    }

    // TIMESHEET APPROVAL

    public function timesheet(Request $request)
    {
        $user = auth()->user();
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $cutOff = $request->input('cut_off');

        // Initialize the query
        $data = ApprovedAttendance::query();

        // If no status is selected, default to showing pending requests
        if (empty($status)) {
            $data->where('status', 'Pending');
        }

        if ($user->isAdmin() || $user->isHR()) {

            $pendingCount = ApprovedAttendance::where('status', 'Pending')->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')->count();
        } elseif ($user->isSupervisor()) {
            // Supervisors can only see leave requests from their department but not their own requests
            $data->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department)
                      ->where('role_as', '!=', User::ROLE_HR) // Not HR
                      ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                      ->where('id', '!=', $user->id); // Not their own requests
            });

            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();

            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();

            $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();
        } else {

            $data->where('users_id', $user->id);

            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->where('users_id', $user->id)
                                ->count();

            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->where('users_id', $user->id)
                                ->count();
           $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->where('users_id', $user->id)
                                ->count();
        }

        // Apply search filters
        if (!empty($searchInput)) {
            $names = explode(' ', $searchInput); // Split input by space
            $fName = $names[0] ?? '';
            $lName = $names[1] ?? '';

            $data->whereHas('user', function ($query) use ($fName, $lName) {
                $query->where(function ($subQuery) use ($fName, $lName) {
                    if (!empty($fName)) {
                        $subQuery->where('name', 'like', "%$fName%");
                    }
                    if (!empty($lName)) {
                        $subQuery->where('name', 'like', "%$lName%");
                    }
                });
            });
        }

        if (!empty($cutOff)) {
            $data->where('cut_off', 'like', "%$cutOff%");
        }

        if (!empty($status)) {
            $data->where('status', 'like', "%$status%");
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

        $attendance = $data->get();

        return view('admin.timesheet.timesheet', compact('attendance', 'pendingCount', 'user', 'approvedCount', 'declinedCount'));
    }

    public function approve($id)
    {
        $att = ApprovedAttendance::findOrFail($id);

        // Log the attendance data
        Log::info('Attendance Data', [
            'cutoff' => $att->cut_off,
            'start_date' => $att->start_date,
            'end_date' => $att->end_date,
        ]);

        $user = $att->user;
        $att->status = 'Approved';
        $att->approved_by = auth()->user()->id;
        $att->save();

        // Notify the user
        $user->notify(new AttendanceApprovedNotification($att));

        return redirect()->back()->with('success', 'Timesheet Approved');
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

        return view('admin.timesheet.viewtimesheet', [
            'att' => $att,
            'attendanceRecords' => $attendanceRecords,
            'totalLate' => $totalLate,
            'total' => $totalTime,
        ]);
    }


    // ATTENDANCETABLE EDIT/UPDATE

    public function empreport(Request $request)
    {
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
        $date = $request->get('date', date('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query users with their attendance records filtered by date range, specific date, and optionally department
        $usersQuery = User::query()
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
                $query->with('edited');
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


        // Fetch distinct departments for the dropdown filter
        $departments = User::select('department')->distinct()->get();

        return view('admin.attendance.attendancetable', [
            'filteredData' => $users,
            'month' => $month,
            'year' => $year,
            'departments' => $departments,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,

            'selectedDate' => $date,
            'selectedStartDate' => $startDate,
            'selectedEndDate' => $endDate,
        ]);
    }

    public function updateTableAttendance(Request $request, $id)
    {
        $attupdate = EmployeeAttendance::findOrFail($id);

        // Log changes
        $original = $attupdate->toArray();
        $changes = array_diff_assoc($request->only([
            'timeIn', 'breakIn', 'breakOut', 'timeOut', 'totalLate', 'totalHours'
        ]), $original);

        if (!empty($changes)) {
            AttendanceEditHistory::create([
                'attendance_id' => $id,
                'changes' => json_encode($changes),
                'edited_by' => Auth::id(),
            ]);
        }

        // Update the record
        $attupdate->timeIn = $request->input('timeIn');
        $attupdate->breakIn = $request->input('breakIn');
        $attupdate->breakOut = $request->input('breakOut');
        $attupdate->timeOut = $request->input('timeOut');
        $attupdate->totalLate = $request->input('totalLate');
        $attupdate->timeTotal = $request->input('totalHours');
        $attupdate->status = 'Edited';
        $attupdate->edited_by = Auth::id();
        $attupdate->save();

        Alert::success('Attendance Updated');
        return redirect()->back();
    }

    public function getEditHistory($id)
    {
        $history = AttendanceEditHistory::where('attendance_id', $id)
            ->with('editor') // Assuming 'editor' is the relationship to User
            ->get()
            ->map(function ($item) {
                return [
                    'field' => implode(', ', array_keys(json_decode($item->changes, true))),
                    'old_value' => implode(', ', array_values(json_decode($item->changes, true))),
                    'new_value' => $item->created_at->diffForHumans(),
                    'edited_by' => $item->editor->name,
                    'date' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json(['history' => $history]);
    }


    public function destroyTableAttendance($id)
    {
        $appr = EmployeeAttendance::findOrFail($id);

        $appr->delete();

        Alert::success('Attendance deleted successfully');
        return redirect()->back();
    }
}
