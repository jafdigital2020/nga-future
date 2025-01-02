<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceCredit;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceEditHistory;

class RequestController extends Controller
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
            ->whereIn('status_code', ['Pending', 'Approved', 'Declined', 'Pre-Approved'])
            ->get();

        // Pass remaining credits to the view along with other data
        return view('emp.requests.reqattendance', compact('pending', 'pendingCount', 'reqcount', 'decCount', 'remainingCredits'));
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

        $requestedDate = Carbon::parse($request->date);
        $yesterday = Carbon::yesterday();

        if ($requestedDate->lt($yesterday)) {
            return redirect()->back()->with(['error' => 'You can only request attendance from the day before the current date.']);
        }

        $filePath = $request->hasFile('file') ? $request->file('file')->store('attendance_files', 'public') : null;

        try {
            $timeInFormatted = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i:s A');
            $timeOutFormatted = Carbon::createFromFormat('H:i', $request->end_time)->format('h:i:s A');

            // Check for existing attendance
            $existingAttendance = EmployeeAttendance::where('users_id', Auth::id())
                ->where('date', $request->date)
                ->first();

            if ($existingAttendance) {
                // Log changes in the attendance_edit_history table
                AttendanceEditHistory::create([
                    'attendance_id' => $existingAttendance->id,
                    'changes' => json_encode([
                        'timeIn' => $existingAttendance->timeIn,
                        'timeOut' => $existingAttendance->timeOut,
                        'manualTimeTotal' => $existingAttendance->manualTimeTotal,
                        'reason' => $existingAttendance->reason,
                        'status_code' => $existingAttendance->status_code,
                        'image_path' => $existingAttendance->image_path,
                    ]),
                    'edited_by' => Auth::id(),
                ]);

                // Override the existing attendance record
                $existingAttendance->timeIn = $timeInFormatted;
                $existingAttendance->timeOut = $timeOutFormatted;
                $existingAttendance->image_path = $filePath;
                $existingAttendance->reason = $request->reason;
                $existingAttendance->status_code = 'Pending';

                $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
                if ($isManualEntry && $request->total_hours) {
                    $existingAttendance->manualTimeTotal = $request->total_hours;
                } else {
                    $existingAttendance->manualTimeTotal = $this->calculateTotalHours($request->start_time, $request->end_time, $request->break_hours);
                }

                $existingAttendance->save();

                Log::info("Attendance record updated with ID: " . $existingAttendance->id);
                return redirect()->back()->with('success', 'Attendance request sent successfully!');
            } else {
                // Create new attendance record
                $attendance = new EmployeeAttendance();
                $attendance->users_id = Auth::id();
                $attendance->date = $request->date;
                $attendance->timeIn = $timeInFormatted;
                $attendance->timeOut = $timeOutFormatted;
                $attendance->image_path = $filePath;
                $attendance->reason = $request->reason;
                $attendance->status_code = 'Pending';

                $isManualEntry = $request->has('is_manual_entry') && $request->is_manual_entry == 1;
                if ($isManualEntry && $request->total_hours) {
                    $attendance->manualTimeTotal = $request->total_hours;
                } else {
                    $attendance->manualTimeTotal = $this->calculateTotalHours($request->start_time, $request->end_time, $request->break_hours);
                }

                $attendance->save();

                Log::info("Attendance record saved with ID: " . $attendance->id);
                return redirect()->back()->with('success', 'Attendance record saved successfully!');
            }
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

            // If the date field has been changed, check for duplicates
            if ($attendance->date !== $request->datee) {
                $existingAttendance = EmployeeAttendance::where('users_id', Auth::id())
                    ->where('date', $request->datee)
                    ->first();

                if ($existingAttendance) {
                    return redirect()->back()->with(['error' => 'An attendance record with this date already exists.']);
                }
            }

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
            return redirect()->back()->with(['error' => 'Failed to update attendance record. Please try again.']);
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
