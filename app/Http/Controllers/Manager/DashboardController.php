<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Policy;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\SettingsHoliday;
use App\Models\EmploymentRecord;
use App\Models\EmployementSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $authUserId = $user->id;
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

        $today = Carbon::today();
    
        // Get the nearest holiday after or equal to today
        $nearestHoliday = SettingsHoliday::where('holidayDate', '>=', $today)
                                          ->orderBy('holidayDate', 'asc')
                                          ->first();

        return view('manager.dashboard', compact('policies','latestAnnouncement','user', 'empatt', 'all', 'total', 'latest', 'filteredData', 'supervisor', 'record', 'salrecord', 'leaveApproved', 'leavePending', 'nearestHoliday'));
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

    public function getHolidays() 
    {
        $holidays = SettingsHoliday::all();
        
        return response()->json($holidays);
    }


}
