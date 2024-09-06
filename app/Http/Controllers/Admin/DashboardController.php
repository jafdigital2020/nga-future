<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\EmploymentRecord;
use App\Models\EmployementSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $authUserId = auth()->user()->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        $record = EmploymentRecord::whereHas('user', function ($query) {
            $query->where('role_as', '!=', '1');
        })->get();
        $salrecord = EmployementSalary::whereHas('user', function ($query) {
            $query->where('role_as', '!=', '1');
        })->get();
        $data = EmployeeAttendance::where('users_id', $authUserId);
        
        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        }
    
        $filteredData = $data->get();
    
        $department = $user->department;
        $supervisor = User::getSupervisorForDepartment($department, $user);
        
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

         // Fetch login count for today
         $todayLoginCount = 0;
         $attendanceQuery = EmployeeAttendance::query();
 
         if ($user->isSupervisor()) {
             $attendanceQuery->whereHas('user', function ($query) use ($user) {
                 $query->where('department', $user->department);
             });
         }
 
         $attendanceQuery->whereDate('date', today());
 
         $todayLoginCount = $attendanceQuery->distinct('users_id')->count('users_id');
 
         // Leave Counts
         $vacationLeaveCountToday = LeaveRequest::where('type', 'Vacation Leave')
                                    ->where('status', 'Approved')
                                    ->where(function($query) {
                                         $query->whereDate('start_date', today())
                                               ->orWhereDate('end_date', today())
                                               ->orWhere(function($query) {
                                                   $query->where('start_date', '<=', today())
                                                         ->where('end_date', '>=', today());
                                               });
                                     })
                                     ->count();
 
         $sickLeaveCountToday = LeaveRequest::where('type', 'Sick Leave')
                                    ->where('status', 'Approved')
                                    ->where(function($query) {
                                         $query->whereDate('start_date', today())
                                               ->orWhereDate('end_date', today())
                                               ->orWhere(function($query) {
                                                   $query->where('start_date', '<=', today())
                                                         ->where('end_date', '>=', today());
                                               });
                                     })
                                     ->count();
 
         $birthdayLeaveCountToday = LeaveRequest::where('type', 'Birthday Leave')
                                    ->where('status', 'Approved')
                                    ->where(function($query) {
                                         $query->whereDate('start_date', today())
                                               ->orWhereDate('end_date', today())
                                               ->orWhere(function($query) {
                                                   $query->where('start_date', '<=', today())
                                                         ->where('end_date', '>=', today());
                                               });
                                     })
                                     ->count();
 
         $unpaidLeaveCountToday = LeaveRequest::where('type', 'Unpaid Leave')
                                    ->where('status', 'Approved')
                                    ->where(function($query) {
                                         $query->whereDate('start_date', today())
                                               ->orWhereDate('end_date', today())
                                               ->orWhere(function($query) {
                                                   $query->where('start_date', '<=', today())
                                                         ->where('end_date', '>=', today());
                                               });
                                     })
                                     ->count();

        $department = $request->get('department');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Fetch all users with their attendance for the specified month
        $users = User::with(['employeeAttendance' => function($query) use ($year, $month) {
            $query->whereYear('date', $year)
                  ->whereMonth('date', $month);
        }])->get();

        $departments = User::select('department')->distinct()->get();

        $totalUsers = User::where('role_as', '!=', 1)->count();

        // Current month new users excluding role_as = 1
        $currentMonth = Carbon::now()->startOfMonth();
        $newUsersThisMonth = User::where('created_at', '>=', $currentMonth)
                                ->where('role_as', '!=', 1)
                                ->count();

        
        // Last month new users excluding role_as = 1
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $newUsersLastMonth = User::whereBetween('created_at', [$lastMonth, $lastMonth->copy()->endOfMonth()])
                                ->where('role_as', '!=', 1)
                                ->count();

        // Percentage increase calculation with safe checks
        $percentageIncrease = ($newUsersLastMonth > 0)
                            ? (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 
                            : ($newUsersThisMonth > 0 ? 100 : 0);
        
        $today = now()->format('Y-m-d');

        $hasTimeIn = $user->employeeAttendance()->whereDate('date', $today)->exists();
        $hasBreakOut = $user->employeeAttendance()->whereDate('date', $today)->whereNotNull('breakOut')->exists();
        $hasBreakIn = $user->employeeAttendance()->whereDate('date', $today)->whereNotNull('breakIn')->exists();
        
        return view('admin.dashboard', compact(
        'user', 
        'empatt', 
        'all', 
        'total', 
        'latest', 
        'filteredData', 
        'supervisor', 
        'record', 
        'salrecord', 
        'todayLoginCount',  
        'vacationLeaveCountToday', 
        'sickLeaveCountToday', 
        'birthdayLeaveCountToday', 
        'unpaidLeaveCountToday', 
        'totalUsers',
        'users',
        'year',
        'month',
        'departments',
        'hasTimeIn',
        'hasBreakOut',
        'hasBreakIn',
        'newUsersThisMonth',
        'percentageIncrease',
        ));
    }

    public function getUserAttendance(Request $request)
    {
        $authUserId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $attendanceData = EmployeeAttendance::where('users_id', $authUserId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'timeIn', 'timeOut', 'timeTotal', 'totalLate')
            ->get();
        
        $leaveRequests = LeaveRequest::where('users_id', $authUserId)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
            })
            ->get();  
        
        return response()->json([
            'attendance' => $attendanceData,
            'leaves' => $leaveRequests,
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->checkIn();

        return redirect('/admin/dashboard');
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/admin/dashboard');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();

        return redirect('/admin/dashboard');
    }


    public function update(Request $request)
    {
        $request->user()->checkOut();
        
        return redirect('/admin/dashboard');
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
            'vacation_leave' => 'required|integer',
            'sick_leave' => 'required|integer',
            'birthday_leave' => 'required|integer',
            'status' => 'required|string|in:pending,approved,rejected,sent'
        ]);

        try {
            // Create a new ApprovedAttendance record
            $attendance = new ApprovedAttendance();
            $attendance->users_id = Auth::id(); 
            $attendance->name = Auth::user()->name;
            $attendance->department = Auth::user()->department;
            $attendance->month = date('F'); 
            $attendance->totalHours = $request->input('total_worked');
            $attendance->totalLate = $request->input('total_late');
            $attendance->cut_off = $request->input('cutoff');
            $attendance->start_date = $request->input('start_date');
            $attendance->end_date = $request->input('end_date');
            $attendance->unpaidLeave = $request->input('unpaid_leave');
            $attendance->vacLeave = $request->input('vacation_leave');
            $attendance->sickLeave = $request->input('sick_leave');
            $attendance->bdayLeave = $request->input('birthday_leave');
            $attendance->status = $request->input('status');

            // Save the record to the database
            $attendance->save();

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

    
}
