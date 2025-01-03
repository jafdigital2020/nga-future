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
use Illuminate\Support\Facades\Http;
use Karmendra\LaravelAgentDetector\AgentDetector;
use App\Notifications\AttendanceSubmissionNotification;

class DashboardController extends Controller
{
  // ** Clock In ** //
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

}
