<?php



namespace App\Http\Controllers\Employee\api;


use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Policy;
use App\Models\LeaveCredit;
use App\Models\Announcement;
use App\Models\LeaveRequest;
use App\Models\UserGeofence;
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


class AttendanceController extends Controller
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

        return view('emp.dashboard', compact(
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

    // ** Clock In ** //

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

                $data = auth()->user()->employeeAttendance()->create([
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
                    'message' => 'Checked in successfully with photo!',
                    'data' => $data
                ], 200);
            }

            // Save attendance
            $data = auth()->user()->employeeAttendance()->create([
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
                'message' => 'Checked in successfully!',
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e
            ], 500);
        }
    }


    public function getattendance(Request $request){

        $userId = $request->input('user_id');
        $data = EmployeeAttendance::where('users_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);

    }


    // ** TIME OUT ** //
    public function clockout(Request $request)
    {
        try{

            // $now = Carbon::now('Asia/Manila');
            // $dateToday = $now->toDateString();
            // $dateYesterday = $now->copy()->subDay()->toDateString();


            // $timeIn = $this->employeeAttendance()
            //     ->where(function ($query) use ($dateToday, $dateYesterday) {
            //         $query->whereDate('date', $dateToday)
            //             ->orWhereDate('date', $dateYesterday);
            //     })
            //     ->whereNull('timeOut')
            //     ->latest('timeIn')
            //     ->first();

            // if (!$timeIn) {
            //     return back()->with('error', "You don't have a time-in record. Please time in first.");
            // }




            $request->user()->clockoutt();

            return response()->json([
                    'status' => 'success',
                    'message' => 'You have successfully timed out. Thank you for your hard work!'
                ], 200);
        }
        catch (Exception $e) {
            Log::error('Check-out Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
        }

    }





}
