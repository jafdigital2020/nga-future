<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\OvertimeCredits;
use App\Models\OvertimeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Notifications\OvertimeRequestStatusUpdated;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OvertimeController extends Controller
{
    public function overTime()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
    
        // Get all overtime requests for the current month
        $overtime = OvertimeRequest::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get();
    
        // Count of approved overtime requests for the current month
        $otEmpCount = OvertimeRequest::where('status', 'Approved')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear) // Also check the year
            ->count();
    
        // Calculate total approved overtime hours for the current month
        $otHoursCount = OvertimeRequest::where('status', 'Approved')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get() // Fetch the requests to iterate over
            ->sum(function ($overtime) {
                // Split the total_hours into hours, minutes, and seconds
                list($hours, $minutes, $seconds) = explode(':', $overtime->total_hours);
                
                // Convert to decimal hours (including half-hour increments)
                $totalHours = (int)$hours + ((int)$minutes / 60);
                
                // Return the decimal representation, rounding to the nearest 0.5
                return round($totalHours * 2) / 2; // Round to nearest 0.5
            });
    
        // Count of pending overtime requests for the current month
        $otPendingCount = OvertimeRequest::where('status', 'Pending')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();
    
        // Count of rejected overtime requests for the current month
        $otRejectCount = OvertimeRequest::where('status', 'Rejected')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();
    
        // Return the view with filtered overtime requests and counts for the current month
        return view('admin.overtime.request', compact('overtime', 'otEmpCount', 'otHoursCount', 'otPendingCount', 'otRejectCount'));
    }
    
    public function approveOT($id)
    {
        try {
            $otapprove = OvertimeRequest::findOrFail($id);
            $authUser = Auth::user();
        
            // Update the status and approved_by fields
            $otapprove->status = 'Approved';
            $otapprove->approved_by = $authUser->id;
            $otapprove->save();
    
            // Get the user and their overtime credits
            $user = $otapprove->user; // Assuming you have a relation set up
            $overtimeCredits = OvertimeCredits::where('users_id', $user->id)->first();
    
            if (!$overtimeCredits) {
                return redirect()->back()->with('error', 'Overtime credits not found for this user.');
            }
    
            // Convert total_hours from the request and otCredits to seconds
            $total_hours_seconds = Carbon::createFromFormat('H:i:s', $otapprove->total_hours)->secondsSinceMidnight();
            $otCredits_seconds = Carbon::createFromFormat('H:i:s', $overtimeCredits->otCredits)->secondsSinceMidnight();
    
            // Ensure the user has enough overtime credits
            if ($otCredits_seconds < $total_hours_seconds) {
                return redirect()->back()->with('error', 'User does not have enough overtime credits to cover this request.');
            }
    
            // Deduct the total_hours from the user's otCredits
            $new_otCredits_seconds = $otCredits_seconds - $total_hours_seconds;
            $new_otCredits = gmdate('H:i:s', $new_otCredits_seconds); // Convert seconds back to H:i:s format
    
            // Update the user's overtime credits
            $overtimeCredits->otCredits = $new_otCredits;
            $overtimeCredits->save();
    
            // Notify the user about the approval
            $user->notify(new OvertimeRequestStatusUpdated($otapprove, 'Approved'));
    
            // Success message
            return redirect()->back()->with('success', 'Overtime Approved and credits deducted!');
            
        } catch (ModelNotFoundException $e) {
            // Handle the case where the overtime request is not found
            return redirect()->back()->with('error', 'Overtime request not found.');
        } catch (Exception $e) {
            // Handle any other errors
            return redirect()->back()->with('error', 'An error occurred while approving the overtime request: ' . $e->getMessage());
        }
    }
    

    public function rejectOT($id)
    {
        try {

            $otreject = OvertimeRequest::findOrFail($id);
            $authUser = Auth::user();

            $otreject->status = 'Rejected';
            $otreject->approved_by = $authUser->id;
            $otreject->save();

            // Notify the user about the rejection
            $user = $otreject->user; // Assuming you have a relation set up
            $user->notify(new OvertimeRequestStatusUpdated($otreject, 'Rejected'));

            return redirect()->back()->with('success', 'Overtime Rejected!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the overtime request is not found
            return redirect()->back()->with('error', 'Overtime request not found.');
        } catch (Exception $e) {
            // Handle any other errors
            return redirect()->back()->with('error', 'An error occurred while approving the overtime request: ' . $e->getMessage());
        }
    }
    
}
