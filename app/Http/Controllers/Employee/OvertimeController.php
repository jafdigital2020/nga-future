<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OvertimeCredits;
use App\Models\OvertimeRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;
use App\Notifications\OvertimeRequestSubmitted;

class OvertimeController extends Controller
{
    public function overtimeIndex()
    {
        $user= Auth::user()->id;
        $otCredits = OvertimeCredits::where('users_id', $user)->first();
        $overtime = OvertimeRequest::where('users_id', $user)->get();
        // Pending Count
        $pendingCount = OvertimeRequest::where('users_id', $user)->where('status', 'Pending')->count();
        // Rejected Count
        $rejectedCount = OvertimeRequest::where('users_id', $user)->where('status', 'Rejected')->count();
        // Approved Total Hours for the Month
        $approvedHours = OvertimeRequest::where('users_id', $user)
        ->where('status', 'Approved')
        ->whereMonth('date', Carbon::now()->month)
        ->get()
        ->sum(function ($overtime) {
            list($hours, $minutes, $seconds) = explode(':', $overtime->total_hours);

            $totalHours = (int)$hours + ((int)$minutes / 60);
            

            return round($totalHours * 2) / 2; 
        });

        if ($otCredits) {
            $timeParts = explode(':', $otCredits->otCredits);
            $hours = (int)$timeParts[0];
            $minutes = (int)$timeParts[1];
        
            // Convert minutes to decimal
            $decimalHours = $hours + ($minutes / 60);
        } else {
            $decimalHours = 0; 
        }
  
     
        $totalRequestsThisMonth = OvertimeRequest::where('users_id', $user)
        ->where('status', 'Approved')
        ->whereMonth('date', Carbon::now()->month)
        ->count(); 

        return view('emp.overtime.index', compact('overtime', 'pendingCount', 'rejectedCount', 'approvedHours', 'totalRequestsThisMonth', 'otCredits', 'decimalHours'));
    }

    public function overtimeRequest(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'date' => 'required|date|after:today',
                'reason' => 'required|string',
                'start_time' => 'required|date_format:H:i', 
                'end_time' => 'required|date_format:H:i|after:start_time', 
                'total_hours' => 'required|date_format:H:i:s', // Validate total_hours as time
            ], [
                'date.after' => 'The date must be at least one day ahead.',
                'date.required' => 'Please select a date for the overtime request.',
                'date.date' => 'Please provide a valid date.',
                'start_time.required' => 'Please provide a start time for the overtime request.',
                'end_time.required' => 'Please provide an end time for the overtime request.',
                'end_time.after' => 'End time must be after the start time.',
                'total_hours.required' => 'Please provide the total overtime hours.',
                'total_hours.date_format' => 'Please provide the total overtime in the format HH:MM:SS.',
            ]);
    
            // Get user's overtime credits in H:i:s format
            $user = Auth::user();
            $overtimeCredits = OvertimeCredits::where('users_id', $user->id)->first();
    
            if (!$overtimeCredits || $overtimeCredits->otCredits == '00:00:00') {
                return redirect()->back()->withErrors(['overtime_credits' => 'You have no overtime credits remaining.']);
            }
    
            // Convert total_hours and overtimeCredits to seconds for comparison
            $total_hours_seconds = Carbon::createFromFormat('H:i:s', $request->input('total_hours'))->secondsSinceMidnight();
            $otCredits_seconds = Carbon::createFromFormat('H:i:s', $overtimeCredits->otCredits)->secondsSinceMidnight();
    
            // Check if user has enough credits
            if ($otCredits_seconds < $total_hours_seconds) {
                return redirect()->back()->withErrors(['overtime_credits' => 'You do not have enough overtime credits to cover this request.']);
            }
    
            // Check for existing overtime requests on the same date and time
            $existingRequest = OvertimeRequest::where('users_id', $user->id)
                ->where('date', $request->input('date'))
                ->where('start_time', $request->input('start_time'))
                ->where('end_time', $request->input('end_time'))
                ->first();
    
            if ($existingRequest) {
                return redirect()->back()->withErrors(['date' => 'An overtime request for this date and time already exists.']);
            }
    
            // Save the overtime request
            $overtime = new OvertimeRequest();
            $overtime->users_id = $user->id;
            $overtime->date = $request->input('date');
            $overtime->start_time = $request->input('start_time');
            $overtime->end_time = $request->input('end_time');
            $overtime->total_hours = $request->input('total_hours'); // Save total_hours in H:i:s format
            $overtime->reason = $request->input('reason');
            $overtime->save();
    
            // Notify supervisors, HR, and Admin users
            $supervisor = $user->supervisor;
            $hrUsers = User::where('role_as', User::ROLE_HR)->get();
            $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
            $notifiableUsers = collect([$supervisor])
                ->merge($hrUsers)
                ->merge($adminUsers)
                ->unique('id')  // Ensure no user is notified more than once
                ->filter();  // Remove any null values (in case supervisor is null)
    
            foreach ($notifiableUsers as $notifiableUser) {
                $notifiableUser->notify(new OvertimeRequestSubmitted($overtime, $user));
            }
    
            // Success message
            return redirect()->back()->with('success', 'Overtime request submitted successfully.');
            
        } catch (ValidationException $e) {
            // Handle validation errors with SweetAlert
            foreach ($e->validator->errors()->all() as $error) {
                Alert::error('Validation Error', $error);
            }
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            Alert::error('An error occurred while submitting your overtime request.');
            return redirect()->back();
        }
    }
    

    public function updateOT(Request $request, $id)
    {
        try {
            // Find the existing overtime request
            $overtime = OvertimeRequest::findOrFail($id);
    
            // Validate the request
            $validated = $request->validate([
                'datee' => 'required|date|after:today',
                'start_timee' => 'required|date_format:H:i', // Assuming start_time is in HH:mm format
                'end_timee' => 'required|date_format:H:i|after:start_timee', // Ensure end_time is after start_time
            ], [
                'datee.after' => 'The date must be at least one day ahead.',
                'datee.required' => 'Please select a date for the overtime request.',
                'datee.date' => 'Please provide a valid date.',
                'start_timee.required' => 'Please provide a start time for the overtime request.',
                'end_timee.required' => 'Please provide an end time for the overtime request.',
                'end_timee.after' => 'End time must be after the start time.',
            ]);
    
            // Check for existing overtime requests on the same date and time, excluding the current request
            $existingRequest = OvertimeRequest::where('users_id', Auth::user()->id)
                ->where('date', $request->input('datee'))
                ->where('start_time', $request->input('start_timee'))
                ->where('end_time', $request->input('end_timee'))
                ->where('id', '!=', $id) // Exclude the current request
                ->first();
    
            if ($existingRequest) {
                return redirect()->back()->withErrors(['datee' => 'An overtime request for this date and time already exists.']);
            }
    
            // Update the overtime request
            $overtime->date = $request->input('datee');
            $overtime->start_time = $request->input('start_timee');
            $overtime->end_time = $request->input('end_timee'); 
            $overtime->total_hours = $request->input('total_hourse');
            $overtime->reason = $request->input('reasone');
    
            // Save the updated overtime request
            $overtime->save();
    
            return redirect()->back()->with('success', 'OT request updated successfully.');
        } catch (ValidationException $e) {
            // Handle validation errors with SweetAlert
            foreach ($e->validator->errors()->all() as $error) {
                Alert::error('Validation Error', $error);
            }
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            // Handle general errors
            return redirect()->back()->with('error', 'An error occurred while updating your overtime request: ' . $e->getMessage());
        }
    }
    

    public function deleteOT($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->status == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be deleted.');
            return redirect()->back();
        }

        $overtime->delete();

        return redirect()->back()->with('success', 'Deleted Successfully');
    }

}
