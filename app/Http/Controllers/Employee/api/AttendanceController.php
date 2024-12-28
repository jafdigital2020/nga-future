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
use Carbon\CarbonInterval;


class AttendanceController extends Controller
{

    public function getTodayAttendance(Request $request)
    {
        try {
            $currentDate = Carbon::now('Asia/Manila')->toDateString();

            // AuthBearer Token Needed
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access. Please log in.'
                ], 401);
            }

            $data = $user->employeeAttendance()
                ->whereDate('date', $currentDate)
                ->orderBy('timeIn', 'asc')
                ->get();

            if ($data === null || $data->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'You have not timed in today'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Log::error('Get Today Attendance Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e
            ], 500);
        }
    }

    public function getDayAttendance(Request $request)
    {
        try {
            $selectedDate = $request->input('date');

            // AuthBearer Token Needed
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access. Please log in.'
                ], 401);
            }

            $data = $user->employeeAttendance()
                ->whereDate('date', $selectedDate)
                ->orderBy('timeIn', 'asc')
                ->get();

            if ($data === null || $data->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'You have do not have record for this day'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Log::error('Get Today Attendance Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e
            ], 500);
        }
    }

    public function getMonthlyAttendance(Request $request)
    {
        try {
            $currentMonthYear = $request->input('monthYear', Carbon::now('Asia/Manila')->format('Y-m'));

            // AuthBearer Token Needed
            $user = auth()->user();

            $data = $user->employeeAttendance()
                ->where('date', 'like', "$currentMonthYear%")
                ->orderBy('date', 'asc')
                ->get();

            if ($data === null || $data->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No attendance records found for this month'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Log::error('Get Monthly Attendance Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'error' => $e
            ], 500);
        }
    }

    public function startBreak15m(Request $request)
    {
        try {
            $user = auth()->user();
            $currentDate = Carbon::now('Asia/Manila')->toDateString();

            // Get the attendance record for today
            $attendance = EmployeeAttendance::where('users_id', $user->id)
                ->where('date', $currentDate)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Attendance record not found for today.'
                ], 404);
            }

            // Get the max number of breaks allowed from settings
            $maxBreaks = BreakSettings::first()->max_breaks;

            // Decode the current breaks array or initialize it
            $breaks = $attendance->breaks ? json_decode($attendance->breaks, true) : [];

            // Check if there's an ongoing break (end is null)
            foreach ($breaks as $break) {
                if ($break['end'] === null) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You already have an ongoing 15-minute break.'
                    ], 400);
                }
            }

            // Check if the max number of breaks is already reached
            if (count($breaks) >= $maxBreaks) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have reached the maximum number of 15-minute breaks for today.'
                ], 400);
            }

            // Add a new break with the start time in hh:mm:ss AM/PM format and no end time
            $breaks[] = ['start' => Carbon::now('Asia/Manila')->format('h:i:s A'), 'end' => null];
            $attendance->breaks = json_encode($breaks);
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => '15-minute break started.',
                'breaks' => $breaks
            ], 200);
        } catch (\Exception $e) {
            Log::error('Start Break Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while starting the break.'
            ], 500);
        }
    }

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

            // Check if the user has already timed out
            if ($attendance->timeOut !== null) {
                return redirect()->back()->with('error', 'You cannot take a 15-minute break after clocking out.');
            }

            // Get the max number of breaks allowed from settings
            $maxBreaks = BreakSettings::first()->max_breaks;

            // Decode the current breaks array or initialize it
            $breaks = $attendance->breaks ? json_decode($attendance->breaks, true) : [];

            // Check if there's an ongoing break (end is null)
            foreach ($breaks as $break) {
                if ($break['end'] === null) {
                    return redirect()->back()->with('error', 'You already have an ongoing 15-minute break.');
                }
            }

            // Check if the max number of breaks is already reached
            if (count($breaks) >= $maxBreaks) {
                return redirect()->back()->with('error', 'You have reached the maximum number of 15-minute breaks for today.');
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


    public function endBreak15m(Request $request)
    {
        try {
            $user = auth()->user();
            $currentDate = Carbon::now('Asia/Manila')->toDateString();

            // Get the attendance record for today
            $attendance = EmployeeAttendance::where('users_id', $user->id)
                ->where('date', $currentDate)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Attendance record not found for today.'
                ], 404);
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active 15-mins break to end.'
                ], 400);
            }

            // Save the updated breaks array
            $attendance->breaks = json_encode($breaks);
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => '15-minute break ended.',
                'breaks' => $breaks
            ], 200);
        } catch (\Exception $e) {
            Log::error('End Break Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while ending the break.'
            ], 500);
        }
    }




    public function startBreak1h(Request $request)
    {
        //start break 1h

        $now = Carbon::now('Asia/Manila');
        $dateToday = $now->toDateString();
        $dateYesterday = $now->copy()->subDay()->toDateString();

        // Look for attendance records that may have started yesterday and continued into today
        $breakin = EmployeeAttendance::where('users_id', auth()->user()->id)
            ->whereIn('date', [$dateYesterday, $dateToday])
            ->whereNull('breakIn')
            ->latest('date')
            ->first();

        if (!$breakin) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already taken a break or have not timed in yet.'
            ], 400);
        }

        // Check if the user has already timed out
        $timeout = EmployeeAttendance::where('users_id', auth()->user()->id)
            ->whereIn('date', [$dateYesterday, $dateToday])
            ->whereNotNull('timeOut')
            ->latest('date')
            ->first();

        if ($timeout) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already timed out.'
            ], 400);
        }

        $breakInTime = $now;
        $breakEndTime = $breakInTime->copy()->addHour(); // Add 1 hour to calculate break end time

        $breakin->update([
            'breakIn' => $breakInTime->format('h:i:s A'),
            'breakEnd' => $breakEndTime->format('h:i:s A'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'You have successfully started your break.',
            'data' => $breakin
        ], 200);
    }


    public function endBreak1h(Request $request)
    {

        $now = Carbon::now('Asia/Manila');
        $dateToday = $now->toDateString();
        $dateYesterday = $now->copy()->subDay()->toDateString();

        // Check if the user has already timed out
        $timeout = EmployeeAttendance::where('users_id', auth()->user()->id)
            ->whereIn('date', [$dateYesterday, $dateToday])
            ->whereNotNull('timeOut')
            ->latest('date')
            ->first();

        if ($timeout) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already timed out.'
            ], 400);
        }

        $breakout = EmployeeAttendance::where('users_id', auth()->user()->id)
            ->whereIn('date', [$dateYesterday, $dateToday])
            ->whereNotNull('breakIn')
            ->whereNull('breakOut')
            ->latest('date')
            ->first();

        if (!$breakout) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have not taken a break yet or have already returned.'
            ], 400);
        }

        $breakInTime = Carbon::parse($breakout->breakIn);
        $breakOutTime = $now;
        $diffInMinutes = $breakInTime->diffInMinutes($breakOutTime);
        $breakLateFormat = null;

        // Check if break time exceeded 60 minutes
        if ($diffInMinutes > 60) {
            $exceededMinutes = $diffInMinutes - 60;
            $breakLateFormat = CarbonInterval::minutes($exceededMinutes)->cascade()->format('%H:%I:%S');
            $breakout->update([
                'breakLate' => $breakLateFormat,
            ]);
        }

        $breakEndTime = Carbon::parse($breakout->breakEnd);
        if ($breakOutTime->greaterThan($breakEndTime)) {
            $exceededMinutes = $breakOutTime->diffInMinutes($breakEndTime);
            $breakLateFormat = CarbonInterval::minutes($exceededMinutes)->cascade()->format('%H:%I:%S');
            $breakout->update([
                'breakLate' => $breakLateFormat,
            ]);
        }

        $breakout->update([
            'breakOut' => $now->format('h:i:s A'),
        ]);

        $message = 'Welcome Back!';
        if ($breakLateFormat) {
            $message .= ' You were late by ' . $breakLateFormat . ' (HH:MM:SS).';
        }



        return response()->json([
            'status' => 'success',
            'message' => '1-hour break ended successfully.',
            'data' => $breakout
        ], 200);

    }




}
