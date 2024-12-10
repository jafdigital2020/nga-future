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

    public function getattendance(Request $request)
    {

        $currentDate = Carbon::now('Asia/Manila')->toDateString();

        $userId = $request->input('user_id');
        $data = EmployeeAttendance::where('users_id', $userId)
            ->whereDate('created_at', $currentDate)
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





    // ** TIME OUT ** //
    public function clockout(Request $request)
    {
        try {

            $now = Carbon::now('Asia/Manila');
            $dateToday = $now->toDateString();
            $dateYesterday = $now->copy()->subDay()->toDateString();


            $timeIn = auth()->user()->employeeAttendance()
                ->where(function ($query) use ($dateToday, $dateYesterday) {
                    $query->whereDate('date', $dateToday)
                        ->orWhereDate('date', $dateYesterday);
                })
                ->whereNull('timeOut')
                ->latest('timeIn')
                ->first();

            if (!$timeIn) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You don't have a time-in record. Please time in first."
                ], 400);
            }

            $request->user()->clockoutt();

            $data = auth()->user()->employeeAttendance()
                ->where(function ($query) use ($dateToday, $dateYesterday) {
                    $query->whereDate('date', $dateToday)
                        ->orWhereDate('date', $dateYesterday);
                })
                ->latest('timeIn')
                ->first();


            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully timed out. Thank you for your hard work!',
                'data' => $data,
            ], 200);

        } catch (Exception $e) {
            Log::error('Check-out Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
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
