<?php

namespace App\Http\Controllers\Employee\api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use App\Http\Controllers\Controller;
use App\Notifications\LeaveRequestNotification;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function getLeaveRequest(Request $request){

        $userId = $request->input('user_id');
        $data = LeaveRequest::where('users_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);

    }


    public function createLeaveRequest(Request $request)
    {
        $userId = $request->input('user_id');
        $leaveTypeId = $request->input('type');
        $requestedDays = $request->input('total_days');
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $today = Carbon::now('Asia/Manila');


        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }


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
            return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
            ], 422);
        }

        // Fetch the selected leave type to check restriction_days
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return response()->json([
            'status' => 'error',
            'message' => 'Leave type not found.'
            ], 404);
        }

        // Check the restriction_days for this leave type
        $restrictionDays = $leaveType->restriction_days;

        // Calculate the minimum allowed start date
        if ($restrictionDays > 0) {
            $minStartDate = Carbon::now('Asia/Manila')->addDays($restrictionDays)->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You can only request this leave type {$restrictionDays} days in advance."
                ], 422);
            }
        } else {
            // If restriction_days is 0, allow requests starting today
            $minStartDate = Carbon::now('Asia/Manila')->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The start date cannot be in the past.'
                ], 422);
            }
        }

        // Validate leave type against user's leave credits
        $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();
        $validLeaveTypes = $leaveCredits->pluck('leave_type_id')->toArray();

        if (!in_array($leaveTypeId, $validLeaveTypes)) {
            return response()->json([
            'status' => 'error',
            'message' => 'Invalid leave type selected'
            ], 422);
        }

        // Check user's leave balance
        $leaveCredit = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
            return response()->json([
            'status' => 'error',
            'message' => 'Insufficient leave balance'
            ], 422);
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
            return response()->json([
            'status' => 'error',
            'message' => 'You already have a leave request within the selected dates'
            ], 422);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Leave Request Sent',
            'data' => $leaveRequest
        ], 200);
    }


    public function updateLeaveRequest(Request $request, $id){
    try {
        // Validate the request inputs
        $request->validate([
            'type' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
            'total_days' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
            'start_date' => 'required|date|after_or_equal:today', // Ensure start date is today or later
            'end_date' => 'required|date|after_or_equal:start_date', // Ensure end date is after the start date
            'reason' => 'nullable|string|max:255', // Optional reason
        ]);

        $leave = LeaveRequest::findOrFail($id);

        // Prevent editing approved leave requests
        if ($leave->status == 'Approved') {
            return response()->json([
            'status' => 'error',
            'message' => 'This leave request has already been approved and cannot be edited.'
            ], 422);
        }

        $userId = $request->input('user_id');
        $leaveTypeId = $request->input('type');
        $requestedDays = $request->input('total_days');
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $today = Carbon::now('Asia/Manila');

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }


        // Fetch the selected leave type to check restriction_days
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return response()->json([
            'status' => 'error',
            'message' => 'Leave type not found.'
            ], 404);
        }

        // Check the restriction_days for this leave type
        $restrictionDays = $leaveType->restriction_days;

        if ($restrictionDays > 0) {
            $minStartDate = $today->addDays($restrictionDays)->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You can only request this leave type {$restrictionDays} days in advance."
                ], 422);
            }
        } else {
            // If restriction_days is 0, allow requests starting today
            $minStartDate = $today->startOfDay();

            if ($startDate->lt($minStartDate)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The start date cannot be in the past.'
                ], 422);
            }
        }

        // Fetch the leave credit record for this user and leave type
        $leaveCredit = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient leave balance for the selected leave type.'
            ], 422);
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
            return response()->json([
                'status' => 'error',
                'message' => 'You already have a leave request within the selected dates.'
            ], 422);
        }

        // Update the leave request details
        $leave->leave_type_id = $leaveTypeId; // Update to the leave type name if needed
        $leave->start_date = $startDate;
        $leave->end_date = $endDate;
        $leave->days = $requestedDays;
        $leave->reason = $request->input('reason');
        $leave->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request updated successfully.',
            'data' => $leave
        ], 200);

    } catch (\Exception $e) {
        // Handle any exceptions that may occur
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the leave request: ' . $e->getMessage()
        ], 500);
    }
    }

    public function deleteLeaveRequest($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status == 'Approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'This leave request has already been approved and cannot be deleted.'
            ], 422);
        }

        $leave->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Leave request deleted successfully.'
        ], 200);
    }


}
