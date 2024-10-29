<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Policy;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\SettingsHoliday;
use App\Models\EmploymentRecord;
use App\Models\EmployementSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AttendanceSubmissionNotification;

class DashboardController extends Controller
{
    // public function index (Request $request)
    // {
    //     $user = Auth::user();
    //     $att = Auth::user()->id;
    //     $EmployeeAttendance = EmployeeAttendance::latest()->first();
    //     $total = EmployeeAttendance::sum('timeTotal');
    //     $all = DB::table('attendance')->get();
    //     $empatt = DB::table('attendance')->where('users_id', auth()->user()->id)->get();
    //     $latest = EmployeeAttendance::where('users_id', Auth::user()->id)->latest()->first();
    //     $data = EmployeeAttendance::where('users_id', Auth::user()->id)->get();

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
    //     $attendanceData = $filteredData->groupBy('date');
    
     
    //     return view('emp.dashboard', compact('att', 'user', 'empatt', 'all', 'total', 'latest', 'data', 'filteredData', 'totalTime', 'totalLate', 'attendanceData'));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $authUserId = auth()->user()->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $latestAnnouncement = Announcement::latest()->first();
        $policies = Policy::all();
    
        $leaveApproved = LeaveRequest::where('users_id', auth()->user()->id)
                                  ->where('status', 'Approved')
                                  ->count();

        $leavePending = LeaveRequest::where('users_id', auth()->user()->id)
                                  ->where('status', 'Pending')
                                  ->count();
           
        $record = EmploymentRecord::where('users_id', $user->id)->get();
        $salrecord = EmployementSalary::where('users_id', $user->id)->get();
        $data = EmployeeAttendance::where('users_id', $authUserId);
        
        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        }
    
        $filteredData = $data->get();
    
        $department = $user->department;
        $supervisor = $user->supervisor;
        
        if ($request->ajax()) {
            $leaveRequests = LeaveRequest::where('users_id', $authUserId)
                ->where(function($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                          ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
                })
                ->get();  
            return response()->json([
                'attendance' => $filteredData,
                'leaves' => $leaveRequests,
            ]);
        }
    
        $total = EmployeeAttendance::sum('timeTotal');
        $all = DB::table('attendance')->get();
        $empatt = DB::table('attendance')->where('users_id', auth()->user()->id)->get();
        $latest = EmployeeAttendance::where('users_id', Auth::user()->id)->latest()->first();

        $today = Carbon::today();
    
        // Get the nearest holiday after or equal to today
        $nearestHoliday = SettingsHoliday::where('holidayDate', '>=', $today)
                                          ->orderBy('holidayDate', 'asc')
                                          ->first();
        
        return view('emp.dashboard', compact('policies','user', 'empatt', 'all', 'total', 'latest', 'filteredData', 'supervisor', 'record', 'salrecord', 'leaveApproved', 'leavePending', 'nearestHoliday', 'latestAnnouncement'));
    }
    

    public function getUserAttendance(Request $request)
    {
        $authUserId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        // Fetch attendance data
        $attendanceData = EmployeeAttendance::where('users_id', $authUserId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'timeIn', 'timeOut', 'timeTotal', 'totalLate')
            ->get();
        
        // Fetch leave requests with leave type
        $leaveRequests = LeaveRequest::with('leaveType')
            ->where('users_id', $authUserId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
            })
            ->get();  
        
        // Fetch approved overtime requests
        $approvedOvertime = OvertimeRequest::where('users_id', $authUserId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'Approved')
            ->select('date', 'start_time', 'end_time', 'total_hours', 'reason')
            ->get();
    
        return response()->json([
            'attendance' => $attendanceData,
            'leaves' => $leaveRequests,
            'overtime' => $approvedOvertime,
        ]);
    }
    
    

    public function store(Request $request)
    {
        $request->user()->checkIn($request);

        return redirect('/emp/dashboard');
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/emp/dashboard');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();

        return redirect('/emp/dashboard');
    }


    public function update(Request $request)
    {
        $request->user()->checkOut();

        return redirect('/emp/dashboard');
    }

    public function check(Request $request)
    {
        $attendance = ApprovedAttendance::where('cut_off', $request->cutoff)
                                        ->where('name', Auth::user()->name)
                                        ->where('start_date', $request->start_date)
                                        ->where('end_date', $request->end_date)
                                        ->first();

        if ($attendance) {
            return response()->json(['exists' => true, 'status' => $attendance->status]);
        }

        return response()->json(['exists' => false]);
    }

    public function saveAttendance(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Saving attendance:', $request->all());
    
        // Validate the request data
        $request->validate([
            'total_worked' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/',
            'total_late' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/',
            'cutoff' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'unpaid_leave' => 'required|integer',
            'paid_leave' => 'required|integer', // New validation for paid leaves
            'approved_overtime' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/', // New validation for approved overtime
            'status' => 'required|string|in:pending,approved,rejected,sent'
        ]);
    
        // Check if the attendance record already exists for the same user and cutoff
        $existingAttendance = ApprovedAttendance::where('users_id', Auth::id())
            ->where('cut_off', $request->input('cutoff'))
            ->where('start_date', $request->input('start_date'))
            ->where('end_date', $request->input('end_date'))
            ->first();
    
        if ($existingAttendance) {
            return response()->json(['message' => 'Attendance record already exists for this cutoff.'], 409);
        }
    
        try {
            // Create a new ApprovedAttendance record
            $attendance = new ApprovedAttendance();
            $attendance->users_id = Auth::id(); 
            $attendance->name = Auth::user()->fName . ' ' . Auth::user()->lName;
            $attendance->department = Auth::user()->department;
            $attendance->month = date('F'); 
            $attendance->year = $request->input('year');
            $attendance->totalHours = $request->input('total_worked');
            $attendance->totalLate = $request->input('total_late');
            $attendance->cut_off = $request->input('cutoff');
            $attendance->start_date = $request->input('start_date');
            $attendance->end_date = $request->input('end_date');
            $attendance->unpaidLeave = $request->input('unpaid_leave');
            $attendance->paidLeave = $request->input('paid_leave'); // New field for paid leaves
            $attendance->approvedOvertime = $request->input('approved_overtime'); // New field for approved overtime
            $attendance->status = $request->input('status');
    
            // Save the record to the database
            $attendance->save();
    
            // Get the authenticated user
            $user = Auth::user();
    
            // Get the supervisor for the user's department
            $supervisor = $user->supervisor;
    
            // Get all HR users
            $hrUsers = User::where('role_as', User::ROLE_HR)->get();
    
            // Get all Admin users
            $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
            // Combine HR, Admin, and Supervisor to avoid duplicates
            $notifiableUsers = collect([$supervisor])
                ->merge($hrUsers)
                ->merge($adminUsers)
                ->unique('id')  // Ensure no user is notified more than once
                ->filter();  // Remove any null values (in case supervisor is null)
    
            // Notify all unique users
            foreach ($notifiableUsers as $notifiableUser) {
                $notifiableUser->notify(new AttendanceSubmissionNotification($attendance, $user));
            }
    
            // Return a success response
            return response()->json(['message' => 'Attendance saved successfully.'], 200);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error saving attendance:', ['error' => $e->getMessage()]);
    
            // Return an error response
            return response()->json(['message' => 'Error saving attendance.', 'error' => $e->getMessage()], 500);
        }
    }
    

    public function getStatus(Request $request)
    {
        $cutoff = $request->input('cutoff');

        $attendance = ApprovedAttendance::where('cut_off', $cutoff)->first();

        if ($attendance) {
            return response()->json(['status' => $attendance->status]);
        }

        return response()->json(['status' => 'New']);
    }

    public function getHolidays() 
    {
        $holidays = SettingsHoliday::all();
        
        return response()->json($holidays);
    }

}
