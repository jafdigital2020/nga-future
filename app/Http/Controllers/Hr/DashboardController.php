<?php

namespace App\Http\Controllers\Hr;

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
use Illuminate\Support\Facades\Http;
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
                ->where(function ($query) use ($startDate, $endDate) {
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

        $overtime = OvertimeRequest::where('users_id', $user->id)->get();
        // Get the latest overtime request for the user
        $latestOvertime = OvertimeRequest::where('users_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();


        // Get the nearest holiday after or equal to today
        $nearestHoliday = SettingsHoliday::where('holidayDate', '>=', $today)
            ->orderBy('holidayDate', 'asc')
            ->first();

        return view('hr.dashboard', compact(
            'attendanceApproved',
            'policies',
            'user',
            'empatt',
            'all',
            'total',
            'latest',
            'latestOvertime',
            'filteredData',
            'supervisor',
            'record',
            'salrecord',
            'leaveApproved',
            'leavePending',
            'nearestHoliday',
            'latestAnnouncement',
            'totalLeaveCredits',
            'breaks',
            'maxBreaks'
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
            ->where(function ($query) use ($startDate, $endDate) {
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

            // Check if user is allowed multiple logins
            $user = auth()->user();
            if (!$user->allow_multiple_login) {
                // Check if the user has already timed in for the day
                $employeeAttendance = $user->employeeAttendance()
                    ->where('date', $currentDate)
                    ->first();

                if ($employeeAttendance) {
                    Log::info("User has already timed in for today.");
                    return response()->json(['status' => 'error', 'message' => 'You have already timed in!']);
                }
            }

            // Get user's shift schedule
            $shiftSchedule = ShiftSchedule::where('users_id', $user->id)
                ->where('date', $currentDate)
                ->first();

            if (!$shiftSchedule) {
                return response()->json(['status' => 'error', 'message' => 'Shift schedule not found for today.']);
            }

            // Determine status and total late based on shift times if not flexible
            $status = 'On Time';
            $totalLate = '00:00:00';
            $timeEnd = null;
            $shiftOver = null;

            if (!$shiftSchedule->isFlexibleTime) {
                $shiftStart = Carbon::parse($shiftSchedule->shiftStart, 'Asia/Manila');
                $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');
                $shiftEnd = Carbon::parse($shiftSchedule->shiftEnd, 'Asia/Manila');

                // If the user is late
                if ($timeIn->gt($lateThreshold)) {
                    $status = 'Late';
                    $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
                    $hours = floor($totalLateInSeconds / 3600);
                    $minutes = floor(($totalLateInSeconds % 3600) / 60);
                    $seconds = $totalLateInSeconds % 60;
                    $totalLate = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                }

                // Calculate timeEnd based on allowedHours
                $allowedHours = Carbon::parse($shiftSchedule->allowedHours)->secondsSinceMidnight();
                $calculatedTimeEnd = $timeIn->copy()->addSeconds($allowedHours)->addHour();

                // If calculated timeEnd exceeds shiftEnd, cap timeEnd to shiftEnd
                if ($calculatedTimeEnd->greaterThan($shiftEnd)) {
                    $timeEnd = $shiftEnd->format('h:i:s A');
                } else {
                    $timeEnd = $calculatedTimeEnd->format('h:i:s A');
                }

                // Set shiftOver to shiftEnd
                $shiftOver = $shiftEnd->format('h:i:s A');
            }

            // Handle device detection
            $userAgent = $request->header('User-Agent');
            $agentDetector = new AgentDetector($userAgent);
            $deviceType = $agentDetector->device();
            $platform = $agentDetector->platform();
            $browser = $agentDetector->browser();
            $deviceInfo = "{$deviceType} ({$platform}, {$browser})";

            // Geolocation data from request
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            Log::info("User location: Latitude = $latitude, Longitude = $longitude");


            // Use a third-party API or validate mock locations (Android only)
            $isMockLocation = $this->isMockLocation($latitude, $longitude);

            if ($isMockLocation) {
                return response()->json(['status' => 'error', 'message' => 'Fake GPS detected.'], 403);
            }

            // Low accuracy check-in with photo upload
            if ($request->has('low_accuracy') && $request->low_accuracy) {
                // Check if an image was uploaded and store it
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('checkin_photos', 'public');
                    Log::info("Image path to be saved: " . $imagePath); // Debugging line
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Image upload is required for low accuracy check-in.']);
                }

                // Complete check-in and store attendance data
                $attendance = auth()->user()->employeeAttendance()->create([
                    'name' => $user->fName . ' ' . $user->lName,
                    'date' => $currentDate,
                    'timeIn' => $timeIn->format('h:i:s A'),
                    'status' => $status,
                    'latitude' => $latitude,
                    'totalLate' => $totalLate,
                    'timeEnd' => $timeEnd,  // Ensure null for flexible time
                    'shiftOver' => $shiftOver,  // Ensure null for flexible time
                    'device' => $deviceInfo,
                    'longitude' => $longitude,
                    'location' => $request->location,
                    'image_path' => $imagePath, // Save uploaded image path
                    'status_code' => 'Active',
                ]);

                Log::info("Attendance saved with ID: " . $attendance->id); // Confirm save with ID

                return response()->json(['status' => 'success', 'message' => 'Checked in successfully with photo!']);
            }

            // Retrieve all geofences assigned to the user
            $userGeofences = UserGeofence::where('user_id', $user->id)
                ->whereHas('geofenceSetting', function ($query) {
                    $query->whereIn('status', ['Active', 'Never Expired']);
                })
                ->with('geofenceSetting')
                ->get();

            Log::info("Fetched geofences: " . json_encode($userGeofences->toArray()));

            // Check for expired geofences
            $expiredGeofence = UserGeofence::where('user_id', $user->id)
                ->whereHas('geofenceSetting', function ($query) {
                    $query->where('status', 'Expired');
                })
                ->exists();

            if ($expiredGeofence) {
                Log::error("The geofence assigned to the user has expired.");
                return response()->json(['status' => 'error', 'message' => 'The geofence that is assigned to you has expired. Please contact your manager or admin.']);
            }

            $isWithinGeofence = false;
            $isWithinTempRadius = false;
            $tempRadius = 2000; // Temporary threshold in meters

            foreach ($userGeofences as $userGeofence) {
                if ($userGeofence->geofenceSetting) {
                    $geofence = $userGeofence->geofenceSetting;
                    $distance = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $geofence->latitude,
                        $geofence->longitude
                    );
                    Log::info("Distance to geofence {$geofence->fencing_name}: $distance meters");

                    if ($distance <= $geofence->fencing_radius) {
                        $isWithinGeofence = true;
                        break;
                    }

                    if ($distance <= $tempRadius) {
                        $isWithinTempRadius = true;
                    }
                }
            }

            if ($isWithinGeofence || $userGeofences->isEmpty()) {
                Log::info("User is within a geofence or no geofence assigned, proceeding to check-in.");

                if (!$shiftSchedule->isFlexibleTime) {
                    $lateThreshold = Carbon::parse($shiftSchedule->lateThreshold, 'Asia/Manila');
                    if ($timeIn->greaterThan($lateThreshold)) {
                        $status = 'Late';
                        $totalLateInSeconds = $timeIn->diffInSeconds($lateThreshold);
                        $totalLate = gmdate("H:i:s", $totalLateInSeconds);
                    }
                }

                // Save attendance record
                auth()->user()->employeeAttendance()->create([
                    'name' => $user->fName . ' ' . $user->lName,
                    'date' => $currentDate,
                    'timeIn' => $timeIn->format('h:i:s A'),
                    'status' => $status,
                    'totalLate' => $totalLate,
                    'timeEnd' => $timeEnd,  // Ensure null for flexible time
                    'shiftOver' => $shiftOver,  // Ensure null for flexible time
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'device' => $deviceInfo,
                    'location' => $request->location,
                    'status_code' => 'Active',
                ]);

                Log::info("Check-in successful.");
                return response()->json(['status' => 'success', 'message' => 'Checked in successfully!']);
            }

            if ($isWithinTempRadius && !$isWithinGeofence) {
                Log::info("User is outside geofence but within temporary radius.");
                return response()->json(['status' => 'low_accuracy', 'message' => 'Low accuracy, please upload a photo to complete check-in.']);
            }

            Log::info("User is outside all geofences and temporary radius.");
            return response()->json(['status' => 'error', 'message' => 'You are outside all assigned geofence areas.']);
        } catch (Exception $e) {
            Log::error('Check-in Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred. Please try again later.']);
        }
    }

    // ** CLOCK OUT ** //
    public function update(Request $request)
    {
        try {
            Log::info('Incoming clock-out request:', $request->all());

            $currentDate = Carbon::now('Asia/Manila')->toDateString();
            $timeOut = Carbon::now('Asia/Manila');

            Log::info('Retrieving attendance record.', ['date' => $currentDate]);

            // Determine attendance record based on allow_multiple_login

            $user = auth()->user();
            if ($user->allow_multiple_login) {
                // Select the first attendance record for the day
                $attendance = $user->employeeAttendance()
                    ->whereDate('date', $currentDate)
                    ->orderBy('timeIn', 'asc') // Earliest timeIn for the day
                    ->first();
            } else {
                // Select the attendance record with null timeOut and the latest timeIn
                $attendance = $user->employeeAttendance()
                    ->where(function ($query) use ($currentDate) {
                        $query->whereDate('date', $currentDate)
                            ->orWhereDate('date', Carbon::yesterday()->toDateString());
                    })
                    ->whereNull('timeOut')
                    ->whereNotNull('timeIn')
                    ->orderBy('timeIn', 'desc') // Prioritize the most recent timeIn
                    ->first(); // Ensure you're retrieving only one record
            }

            if (!$attendance) {
                Log::warning('No clock-in record found.', ['user_id' => $user->id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No clock-in record found for today. Please clock in first.',
                ]);
            }

            $existingClockOut = $user->employeeAttendance()
                ->whereDate('date', $currentDate)
                ->whereNotNull('timeOut')
                ->first();

            if ($existingClockOut) {
                Log::warning('User has already clocked out today.', ['user_id' => $user->id]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already clocked out today.',
                ]);
            }

            $noBreakOut = $user->employeeAttendance()
                ->whereDate('date', $currentDate)
                ->whereNotNull('breakIn')
                ->whereNull('breakOut')
                ->first();

            if ($noBreakOut) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please end your break first.'
                ]);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            Log::info('User geolocation received.', ['latitude' => $latitude, 'longitude' => $longitude]);

            $userGeofences = UserGeofence::where('user_id', $user->id)
                ->with('geofenceSetting')
                ->get();

            $isWithinGeofence = false;
            $tempRadius = 2000; // Temporary radius for low accuracy

            foreach ($userGeofences as $userGeofence) {
                if ($userGeofence->geofenceSetting) {
                    $geofence = $userGeofence->geofenceSetting;
                    $distance = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $geofence->latitude,
                        $geofence->longitude
                    );

                    Log::info('Geofence distance calculated.', [
                        'geofence_name' => $geofence->fencing_name,
                        'distance' => $distance
                    ]);

                    if ($distance <= $geofence->fencing_radius) {
                        $isWithinGeofence = true;
                        break;
                    }
                }
            }

            if (!$isWithinGeofence) {
                if ($this->isWithinTemporaryRadius($latitude, $longitude, $userGeofences, $tempRadius)) {
                    if ($request->hasFile('image')) {
                        Log::info('Low accuracy clock-out request with image detected.');

                        $image = $request->file('image');
                        try {
                            $imagePath = $image->store('clockout_photos', 'public');
                            Log::info('Clock-out photo uploaded successfully.', ['path' => $imagePath]);

                            $attendance->update([
                                'timeOut' => $timeOut->format('h:i:s A'),
                                'clock_out_latitude' => $latitude,
                                'clock_out_longitude' => $longitude,
                                'clock_out_image_path' => $imagePath,
                            ]);

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Clock-out successful with photo!'
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Image storage failed.', ['error' => $e->getMessage()]);
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Failed to save image. Please try again.',
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => 'low_accuracy',
                        'message' => 'Low accuracy detected. Please capture a photo to complete clock-out.',
                    ]);
                }

                Log::warning('User outside assigned geofence.', [
                    'user_id' => $user->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'You are outside your assigned geofence. Clock-out failed.',
                ]);
            }

            $timeIn = Carbon::parse($attendance->timeIn, 'Asia/Manila');
            $totalWorkSeconds = $timeOut->diffInSeconds($timeIn);
            $totalWorkHours = gmdate('H:i:s', $totalWorkSeconds);

            $nightShiftStart = Carbon::parse('22:00:00', 'Asia/Manila');
            $nightShiftEnd = Carbon::parse('06:00:00', 'Asia/Manila')->addDay();
            $nightDiffSeconds = 0;

            if ($timeIn <= $nightShiftEnd && $timeOut >= $nightShiftStart) {
                $nightStart = $timeIn->max($nightShiftStart);
                $nightEnd = $timeOut->min($nightShiftEnd);
                $nightDiffSeconds = $nightEnd->diffInSeconds($nightStart);
            }

            $nightDiffHours = gmdate('H:i:s', $nightDiffSeconds);

            $attendance->update([
                'timeOut' => $timeOut->format('h:i:s A'),
                'clock_out_latitude' => $latitude,
                'clock_out_longitude' => $longitude,
                'totalWorkHours' => $totalWorkHours,
                'night_diff_hours' => $nightDiffHours,
            ]);

            Log::info('Clock-out successful.', [
                'user_id' => $user->id,
                'total_work_hours' => $totalWorkHours,
                'night_diff_hours' => $nightDiffHours
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Clock-out successful!',
            ]);
        } catch (\Exception $e) {
            Log::error('Clock-Out Error:', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
            ]);
        }
    }


    /*** Check if the user is within a temporary radius of any geofence. ***/
    private function isWithinTemporaryRadius($latitude, $longitude, $userGeofences, $tempRadius)
    {
        foreach ($userGeofences as $userGeofence) {
            if ($userGeofence->geofenceSetting) {
                $geofence = $userGeofence->geofenceSetting;
                $distance = $this->calculateDistance(
                    $latitude,
                    $longitude,
                    $geofence->latitude,
                    $geofence->longitude
                );

                if ($distance <= $tempRadius) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isMockLocation($latitude, $longitude)
    {
        // Always trust Google Maps' GPS data as it is accurate
        return false; // Assume location is valid if coming from Google Maps
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
            'paid_leave' => 'required|numeric',
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


    public function startOvertime(Request $request)
    {
        try {
            $user = Auth::user();
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
                return redirect()->back()->with('error', 'You cannot start overtime after clocking out.');
            }
    
            // Check if there is already an ongoing overtime request
            $overtime = OvertimeRequest::where('users_id', $user->id)
                ->where('date', $currentDate)
                ->first();
    
            if ($overtime) {
                // If overtime already exists and has started, show an error
                if ($overtime->start_time !== null) {
                    return redirect()->back()->with('error', 'You already have an ongoing overtime or already started overtime for this day.');
                }
            }
    
            // Save the overtime request
            $overtime = new OvertimeRequest();
            $overtime->users_id = $user->id;
            $overtime->date = $currentDate;
            $overtime->start_time = Carbon::now('Asia/Manila')->format('h:i:s A');
            $overtime->end_time = null;
            $overtime->total_hours = null;
            $overtime->reason = null;
            $overtime->save();
    
            return redirect()->back()->with('success', 'Overtime started successfully.');
    
        } catch (\Exception $e) {
            Log::error('Start Overtime Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while starting the overtime.');
        }
    }

    public function endOvertime(Request $request)
    {
        try {
            $user = Auth::user();

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
                return redirect()->back()->with('error', 'You cannot end overtime after clocking out.');
            }

            $overtime = OvertimeRequest::where('users_id', $user->id)
                ->where('date', $currentDate)
                ->first();


            if ($overtime->end_time !== null) {
                return redirect()->back()->with('error', 'You have already ended overtime for this day.');
            }

            // Set the end time for the overtime request
            $overtime->end_time = Carbon::now('Asia/Manila')->format('h:i:s A');

            // Calculate the total hours for the overtime request
            $start = Carbon::parse($overtime->start_time, 'Asia/Manila');
            $end = Carbon::parse($overtime->end_time, 'Asia/Manila');
            $totalHours = $end->diff($start)->format('%H:%I:%S');

            $overtime->total_hours = $totalHours;

            $overtime->save();

            return redirect()->back()->with('success', 'Overtime ended successfully.');
        } catch (\Exception $e) {
            Log::error('End Overtime Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while ending the overtime.');
        }
    }
}
