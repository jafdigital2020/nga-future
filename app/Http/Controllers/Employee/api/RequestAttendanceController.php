<?php

namespace App\Http\Controllers\Employee\api;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceCredit;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestAttendanceController extends Controller
{
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

        // Return JSON response with the data
        return response()->json([
            'pending' => $pending,
            'pendingCount' => $pendingCount,
            'reqcount' => $reqcount,
            'decCount' => $decCount,
            'remainingCredits' => $remainingCredits
        ]);
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


    public function storeCertificateAttendance(Request $request)
    {

         // AuthBearer Token Needed
        $user = auth()->user();


        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reason' => 'nullable|string|max:255',
            'total_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        // Ensure that the request date is at most one day before today
        $requestedDate = Carbon::parse($request->date);
        $yesterday = Carbon::yesterday();

        if ($requestedDate->lt($yesterday)) {
            return response()->json(['error' => 'You can only request attendance from the day before the current date.'], 400);
        }

        $filePath = $request->hasFile('file') ? $request->file('file')->store('attendance_files', 'public') : null;

        try {
            // Convert timeIn and timeOut to 12-hour format for database storage
            $timeInFormatted = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i:s A');
            $timeOutFormatted = Carbon::createFromFormat('H:i', $request->end_time)->format('h:i:s A');

            // Check for existing attendance with the same date, timeIn, and timeOut
            $existingAttendance = $user->employeeAttendance()
                ->where('date', $request->date)
                ->first();


            if ($existingAttendance) {
                return response()->json(['error' => 'An attendance record for this date already exists.'], 400);
            }

            // Create new attendance record
            $attendance = new EmployeeAttendance();
            $attendance->users_id = $user->id;
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
            return response()->json([
                'success' => 'Attendance record saved successfully!',
                'attendance' => $attendance
            ], 201);
        } catch (\Exception $e) {
            Log::error("Failed to save attendance: " . $e->getMessage());
            return response()->json(['error' => 'Failed to save attendance record. Please try again.'], 500);
        }
    }


    public function updateCertificateAttendance(Request $request, $id)
    {

        // AuthBearer Token Needed
        $user = auth()->user();


        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reason' => 'nullable|string|max:255',
            'total_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        try {
            $attendance = EmployeeAttendance::findOrFail($id);

            // If the date field has been changed, check for duplicates
            if ($attendance->date !== $request->date) {
                $existingAttendance = $user->employeeAttendance()
                ->where('date', $request->date)
                ->first();


                if ($existingAttendance) {
                    return response()->json(['error' => 'An attendance record with this date already exists.'], 400);
                }
            }

            // Update attendance fields
            $attendance->date = $request->date;
            $attendance->timeIn = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i:s A');
            $attendance->timeOut = Carbon::createFromFormat('H:i', $request->end_time)->format('h:i:s A');
            $attendance->reason = $request->reason;

            // Check if a new file is uploaded
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('attendance_files', 'public');
                $attendance->image_path = $filePath; // Update with new file path
            }

            // Determine if manual entry of total hours is provided

            $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
            if ($isManualEntry && $request->total_hours) {
                $attendance->manualTimeTotal = $request->total_hours; // Set manual value
            } else {
                $attendance->manualTimeTotal = $this->calculateTotalHours($request->start_time, $request->end_time, $request->break_hours);
            }

            // Save the updated attendance record
            $attendance->save();

            Log::info("Attendance record updated with ID: " . $attendance->id);
            return response()->json([
                'success' => 'Attendance record updated successfully!',
                'attendance' => $attendance
            ], 200);
        } catch (\Exception $e) {
            Log::error("Failed to update attendance: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update attendance record. Please try again.' . $e->getMessage()], 500);
        }
    }



    public function deleteCertificateAttendance($id)
    {
        try {
            $attReq = EmployeeAttendance::findOrFail($id);

            $attReq->delete();

            return response()->json(['success' => 'Attendance Request Deleted Successfully']);
        } catch (\Exception $e) {
            Log::error("An error occurred while deleting the attendance request", [
                'error_message' => $e->getMessage(),
                'attendance_id' => $id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the attendance request: ' . $e->getMessage()
            ], 500);
        }
    }


}
