<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceCredit;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RequestController extends Controller
{
    public function attendance()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $reqcount = EmployeeAttendance::whereIn('status_code', ['Pending', 'Approved', 'Pre-Approved'])
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $pendingCount = EmployeeAttendance::where('status_code', 'Pending')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $decCount = EmployeeAttendance::where('status_code', 'Declined')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $pending = EmployeeAttendance::whereIn('status_code', ['Pending', 'Approved', 'Declined', 'Pre-Approved'])->get();

        return view('admin.request.attendancerequest', compact('pending', 'pendingCount', 'reqcount', 'decCount'));
    }

    public function storeCertificateAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reason' => 'nullable|string|max:255',
            'total_hours' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        $filePath = $request->hasFile('file') ? $request->file('file')->store('attendance_files', 'public') : null;

        try {
            // Convert timeIn and timeOut to 12-hour format for database storage
            $timeInFormatted = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i:s A');
            $timeOutFormatted = Carbon::createFromFormat('H:i', $request->end_time)->format('h:i:s A');

            // Check for existing attendance with the same date, timeIn, and timeOut
            $existingAttendance = EmployeeAttendance::where('users_id', Auth::id())
                ->where('date', $request->date)
                ->where('timeIn', $timeInFormatted)
                ->where('timeOut', $timeOutFormatted)
                ->first();

            if ($existingAttendance) {
                return redirect()->back()->with(['error' => 'An attendance record with the same date and time already exists.']);
            }

            // Create new attendance record
            $attendance = new EmployeeAttendance();
            $attendance->users_id = Auth::id();
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
            return redirect()->back()->with('success', 'Attendance record saved successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to save attendance: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to save attendance record. Please try again.']);
        }
    }

    public function updateCertificateAttendance(Request $request, $id)
    {
        $request->validate([
            'datee' => 'required|date',
            'start_timee' => 'required|date_format:H:i',
            'end_timee' => 'required|date_format:H:i|after:start_timee',
            'filee' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'reasone' => 'nullable|string|max:255',
            'total_hourse' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/'
        ]);

        try {
            $attendance = EmployeeAttendance::findOrFail($id);

            // Update attendance fields
            $attendance->date = $request->datee;
            $attendance->timeIn = Carbon::createFromFormat('H:i', $request->start_timee)->format('h:i:s A');
            $attendance->timeOut = Carbon::createFromFormat('H:i', $request->end_timee)->format('h:i:s A');
            $attendance->reason = $request->reasone;

            // Check if a new file is uploaded
            if ($request->hasFile('filee')) {
                $filePath = $request->file('filee')->store('attendance_files', 'public');
                $attendance->image_path = $filePath; // Update with new file path
            }

            // Determine if manual entry of total hours is provided
            $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
            if ($isManualEntry && $request->total_hourse) {
                $attendance->manualTimeTotal = $request->total_hourse; // Set manual value
            } else {
                // Calculate time total dynamically if no manual entry is provided
                $attendance->manualTimeTotal = $this->calculateTotalHours(
                    $request->start_timee,
                    $request->end_timee,
                );
            }

            // Save the updated attendance record
            $attendance->save();

            Log::info("Attendance record updated with ID: " . $attendance->id);
            return redirect()->back()->with('success', 'Attendance record updated successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to update attendance: " . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to update attendance record. Please try again.']);
        }
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

    public function approveAttendance($id)
    {
        try {
            // Find the attendance record
            $attendance = EmployeeAttendance::findOrFail($id);
            $authUser = Auth::user();

            // Update status and approved_by fields
            $attendance->status_code = 'Approved';
            $attendance->approved_by = $authUser->id; // Ensure `approved_by` field exists in your table
            $attendance->save();

            // Retrieve the user associated with the attendance record
            $user = $attendance->user;
            $attendanceCredits = AttendanceCredit::where('user_id', $user->id)->first();

            if (!$attendanceCredits) {
                return redirect()->back()->with('error', 'Attendance credits not found for this user.');
            }

            // Check if user has enough attendance credits (1 credit per approval)
            if ($attendanceCredits->attendanceCredits < 1) {
                return redirect()->back()->with('error', 'User does not have enough attendance credits to cover this attendance.');
            }

            // Deduct 1 attendance credit
            $attendanceCredits->attendanceCredits -= 1;
            $attendanceCredits->save();


            // Success message
            return redirect()->back()->with('success', 'Attendance approved and one attendance credit deducted successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where attendance record is not found
            return redirect()->back()->with('error', 'Attendance record not found.');
        } catch (\Exception $e) {
            // Handle any other errors
            return redirect()->back()->with('warning', 'An error occurred while approving the attendance: ' . $e->getMessage());
        }
    }

    public function declineAttendance($id)
    {
        try {
            $attendance = EmployeeAttendance::findOrFail($id);
            $authUser = Auth::user();

            // Check if attendance was previously approved
            if ($attendance->status_code === 'Approved') {
                $attendanceCredits = AttendanceCredit::where('user_id', $attendance->user->id)->first();

                if (!$attendanceCredits) {
                    return redirect()->back()->with('error', 'No attendance credits for this employee.');
                }

                // Restore one attendance credit
                $attendanceCredits->attendanceCredits += 1;
                $attendanceCredits->save();
            }

            // Update status to Declined
            $attendance->status_code = 'Declined';
            $attendance->approved_by = $authUser->id;
            $attendance->save();

            return redirect()->back()->with('success', 'Request Declined, and attendance credit restored if previously deducted.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Request not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while declining the attendance: ' . $e->getMessage());
        }
    }


    public function deleteCertificateAttendance($id)
    {
        $attReq = EmployeeAttendance::findOrFail($id);

        // if ($overtime->status == 'Approved') {
        //     Alert::error('This leave request has already been approved and cannot be deleted.');
        //     return redirect()->back();
        // }

        $attReq->delete();

        return redirect()->back()->with('warning', 'Deleted Successfully!');
    }
}
