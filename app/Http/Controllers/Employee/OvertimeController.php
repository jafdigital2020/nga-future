<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
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
            // Split the total_hours into hours, minutes, and seconds
            list($hours, $minutes, $seconds) = explode(':', $overtime->total_hours);
            
            // Convert to decimal hours (including half-hour increments)
            $totalHours = (int)$hours + ((int)$minutes / 60);
            
            // Return the decimal representation, rounding up if minutes are >= 30
            return round($totalHours * 2) / 2; // Round to nearest 0.5
        });
        // Total Approved OT
        $totalRequestsThisMonth = OvertimeRequest::where('users_id', $user)
        ->where('status', 'Approved')
        ->whereMonth('date', Carbon::now()->month)
        ->count(); 

        return view('emp.overtime.index', compact('overtime', 'pendingCount', 'rejectedCount', 'approvedHours', 'totalRequestsThisMonth'));
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
            ], [
                'date.after' => 'The date must be at least one day ahead.',
                'date.required' => 'Please select a date for the overtime request.',
                'date.date' => 'Please provide a valid date.',
                'start_time.required' => 'Please provide a start time for the overtime request.',
                'end_time.required' => 'Please provide an end time for the overtime request.',
                'end_time.after' => 'End time must be after the start time.',
            ]);
    
            // Check for existing overtime requests on the same date and time
            $existingRequest = OvertimeRequest::where('users_id', Auth::user()->id)
                ->where('date', $request->input('date'))
                ->where('start_time', $request->input('start_time'))
                ->where('end_time', $request->input('end_time'))
                ->first();
    
            if ($existingRequest) {
                return redirect()->back()->withErrors(['date' => 'An overtime request for this date and time already exists.']);
            }
    
            // Save the overtime request
            $overtime = new OvertimeRequest();
            $overtime->users_id = Auth::user()->id;
            $overtime->date = $request->input('date');
            $overtime->start_time = $request->input('start_time');
            $overtime->end_time = $request->input('end_time');
            $overtime->total_hours = $request->input('total_hours');
            $overtime->reason = $request->input('reason');
            $overtime->save();
    
            // Define $user for notifications
            $user = Auth::user();
    
            // Get the supervisor for the user's department
            $supervisor = $user->supervisor;
        
            // Get all HR users
            $hrUsers = User::where('role_as', User::ROLE_HR)->get();
        
            // Get all Admin users
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
            return redirect()->back()->with('success', 'Submitted Successfully');
            
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
