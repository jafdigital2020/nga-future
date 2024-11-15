<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Notifications\LeaveRequestNotification;


class LeaveController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $request = LeaveRequest::where('users_id', $user->id)->with('approver')->get();
        $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();

        $pendingRequest = LeaveRequest::where('users_id', $user->id)
            ->where('status', 'Pending')
            ->count();

        $declineRequest = LeaveRequest::where('users_id', $user->id)
            ->where('status', 'Declined')
            ->count();

        $totalLeaveCredits = $leaveCredits->sum('remaining_credits');

        return view('emp.leave', compact('request', 'user', 'leaveCredits', 'pendingRequest', 'declineRequest', 'totalLeaveCredits'));
    }
    
    // public function storeLeave(Request $request)
    // {
    //     $user = auth()->user();
    //     $leaveTypeId = $request->input('type'); 
    //     $requestedDays = $request->input('total_days');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');
    
    //     // Validation rules
    //     $validator = Validator::make($request->all(), [
    //         'type' => 'required|exists:leave_credits,leave_type_id',
    //         'total_days' => 'required|integer|min:1',
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //         'reason' => 'required|string|max:255',
    //         'attached_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Example file validation
    //     ], [
    //         'type.required' => 'Please select a leave type.',
    //         'type.exists' => 'The selected leave type is invalid.',
    //         'total_days.required' => 'Please specify the total number of days.',
    //         'total_days.integer' => 'Total days must be an integer.',
    //         'total_days.min' => 'Total days must be at least 1.',
    //         'start_date.required' => 'Please select a start date.',
    //         'start_date.date' => 'The start date is not a valid date.',
    //         'start_date.after_or_equal' => 'The start date must be today or a future date.',
    //         'end_date.required' => 'Please select an end date.',
    //         'end_date.date' => 'The end date is not a valid date.',
    //         'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
    //         'reason.required' => 'Please provide a reason for your leave request.',
    //         'reason.max' => 'The reason may not be greater than 255 characters.',
    //         'attached_file.file' => 'The attachment must be a file.',
    //         'attached_file.mimes' => 'The attachment must be a file of type: pdf, jpg, jpeg, png.',
    //         'attached_file.max' => 'The attachment may not be greater than 2MB.',
    //     ]);
    
    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         // Get all error messages as a single string
    //         $errorMessages = implode('<br>', $validator->errors()->all());
    
    //         return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
    //     }
    
    //     // Fetch the user's leave credits
    //     $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();
    
    //     // Create an array of valid leave type IDs based on the user's leave credits
    //     $validLeaveTypes = $leaveCredits->pluck('leave_type_id')->toArray();
    
    //     // Check if the leave type is valid
    //     if (!in_array($leaveTypeId, $validLeaveTypes)) {
    //         Alert::error('Invalid leave type selected');
    //         return redirect()->back();
    //     }
    
    //     // Check the user's leave balance for the selected leave type
    //     $leaveCredit = LeaveCredit::where('user_id', $user->id)
    //         ->where('leave_type_id', $leaveTypeId)
    //         ->first();
    
    //     if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
    //         Alert::error('Insufficient leave balance');
    //         return redirect()->back();
    //     }
    
    //     // Check for overlapping leave requests
    //     $overlappingLeave = LeaveRequest::where('users_id', $user->id)
    //         ->where(function ($query) use ($startDate, $endDate) {
    //             $query->whereBetween('start_date', [$startDate, $endDate])
    //                 ->orWhereBetween('end_date', [$startDate, $endDate])
    //                 ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
    //                 ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
    //         })
    //         ->exists();
    
    //     if ($overlappingLeave) {
    //         Alert::error('You already have a leave request within the selected dates');
    //         return redirect()->back();
    //     }
    
    //     // Handle file upload
    //     $filePath = null;
    //     if ($request->hasFile('attached_file')) {
    //         $file = $request->file('attached_file');
    //         $filePath = $file->store('leave_attachments', 'public'); // Store in 'storage/app/public/leave_attachments'
    //     }
    
    //     // Save leave request
    //     $leaveRequest = new LeaveRequest();
    //     $leaveRequest->users_id = $user->id;
    //     $leaveRequest->name = $user->name;
    //     $leaveRequest->start_date = $startDate;
    //     $leaveRequest->end_date = $endDate;
    //     $leaveRequest->days = $requestedDays;
    //     $leaveRequest->reason = $request->input('reason');
    //     $leaveRequest->leave_type_id = $leaveTypeId; // Save the leave type ID
    //     $leaveRequest->status = 'Pending';
    //     $leaveRequest->attached_file = $filePath;  // Store the file path in the attached_file column
    //     $leaveRequest->save();
    
    //     // Notify supervisors and HR
    //     $supervisor = $user->supervisor;
    //     $hrUsers = User::where('role_as', User::ROLE_HR)->get();
    //     $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
    //     $notifiableUsers = collect([$supervisor])
    //         ->merge($hrUsers)
    //         ->merge($adminUsers)
    //         ->unique('id')  // Ensure no user is notified more than once
    //         ->filter();  // Remove any null values (in case supervisor is null)
    
    //     // Notify all unique users
    //     foreach ($notifiableUsers as $notifiableUser) {
    //         $notifiableUser->notify(new LeaveRequestNotification($leaveRequest, $user));
    //     }
    
    //     Alert::success('Leave Request Sent');
    //     return redirect()->back();
    // }

    public function storeLeave(Request $request)
    {
        $user = auth()->user();
        $leaveTypeId = $request->input('type');
        $requestedDays = $request->input('total_days');
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $today = Carbon::now('Asia/Manila');
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:leave_credits,leave_type_id',
            'total_days' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'attached_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'type.required' => 'Please select a leave type.',
            'type.exists' => 'The selected leave type is invalid.',
            'total_days.required' => 'Please specify the total number of days.',
            'total_days.integer' => 'Total days must be an integer.',
            'total_days.min' => 'Total days must be at least 1.',
            'start_date.required' => 'Please select a start date.',
            'start_date.date' => 'The start date is not a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.required' => 'Please select an end date.',
            'end_date.date' => 'The end date is not a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'reason.required' => 'Please provide a reason for your leave request.',
            'reason.max' => 'The reason may not be greater than 255 characters.',
            'attached_file.file' => 'The attachment must be a file.',
            'attached_file.mimes' => 'The attachment must be a file of type: pdf, jpg, jpeg, png.',
            'attached_file.max' => 'The attachment may not be greater than 2MB.',
        ]);
    
        if ($validator->fails()) {
            $errorMessages = implode('<br>', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
        }
    
        // Fetch the selected leave type to check restriction_days
        $leaveType = LeaveType::find($leaveTypeId);
    
        if (!$leaveType) {
            return redirect()->back()->with('error', 'Leave type not found.');
        }
    
        // Check the restriction_days for this leave type
        $restrictionDays = $leaveType->restriction_days;
    
        // Calculate the minimum allowed start date
        if ($restrictionDays > 0) {
            $minStartDate = Carbon::now('Asia/Manila')->addDays($restrictionDays)->startOfDay();
    
            if ($startDate->lt($minStartDate)) {
                return redirect()->back()->with('error', "You can only request this leave type {$restrictionDays} days in advance.");
            }
        } else {
            // If restriction_days is 0, allow requests starting today
            $minStartDate = Carbon::now('Asia/Manila')->startOfDay();
    
            if ($startDate->lt($minStartDate)) {
                return redirect()->back()->with('error', 'The start date cannot be in the past.');
            }
        }
    
        // Validate leave type against user's leave credits
        $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();
        $validLeaveTypes = $leaveCredits->pluck('leave_type_id')->toArray();
    
        if (!in_array($leaveTypeId, $validLeaveTypes)) {
            Alert::error('Invalid leave type selected');
            return redirect()->back();
        }
    
        // Check user's leave balance
        $leaveCredit = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();
    
        if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
            Alert::error('Insufficient leave balance');
            return redirect()->back();
        }
    
        // Check for overlapping leave requests
        $overlappingLeave = LeaveRequest::where('users_id', $user->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
            })
            ->exists();
    
        if ($overlappingLeave) {
            Alert::error('You already have a leave request within the selected dates');
            return redirect()->back();
        }
    
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('attached_file')) {
            $file = $request->file('attached_file');
            $filePath = $file->store('leave_attachments', 'public');
        }
    
        // Save leave request
        $leaveRequest = new LeaveRequest();
        $leaveRequest->users_id = $user->id;
        $leaveRequest->name = $user->name;
        $leaveRequest->start_date = $startDate;
        $leaveRequest->end_date = $endDate;
        $leaveRequest->days = $requestedDays;
        $leaveRequest->reason = $request->input('reason');
        $leaveRequest->leave_type_id = $leaveTypeId;
        $leaveRequest->status = 'Pending';
        $leaveRequest->attached_file = $filePath;
        $leaveRequest->save();
    
        // Notify supervisors and HR
        $supervisor = $user->supervisor;
        $hrUsers = User::where('role_as', User::ROLE_HR)->get();
        $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
        $notifiableUsers = collect([$supervisor])
            ->merge($hrUsers)
            ->merge($adminUsers)
            ->unique('id')
            ->filter();
    
        foreach ($notifiableUsers as $notifiableUser) {
            $notifiableUser->notify(new LeaveRequestNotification($leaveRequest, $user));
        }
    
        Alert::success('Leave Request Sent');
        return redirect()->back();
    }
    
    
//    public function update(Request $request, $id)
//    {
//        try {
//            // Validate the request inputs
//            $request->validate([
//                'typee' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
//                'dayse' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
//                'start_datee' => 'required|date|after_or_equal:today', // Ensure start date is today or later
//                'end_datee' => 'required|date|after_or_equal:start_datee', // Ensure end date is after the start date
//                'reasone' => 'nullable|string|max:255', // Optional reason
//            ]);
   
//            $leave = LeaveRequest::findOrFail($id);
   
//            // Prevent editing approved leave requests
//            if ($leave->status == 'Approved') {
//                Alert::error('This leave request has already been approved and cannot be edited.');
//                return redirect()->back();
//            }
   
//            $user = auth()->user();
//            $leaveTypeId = $request->input('typee');
//            $requestedDays = $request->input('dayse');
//            $startDate = $request->input('start_datee');
//            $endDate = $request->input('end_datee');
   
//            // Fetch the leave credit record for this user and leave type
//            $leaveCredit = LeaveCredit::where('user_id', $user->id)
//                ->where('leave_type_id', $leaveTypeId)
//                ->first();
   
//            if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
//                Alert::error('Insufficient leave balance for the selected leave type');
//                return redirect()->back();
//            }
   
//            // Check for overlapping leave requests
//            $overlappingLeave = LeaveRequest::where('users_id', $user->id)
//                ->where('id', '!=', $id) // Exclude the current leave request
//                ->where(function ($query) use ($startDate, $endDate) {
//                    $query->whereBetween('start_date', [$startDate, $endDate])
//                        ->orWhereBetween('end_date', [$startDate, $endDate])
//                        ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
//                        ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
//                })
//                ->exists();
   
//            if ($overlappingLeave) {
//                Alert::error('You already have a leave request within the selected dates');
//                return redirect()->back();
//            }
   
//            // Update the leave request details
//            $leave->type = $leaveCredit->leaveType->leaveType; // Update to the leave type name if needed
//            $leave->start_date = $startDate;
//            $leave->end_date = $endDate;
//            $leave->days = $requestedDays;
//            $leave->reason = $request->input('reasone');
//            $leave->save();
   
//            Alert::success('Leave request updated successfully');
//            return redirect()->back();
           
//        } catch (\Exception $e) {
//            // Handle any exceptions that may occur
//            Alert::error('An error occurred while updating the leave request: ' . $e->getMessage());
//            return redirect()->back();
//        }
//    }

public function update(Request $request, $id)
{
    try {
        // Validate the request inputs
        $request->validate([
            'typee' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
            'dayse' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
            'start_datee' => 'required|date|after_or_equal:today', // Ensure start date is today or later
            'end_datee' => 'required|date|after_or_equal:start_datee', // Ensure end date is after the start date
            'reasone' => 'nullable|string|max:255', // Optional reason
        ]);

        $leave = LeaveRequest::findOrFail($id);

        // Prevent editing approved leave requests
        if ($leave->status == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be edited.');
            return redirect()->back();
        }

        $user = auth()->user();
        $leaveTypeId = $request->input('typee');
        $requestedDays = $request->input('dayse');
        $startDate = Carbon::parse($request->input('start_datee'));
        $endDate = Carbon::parse($request->input('end_datee'));
        $today = Carbon::now('Asia/Manila');

        // Fetch the leave type to check restriction_days
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            Alert::error('Leave type not found.');
            return redirect()->back();
        }

        // Check the restriction_days for this leave type
        $restrictionDays = $leaveType->restriction_days;

        if ($restrictionDays > 0) {
            $minStartDate = $today->addDays($restrictionDays)->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return redirect()->back()->with('error', "You can only request this leave type {$restrictionDays} days in advance.");
            }
        } else {
            // If restriction_days is 0, allow requests starting today
            $minStartDate = $today->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return redirect()->back()->with('error', 'The start date cannot be in the past.');
            }
        }

        // Fetch the leave credit record for this user and leave type
        $leaveCredit = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
            Alert::error('Insufficient leave balance for the selected leave type.');
            return redirect()->back();
        }

        // Check for overlapping leave requests
        $overlappingLeave = LeaveRequest::where('users_id', $user->id)
            ->where('id', '!=', $id) // Exclude the current leave request
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                    ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
            })
            ->exists();

        if ($overlappingLeave) {
            Alert::error('You already have a leave request within the selected dates.');
            return redirect()->back();
        }

        // Update the leave request details
        $leave->type = $leaveCredit->leaveType->leaveType; // Update to the leave type name if needed
        $leave->start_date = $startDate;
        $leave->end_date = $endDate;
        $leave->days = $requestedDays;
        $leave->reason = $request->input('reasone');
        $leave->save();

        Alert::success('Leave request updated successfully.');
        return redirect()->back();

    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        Alert::error('An error occurred while updating the leave request: ' . $e->getMessage());
        return redirect()->back();
    }
}

   
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be deleted.');
            return redirect()->back();
        }

        $leave->delete();

        Alert::success('Leave request deleted successfully');
        return redirect()->back();
    }
}
