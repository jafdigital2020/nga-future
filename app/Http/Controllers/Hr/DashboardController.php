<?php

namespace App\Http\Controllers\Hr;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Policy;
use App\Models\LeaveCredit;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\BreakSettings;
use App\Models\ShiftSchedule;
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
use Karmendra\LaravelAgentDetector\AgentDetector;
use App\Notifications\AttendanceSubmissionNotification;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $authUserId = $user->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
      // Retrieve the max breaks setting with a null check
      $breakSettings = BreakSettings::first();
      $maxBreaks = $breakSettings ? $breakSettings->max_breaks : 0;
    
        // Get today’s attendance record and decode the breaks
        $currentDate = Carbon::today()->toDateString();
        $attendance = EmployeeAttendance::where('users_id', $authUserId)
                                        ->where('date', $currentDate)
                                        ->first();
        $breaks = $attendance ? json_decode($attendance->breaks, true) : [];
    
        // Calculate other necessary data for the dashboard
        $totalLeaveCredits = LeaveCredit::where('user_id', $authUserId)->sum('remaining_credits');
        $latestAnnouncement = Announcement::latest()->first();
        $policies = Policy::all();
    
        $attendanceApproved = EmployeeAttendance::where('users_id', $authUserId)
                                 ->where('status', 'Approved')
                                 ->count();
    
        $leaveApproved = LeaveRequest::where('users_id', $authUserId)
                                  ->where('status', 'Approved')
                                  ->count();
    
        $leavePending = LeaveRequest::where('users_id', $authUserId)
                                  ->where('status', 'Pending')
                                  ->count();
           
        $record = EmploymentRecord::where('users_id', $user->id)->get();
        $salrecord = EmployementSalary::where('users_id', $user->id)->get();
    
        // Filter attendance data based on status_code and date range
        $data = EmployeeAttendance::where('users_id', $authUserId)
            ->whereIn('status_code', ['Active', 'Approved']);
        
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
        $empatt = DB::table('attendance')->where('users_id', $authUserId)->get();
        $latest = EmployeeAttendance::where('users_id', $authUserId)->latest()->first();
    
        $today = Carbon::today();
    
        // Get the nearest holiday after or equal to today
        $nearestHoliday = SettingsHoliday::where('holidayDate', '>=', $today)
                                          ->orderBy('holidayDate', 'asc')
                                          ->first();
        
        return view('hr.dashboard', compact(
            'attendanceApproved', 'policies', 'user', 'empatt', 'all', 'total', 'latest', 
            'filteredData', 'supervisor', 'record', 'salrecord', 'leaveApproved', 
            'leavePending', 'nearestHoliday', 'latestAnnouncement', 'totalLeaveCredits',
            'breaks', 'maxBreaks' 
        ));
    }

    public function getUserAttendance(Request $request)
    {
        $authUserId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
    // Fetch attendance data with status_code filter
    $attendanceData = EmployeeAttendance::where('users_id', $authUserId)
        ->whereBetween('date', [$startDate, $endDate])
        ->whereIn('status_code', ['Active', 'Approved'])  // Add status_code filter
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
        try {
            $currentDate = Carbon::now('Asia/Manila')->toDateString();
            Log::info("Current Date: $currentDate");

            $timeIn = Carbon::now('Asia/Manila');

            // Get user's shift schedule
            $shiftSchedule = ShiftSchedule::where('users_id', auth()->user()->id)
                ->where('date', $currentDate)
                ->first();

            if (!$shiftSchedule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shift schedule not found for today.'
                ], 404);
            }

            // Determine status and total late
            $status = 'On Time';
            $totalLate = '00:00:00';
            $timeEnd = null;
            $shiftOver = null;

            if (!$shiftSchedule->isFlexibleTime) {
                $shiftStart = Carbon::parse($shiftSchedule->shiftStart, 'Asia/Manila');
                $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');
                $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila');

                if ($timeIn->gt($lateThreshold)) {
                    $status = 'Late';
                    $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
                    $totalLate = gmdate('H:i:s', $totalLateInSeconds);
                }

                $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
                $calculatedTimeEnd = $timeIn->copy()->addSeconds($allowedHours)->addHour();

                $timeEnd = $calculatedTimeEnd->greaterThan($shiftEnd)
                    ? $shiftEnd->format('h:i:s A')
                    : $calculatedTimeEnd->format('h:i:s A');

                $shiftOver = $shiftEnd->format('h:i:s A');
            }

            // Check if user has already timed in
            $employeeAttendance = auth()->user()->employeeAttendance()
                ->where('date', $currentDate)
                ->first();

            if ($employeeAttendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already timed in!'
                ], 400);
                
            }

            // Geolocation data
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            // Handle low accuracy with photo upload
            if ($request->has('low_accuracy') && $request->low_accuracy) {
                if (!$request->hasFile('image')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Image upload is required for low accuracy check-in.'
                    ], 400);
                }

                $imagePath = $request->file('image')->store('checkin_photos', 'public');

                auth()->user()->employeeAttendance()->create([
                    'name' => auth()->user()->fName . ' ' . auth()->user()->lName,
                    'date' => $currentDate,
                    'timeIn' => $timeIn->format('h:i:s A'),
                    'status' => 'On Time',
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $request->location,
                    'image_path' => $imagePath,
                    'status_code' => 'Active',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Checked in successfully with photo!'
                ], 200);
            }

            // Save attendance
            auth()->user()->employeeAttendance()->create([
                'name' => auth()->user()->fName . ' ' . auth()->user()->lName,
                'date' => $currentDate,
                'timeIn' => $timeIn->format('h:i:s A'),
                'status' => $status,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location' => $request->location,
                'totalLate' => $totalLate,
                'timeEnd' => $timeEnd,
                'shiftOver' => $shiftOver,
                'device' => $request->header('User-Agent'),
                'status_code' => 'Active',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked in successfully!'
            ], 200);
        } catch (Exception $e) {
            Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius of the Earth in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // Distance in meters
    }

    public function breakIn(Request $request)
    {
        $request->user()->breakIn();

        return redirect('/hr/dashboard');

    }

    public function breakOut(Request $request)
    {
        $request->user()->breakOut();

        return redirect('/hr/dashboard');
    }

        // ** 15MINS BREAK ** //

    public function startBreak(Request $request)
    {
        try {
            $user = auth()->user();
            $currentDate = Carbon::now('Asia/Manila')->toDateString();
    
            // Get the attendance record for today
            $attendance = EmployeeAttendance::where('users_id', $user->id)
                                            ->where('date', $currentDate)
                                            ->first();
    
            if (!$attendance) {
                return redirect()->back()->with('error', 'Attendance record not found for today.');
            }
    
            // Get the max number of breaks allowed from settings
            $maxBreaks = BreakSettings::first()->max_breaks;
    
            // Decode the current breaks array or initialize it
            $breaks = $attendance->breaks ? json_decode($attendance->breaks, true) : [];
    
            // Check if the max number of breaks is already reached
            if (count($breaks) >= $maxBreaks) {
                return redirect()->back()->with('error', 'You have reached the maximum number of 15mins breaks for today.');
            }
    
            // Add a new break with the start time in hh:mm:ss AM/PM format and no end time
            $breaks[] = ['start' => Carbon::now('Asia/Manila')->format('h:i:s A'), 'end' => null];
            $attendance->breaks = json_encode($breaks);
            $attendance->save();
    
            return redirect()->back()->with('success', '15-minute break started.');
        } catch (\Exception $e) {
            Log::error('Start Break Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while starting the break.');
        }
    }
    
    public function endBreak(Request $request)
    {
        try {
            $user = auth()->user();
            $currentDate = Carbon::now('Asia/Manila')->toDateString();
    
            // Get the attendance record for today
            $attendance = EmployeeAttendance::where('users_id', $user->id)
                                            ->where('date', $currentDate)
                                            ->first();
    
            if (!$attendance) {
                return redirect()->back()->with('error', 'Attendance record not found for today.');
            }
    
            // Decode the breaks array
            $breaks = $attendance->breaks ? json_decode($attendance->breaks, true) : [];
    
            // Find the first break that has a start time but no end time
            $breakEnded = false;
            foreach ($breaks as &$break) {
                if ($break['start'] && !$break['end']) {
                    // Set the end time in hh:mm:ss AM/PM format
                    $break['end'] = Carbon::now('Asia/Manila')->format('h:i:s A');
                    $breakEnded = true;
                    break;
                }
            }
    
            if (!$breakEnded) {
                return redirect()->back()->with('error', 'No active 15-mins break to end.');
            }
    
            // Save the updated breaks array
            $attendance->breaks = json_encode($breaks);
            $attendance->save();
    
            return redirect()->back()->with('success', '15-minute break ended.');
        } catch (\Exception $e) {
            Log::error('End Break Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while ending the break.');
        }
    }

    // ** CLOCK-OUT ** //
    public function update(Request $request)
    {
        $request->user()->checkOut();
        
        return redirect('/hr/dashboard');
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
            'paid_leave' => 'required|integer', 
            'approved_overtime' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/', 
            'regular_holiday_hours' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/', // New field for regular holiday hours
            'special_holiday_hours' => 'required|regex:/^\d{2}:\d{2}:\d{2}$/', // New field for special holiday hours
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
            $attendance->paidLeave = $request->input('paid_leave'); 
            $attendance->approvedOvertime = $request->input('approved_overtime');
            $attendance->regular_holiday_hours = $request->input('regular_holiday_hours'); // Save regular holiday hours
            $attendance->special_holiday_hours = $request->input('special_holiday_hours'); // Save special holiday hours
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
                ->unique('id')  
                ->filter();  
    
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
