<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Notifications\LeaveRequestNotification;
use App\Notifications\RequestApprovedNotification;


class LeaveController extends Controller
{
   
    public function index(Request $request)
    {
        $user = auth()->user();
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $departments = User::distinct()->pluck('department'); 
        $leaveTypes = LeaveType::with('leaveCredits')->get();

        
        // Base query: fetch leave requests for subordinates only
        $data = LeaveRequest::whereHas('user', function ($query) use ($user) {
            $query->where('reporting_to', $user->id); // Only subordinates of this manager
        });
    
        // Count pending leave requests
        $pendingCount = (clone $data)->where('status', 'Pending')->count();
    
        // Apply search filters independently
        if (!empty($name)) {
            $data->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            });
        }
    
        if (!empty($type)) {
            $data->where('type', 'like', "%$type%");
        }
    
        if (!empty($status)) {
            $data->where('status', 'like', "%$status%");
        }
    
        // Filter by department
        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }
    
        // Apply date range filter on start_date
        if (!empty($startDate) && !empty($endDate)) {
            $data->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            });
        }
    
        // Fetch filtered leave requests
        $leaveRequests = $data->get();
    
        // Get IDs of subordinates for login and leave counting
        $subordinates = $user->subordinates->pluck('id');
    
        // Count today's logins for subordinates
        $todayLoginCount = $subordinates->isNotEmpty() 
            ? EmployeeAttendance::query()
                ->whereIn('users_id', $subordinates)
                ->whereDate('date', today())
                ->distinct('users_id')
                ->count('users_id')
            : 0;
    
        // Count today's approved leave requests by paid and unpaid types
        $leaveTypesPaidCountToday = LeaveRequest::whereHas('leaveType', function($query) {
                $query->where('is_paid', true);
            })
            ->where('status', 'Approved')
            ->whereHas('user', function($query) use ($subordinates) {
                $query->whereIn('id', $subordinates);
            })
            ->where(function($query) {
                $query->whereDate('start_date', today())
                      ->orWhereDate('end_date', today())
                      ->orWhere(function($query) {
                          $query->where('start_date', '<=', today())
                                ->where('end_date', '>=', today());
                      });
            })
            ->count();
    
        $leaveTypesUnpaidCountToday = LeaveRequest::whereHas('leaveType', function($query) {
                $query->where('is_paid', false);
            })
            ->where('status', 'Approved')
            ->whereHas('user', function($query) use ($subordinates) {
                $query->whereIn('id', $subordinates);
            })
            ->where(function($query) {
                $query->whereDate('start_date', today())
                      ->orWhereDate('end_date', today())
                      ->orWhere(function($query) {
                          $query->where('start_date', '<=', today())
                                ->where('end_date', '>=', today());
                      });
            })
            ->count();
    
        return view('manager.leave', compact(
            'leaveRequests', 
            'pendingCount',  
            'todayLoginCount', 
            'leaveTypesPaidCountToday',
            'leaveTypesUnpaidCountToday',
            'leaveTypes',
            'departments',
            'user'
        ));
    }
    
    public function storeLeave(Request $request)
    { 
        $user = auth()->user();
        $leaveType = $request->input('type');
        $requestedDays = $request->input('total_days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Define valid leave types
        $validLeaveTypes = ['Vacation Leave', 'Sick Leave', 'Birthday Leave', 'Unpaid Leave'];

        // Check if the leave type is valid
        if (!in_array($leaveType, $validLeaveTypes)) {
            Alert::error('Invalid leave type selected');
            return redirect()->back();
        }

        // Check leave balance
        if ($leaveType == 'Vacation Leave') {
            if ($user->vacLeave < $requestedDays) {
                Alert::error('Insufficient vacation leave balance');
                return redirect()->back();
            }
        } elseif ($leaveType == 'Sick Leave') {
            if ($user->sickLeave < $requestedDays) {
                Alert::error('Insufficient sick leave balance');
                return redirect()->back();
            }
        } elseif ($leaveType == 'Birthday Leave') {
            if ($user->bdayLeave < $requestedDays) {
                Alert::error('Insufficient birthday leave balance');
                return redirect()->back();
            }
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

        // Save leave request
        $leaveRequest = new LeaveRequest();
        $leaveRequest->users_id = $user->id;
        $leaveRequest->name = $user->name;
        $leaveRequest->start_date = $startDate;
        $leaveRequest->end_date = $endDate;
        $leaveRequest->days = $requestedDays;
        $leaveRequest->reason = $request->input('reason');
        $leaveRequest->type = $leaveType;
        $leaveRequest->status = 'Pending';

        $leaveRequest->save();

        Alert::success('Leave Request Sent');

        return redirect()->back();
    }

    public function approve($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user = $leave->user;
        $currentUser = auth()->user();
        $requestedDays = $leave->days;
    
        // Check if the current user is trying to approve their own leave request

        
        if ($currentUser->id == $user->id) {
            Alert::error('You cannot approve your own leave request');
            return redirect()->back();
        }
    
        // Find the leave credit associated with the leave type
        $leaveCredit = LeaveCredit::where('user_id', $user->id)
            ->where('leave_type_id', $leave->leave_type_id) 
            ->first();
    
        if (!$leaveCredit) {
            Alert::error('Leave type not found for the user');
            return redirect()->back();
        }
    
        // Check remaining leave balance
        if ($leaveCredit->remaining_credits < $requestedDays) {
            Alert::error('Insufficient leave balance for this leave type');
            return redirect()->back();
        }
    
        // Deduct the requested days from the remaining credits
        $leaveCredit->remaining_credits -= $requestedDays;
        $leaveCredit->save();
    
        // Update the leave request status
        $leave->status = 'Approved';
        $leave->approved_by = $currentUser->id;
        $leave->save();
    
        // Send a notification to the employee who requested the leave
        $user->notify(new RequestApprovedNotification($leave));

        return redirect()->back()->with('success', 'Leave request approved');
    }

    public function decline($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user = $leave->user; // Get the user who made the leave request
        $requestedDays = $leave->days; // Get the number of requested days
    
        // Check if the leave request was approved and the credits were deducted
        if ($leave->status === 'Approved') {
            // Find the leave credit associated with the leave type
            $leaveCredit = LeaveCredit::where('user_id', $user->id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->first();
    
            if ($leaveCredit) {
                // Restore the leave credits
                $leaveCredit->remaining_credits += $requestedDays;
                $leaveCredit->save();
            }
            
            // Show a message for declined approved leave
            Alert::success('Leave request declined and credits restored');
        } elseif ($leave->status === 'Pending') {
            // Show a message for declined pending leave
            Alert::success('Leave declined successfully!');
        }
    
        // Update the leave request status to 'Declined'
        $leave->status = 'Declined';
        $leave->save();
    
        return redirect()->back();
    }
    
    public function update(Request $request, $id)
    {
        try {
            // Validate the request inputs
            $request->validate([
                'leave_type_id' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
                'dayse' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
                'start_datee' => 'required|date|after_or_equal:today', // Ensure start date is today or later
                'end_datee' => 'required|date|after_or_equal:start_datee', // Ensure end date is after the start date
                'reasone' => 'nullable|string|max:255', // Optional reason
            ]);
    
            $leave = LeaveRequest::findOrFail($id);
    
            // Check if the leave request is for a subordinate of the manager
            $user = auth()->user();
            if ($leave->user->reporting_to !== $user->id) {
                return redirect()->back()->withErrors('You do not have permission to update this leave request.');
            }
    
            // Prevent editing approved leave requests
            if ($leave->status == 'Approved') {
                return redirect()->back()->withErrors('This leave request has already been approved and cannot be edited.');
            }
    
            $leaveTypeId = $request->input('leave_type_id');
            $requestedDays = $request->input('dayse');
            $startDate = $request->input('start_datee');
            $endDate = $request->input('end_datee');
            $userId = $leave->users_id;
    
            // Fetch the leave credit record for this user and leave type
            $leaveCredit = LeaveCredit::where('user_id', $userId)
                ->where('leave_type_id', $leaveTypeId)
                ->first();
    
            if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
                return redirect()->back()->withErrors('The user does not have sufficient leave balance for the selected leave type.');
            }
    
            // Check for overlapping leave requests for the same user
            $overlappingLeave = LeaveRequest::where('users_id', $userId)
                ->where('id', '!=', $id) // Exclude the current leave request
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                        ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
                })
                ->exists();
    
            if ($overlappingLeave) {
                return redirect()->back()->withErrors('The user already has a leave request within the selected dates.');
            }
    
            // Update the leave request details
            $leave->leave_type_id = $leaveTypeId;
            $leave->start_date = $startDate;
            $leave->end_date = $endDate;
            $leave->days = $requestedDays;
            $leave->reason = $request->input('reasone');
            $leave->save();
    
            return redirect()->back()->with('success', 'Leave request updated successfully.');
    
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return redirect()->back()->withErrors('An error occurred while updating the leave request: ' . $e->getMessage());
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

        //Own Leave
    
        public function indexLeave()
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
    
          return view ('manager.leavemanager', compact('request', 'user', 'leaveCredits', 'pendingRequest', 'declineRequest', 'totalLeaveCredits'));
       }
    
       public function storeLeaveManager(Request $request)
       {
        $user = auth()->user();
        $leaveTypeId = $request->input('type'); 
        $requestedDays = $request->input('total_days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        // Validation rules
        $validator = Validator::make($request->all(), [
            'type' => 'required|exists:leave_credits,leave_type_id',
            'total_days' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'attached_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Example file validation
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
    
        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages as a single string
            $errorMessages = implode('<br>', $validator->errors()->all());
    
            return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
        }
    
        // Fetch the user's leave credits
        $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();
    
        // Create an array of valid leave type IDs based on the user's leave credits
        $validLeaveTypes = $leaveCredits->pluck('leave_type_id')->toArray();
    
        // Check if the leave type is valid
        if (!in_array($leaveTypeId, $validLeaveTypes)) {
            Alert::error('Invalid leave type selected');
            return redirect()->back();
        }
    
        // Check the user's leave balance for the selected leave type
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
            $filePath = $file->store('leave_attachments', 'public'); // Store in 'storage/app/public/leave_attachments'
        }
    
        // Save leave request
        $leaveRequest = new LeaveRequest();
        $leaveRequest->users_id = $user->id;
        $leaveRequest->name = $user->name;
        $leaveRequest->start_date = $startDate;
        $leaveRequest->end_date = $endDate;
        $leaveRequest->days = $requestedDays;
        $leaveRequest->reason = $request->input('reason');
        $leaveRequest->leave_type_id = $leaveTypeId; // Save the leave type ID
        $leaveRequest->status = 'Pending';
        $leaveRequest->attached_file = $filePath;  // Store the file path in the attached_file column
        $leaveRequest->save();
    
        // Notify supervisors and HR
        $supervisor = $user->supervisor;
        $hrUsers = User::where('role_as', User::ROLE_HR)->get();
        $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
        $notifiableUsers = collect([$supervisor])
            ->merge($hrUsers)
            ->merge($adminUsers)
            ->unique('id')  // Ensure no user is notified more than once
            ->filter();  // Remove any null values (in case supervisor is null)
    
        // Notify all unique users
        foreach ($notifiableUsers as $notifiableUser) {
            $notifiableUser->notify(new LeaveRequestNotification($leaveRequest, $user));
        }
    
        Alert::success('Leave Request Sent');
        return redirect()->back();
    }
    
       public function updateManager(Request $request, $id)
       {
        try {
            // Validate the request inputs
            $request->validate([
                'typee' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
                'dayse' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
                'start_datee' => 'required|date|after_or_equal:today', // Ensure start date is today or later
                'end_datee' => 'required|date|after_or_equal:start_datee', // Ensure end date is after the start date
                'reason' => 'nullable|string|max:255', // Optional reason
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
            $startDate = $request->input('start_datee');
            $endDate = $request->input('end_datee');
    
            // Fetch the leave credit record for this user and leave type
            $leaveCredit = LeaveCredit::where('user_id', $user->id)
                ->where('leave_type_id', $leaveTypeId)
                ->first();
    
            if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
                Alert::error('Insufficient leave balance for the selected leave type');
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
                Alert::error('You already have a leave request within the selected dates');
                return redirect()->back();
            }
    
            // Update the leave request details
            $leave->type = $leaveCredit->leaveType->leaveType; // Update to the leave type name if needed
            $leave->start_date = $startDate;
            $leave->end_date = $endDate;
            $leave->days = $requestedDays;
            $leave->reason = $request->input('reason');
            $leave->save();
    
            Alert::success('Leave request updated successfully');
            return redirect()->back();
            
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            Alert::error('An error occurred while updating the leave request: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    
       public function destroyManager($id)
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
