<?php

namespace App\Http\Controllers\Hr;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\Models\AttendanceCredit;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        if ($request->ajax()) {
            if ($request->action == 'edit') {
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

            if ($request->action == 'delete') {
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
        $date = $request->get('date'); // Keep single date search
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

        return view('hr.attendance.attendancetable', [
            'filteredData' => $users, // Assuming your Blade template expects 'filteredData'
            'month' => $month,
            'year' => $year,
            'departments' => $departments,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
            'totalLate' => $totalLate,
            'total' => $totalTime, // Assuming 'total' represents total time worked
            'selectedDate' => $date, // Pass the selected date to the view
            'selectedStartDate' => $startDate, // Pass the selected start date to the view
            'selectedEndDate' => $endDate, // Pass the selected end date to the view
        ]);
    }

    private function getMonthNumber($monthName)
    {
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return array_search($monthName, $monthNames) + 1;
    }

    // Timesheet Approval

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

        // Apply search filters independently
        if (!empty($name)) {
            $data->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
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
            $data->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            });
        }

        $attendance = $data->get();

        return view('hr.timesheet.timesheet', compact('attendance', 'pendingCount', 'user', 'approvedCount', 'declinedCount'));
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

        return view('hr.timesheet.viewtimesheet', [
            'att' => $att,
            'attendanceRecords' => $attendanceRecords,
            'totalLate' => $totalLate,
            'total' => $totalTime,
        ]);
    }

    // Table Attendance 

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
        $attupdate->edited_by = Auth::user()->id;
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

    // Attendance Approval

    public function attendance()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $reqcount = EmployeeAttendance::whereIn('status_code', ['Pending', 'Approved'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $pendingCount = EmployeeAttendance::where('status_code', 'Pending')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $decCount = EmployeeAttendance::where('status_code', 'Declined')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $pending = EmployeeAttendance::whereIn('status_code', ['Pending', 'Approved', 'Declined'])->get();

        return view('hr.attendance.approval', compact('pending', 'pendingCount', 'reqcount', 'decCount'));
    }

    public function updateCertificateAttendance(Request $request, $id)
    {
        $request->validate([
            'datee' => 'required|date',
            'start_timee' => 'required|date_format:H:i',
            'end_timee' => 'required|date_format:H:i|after:start_timee',
            'filee' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reasone' => 'nullable|string|max:255',
            'total_hourse' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        try {
            $attendance = EmployeeAttendance::findOrFail($id);

            // Update attendance fields
            $attendance->date = $request->datee;
            $attendance->timeIn = Carbon::createFromFormat('H:i', $request->start_timee)->format('h:i:s A');
            $attendance->timeOut = Carbon::createFromFormat('H:i', $request->end_timee)->format('h:i:s A');
            $attendance->reason = $request->reasone;

            // Check if a new file is uploaded
            if ($request->hasFile('filee')) {
                $filePath = $request->file('filee')->store('attendance_files', 'public');
                $attendance->image_path = $filePath; // Update with new file path
            }

            // Determine if manual entry of total hours is provided
            $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
            if ($isManualEntry && $request->total_hourse) {
                $attendance->manualTimeTotal = $request->total_hourse; // Set manual value
            } else {
                // Calculate time total dynamically if no manual entry is provided
                $attendance->manualTimeTotal = $this->calculateTotalHours(
                    $request->start_timee,
                    $request->end_timee,
                );
            }

            // Save the updated attendance record
            $attendance->save();

            Log::info("Attendance record updated with ID: " . $attendance->id);
            return redirect()->back()->with('success', 'Attendance record updated successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to update attendance: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to update attendance record. Please try again.']);
        }
    }

    protected function calculateTotalHours($startTime, $endTime, $breakHours = null)
    {
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        // Calculate total work time in seconds
        $totalHoursInSeconds = $end->diffInSeconds($start, false);

        // Handle cases where end time is before start time (overnight shifts)
        if ($totalHoursInSeconds < 0) {
            $totalHoursInSeconds += 24 * 60 * 60; // Add 24 hours in seconds
        }

        // Subtract break time if provided
        if ($breakHours) {
            [$breakHrs, $breakMins, $breakSecs] = explode(':', $breakHours);
            $breakInSeconds = ($breakHrs * 3600) + ($breakMins * 60) + $breakSecs;
            $totalHoursInSeconds = max(0, $totalHoursInSeconds - $breakInSeconds);
        }

        // Convert seconds to HH:MM:SS format
        return gmdate('H:i:s', $totalHoursInSeconds);
    }

    public function approveAttendance($id)
    {
        try {
            // Find the attendance record
            $attendance = EmployeeAttendance::findOrFail($id);
            $authUser = Auth::user();

            // Validation: Check if the user is trying to approve their own request
            if ($attendance->users_id === $authUser->id) {
                return redirect()->back()->with('error', 'You cannot approve your own attendance request.');
            }

            // Retrieve the user associated with the attendance record
            $user = $attendance->user;
            $attendanceCredits = AttendanceCredit::where('user_id', $user->id)->first();

            // Check if attendance credits exist
            if (!$attendanceCredits) {
                return redirect()->back()->with('error', 'Attendance credits not found for this user.');
            }

            // Check if user has enough attendance credits (1 credit per approval)
            if ($attendanceCredits->attendanceCredits < 1) {
                return redirect()->back()->with('error', 'User does not have enough attendance credits to cover this attendance.');
            }

            // Deduct 1 attendance credit **before approving**
            $attendanceCredits->attendanceCredits -= 1;
            $attendanceCredits->save();

            // Update status and approved_by fields after validation
            $attendance->status_code = 'Approved';
            $attendance->approved_by = $authUser->id; // Ensure `approved_by` field exists in your table
            $attendance->save();

            // Success message
            return redirect()->back()->with('success', 'Attendance approved and one attendance credit deducted successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where attendance record is not found
            return redirect()->back()->with('error', 'Attendance record not found.');
        } catch (\Exception $e) {
            // Handle any other errors
            return redirect()->back()->with('warning', 'An error occurred while approving the attendance: ' . $e->getMessage());
        }
    }


    public function declineAttendance($id)
    {
        try {
            $attendance = EmployeeAttendance::findOrFail($id);
            $authUser = Auth::user();

            // Check if attendance was previously approved
            if ($attendance->status_code === 'Approved') {
                $attendanceCredits = AttendanceCredit::where('user_id', $attendance->user->id)->first();

                if (!$attendanceCredits) {
                    return redirect()->back()->with('error', 'No attendance credits for this employee.');
                }

                // Restore one attendance credit
                $attendanceCredits->attendanceCredits += 1;
                $attendanceCredits->save();
            }

            // Update status to Declined
            $attendance->status_code = 'Declined';
            $attendance->approved_by = $authUser->id;
            $attendance->save();

            return redirect()->back()->with('success', 'Request Declined, and attendance credit restored if previously deducted.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the attendance: ' . $e->getMessage());
        }
    }


    public function deleteCertificateAttendance($id)
    {
        $attReq = EmployeeAttendance::findOrFail($id);

        if ($attReq->status_code == 'Approved') {
            Alert::error('This attendance request has already been approved and cannot be deleted.');
            return redirect()->back();
        }

        $attReq->delete();

        return redirect()->back()->with('success', 'Deleted Successfully!');
    }

    // Attendance Request

    public function reqattendance()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $authId = Auth::user()->id;

        // Retrieve the attendance credits for the authenticated user
        $attendanceCredit = AttendanceCredit::where('user_id', $authId)->first();

        // If attendance credit exists, use it; otherwise, set remaining credits to zero
        $remainingCredits = $attendanceCredit ? $attendanceCredit->attendanceCredits : 0;

        // Counts for attendance requests
        $reqcount = EmployeeAttendance::whereIn('status_code', ['Pending', 'Approved'])
            ->where('users_id', $authId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $pendingCount = EmployeeAttendance::where('status_code', 'Pending')
            ->where('users_id', $authId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $decCount = EmployeeAttendance::where('status_code', 'Declined')
            ->where('users_id', $authId)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        // Retrieve attendance requests with different status codes
        $pending = EmployeeAttendance::where('users_id', $authId)
            ->whereIn('status_code', ['Pending', 'Approved', 'Declined'])
            ->get();

        // Pass remaining credits to the view along with other data
        return view('hr.attendance.request', compact('pending', 'pendingCount', 'reqcount', 'decCount', 'remainingCredits'));
    }

    public function hrstoreCertificateAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reason' => 'nullable|string|max:255',
            'total_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        // Ensure that the request date is at most one day before today
        $requestedDate = Carbon::parse($request->date);
        $yesterday = Carbon::yesterday();

        if ($requestedDate->lt($yesterday)) {
            return redirect()->back()->with(['error' => 'You can only request attendance from the day before the current date.']);
        }

        $filePath = $request->hasFile('file') ? $request->file('file')->store('attendance_files', 'public') : null;

        try {
            // Convert timeIn and timeOut to 12-hour format for database storage
            $timeInFormatted = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i:s A');
            $timeOutFormatted = Carbon::createFromFormat('H:i', $request->end_time)->format('h:i:s A');

            // Check for existing attendance with the same date, timeIn, and timeOut
            $existingAttendance = EmployeeAttendance::where('users_id', Auth::id())
                ->where('date', $request->date)
                ->first();

            if ($existingAttendance) {
                return redirect()->back()->with(['error' => 'An attendance record for this date already exists.']);
            }

            // Create new attendance record
            $attendance = new EmployeeAttendance();
            $attendance->users_id = Auth::id();
            $attendance->date = $request->date;
            $attendance->timeIn = $timeInFormatted;
            $attendance->timeOut = $timeOutFormatted;
            $attendance->image_path = $filePath;
            $attendance->reason = $request->reason;
            $attendance->status_code = 'Pending';

            // Check for manual entry or calculate total hours
            $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
            if ($isManualEntry && $request->total_hours) {
                $attendance->manualTimeTotal = $request->total_hours; // Set manual value
            } else {
                $attendance->manualTimeTotal = $this->calculateTotalHours($request->start_time, $request->end_time, $request->break_hours);
            }

            // Save the attendance record
            $attendance->save();

            Log::info("Attendance record saved with ID: " . $attendance->id);
            return redirect()->back()->with('success', 'Attendance record saved successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to save attendance: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to save attendance record. Please try again.']);
        }
    }

    public function hrupdateCertificateAttendance(Request $request, $id)
    {
        $request->validate([
            'datee' => 'required|date',
            'start_timee' => 'required|date_format:H:i',
            'end_timee' => 'required|date_format:H:i|after:start_timee',
            'filee' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reasone' => 'nullable|string|max:255',
            'total_hourse' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        try {
            $attendance = EmployeeAttendance::findOrFail($id);

            // If the date field has been changed, check for duplicates
            if ($attendance->date !== $request->datee) {
                $existingAttendance = EmployeeAttendance::where('users_id', Auth::id())
                    ->where('date', $request->datee)
                    ->first();

                if ($existingAttendance) {
                    return redirect()->back()->with(['error' => 'An attendance record with this date already exists.']);
                }
            }

            // Update attendance fields
            $attendance->date = $request->datee;
            $attendance->timeIn = Carbon::createFromFormat('H:i', $request->start_timee)->format('h:i:s A');
            $attendance->timeOut = Carbon::createFromFormat('H:i', $request->end_timee)->format('h:i:s A');
            $attendance->reason = $request->reasone;

            // Check if a new file is uploaded
            if ($request->hasFile('filee')) {
                $filePath = $request->file('filee')->store('attendance_files', 'public');
                $attendance->image_path = $filePath; // Update with new file path
            }

            // Determine if manual entry of total hours is provided
            $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
            if ($isManualEntry && $request->total_hourse) {
                $attendance->manualTimeTotal = $request->total_hourse; // Set manual value
            } else {
                // Calculate time total dynamically if no manual entry is provided
                $attendance->manualTimeTotal = $this->calculateTotalHours(
                    $request->start_timee,
                    $request->end_timee,
                );
            }

            // Save the updated attendance record
            $attendance->save();

            Log::info("Attendance record updated with ID: " . $attendance->id);
            return redirect()->back()->with('success', 'Attendance record updated successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to update attendance: " . $e->getMessage());
            return redirect()->back()->with(['error' => 'Failed to update attendance record. Please try again.']);
        }
    }

    public function hrdeleteCertificateAttendance($id)
    {
        $attReq = EmployeeAttendance::findOrFail($id);

        if ($attReq->status_code == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be deleted.');
            return redirect()->back();
        }

        $attReq->delete();

        return redirect()->back()->with('warning', 'Deleted Successfully!');
    }
}
