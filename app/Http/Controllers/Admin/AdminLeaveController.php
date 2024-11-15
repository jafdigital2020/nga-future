<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveCredit;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\RequestApprovedNotification;

class AdminLeaveController extends Controller
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
    
        // Directly fetch leave credits for the authenticated user
        $leaveCredits = LeaveCredit::where('user_id', $user->id)->with('leaveType')->get();
    
        $data = LeaveRequest::query();
    
        // Admin and HR can see all leave requests
        if ($user->isAdmin() || $user->isHR()) {
            $pendingCount = LeaveRequest::where('status', 'Pending')->count();
        } elseif ($user->isSupervisor()) {
            // Supervisors can only see leave requests from their department(s)
            $data->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department)
                      ->where('role_as', '!=', User::ROLE_HR) // Exclude HR
                      ->where('role_as', '!=', User::ROLE_ADMIN); // Exclude Admin
            });
            $pendingCount = LeaveRequest::where('status', 'Pending')
                                    ->whereHas('user', function ($query) use ($user) {
                                        $query->where('department', $user->department)
                                              ->where('role_as', '!=', User::ROLE_HR)
                                              ->where('role_as', '!=', User::ROLE_ADMIN);
                                    })->count();
        } else {
            // Employees can only see their own leave requests
            $data->where('users_id', $user->id);
            $pendingCount = LeaveRequest::where('status', 'Pending')
                                ->where('users_id', $user->id)
                                ->count();
        }
    
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
    
        $leaveRequests = $data->get();
    
        // Fetch login count for today
        $todayLoginCount = EmployeeAttendance::query()
            ->whereDate('date', today())
            ->distinct('users_id')
            ->count('users_id');
    
        // Fetch today's leave requests and count for paid and unpaid where status is "Approved"
        $leaveTypesPaidCountToday = LeaveRequest::whereHas('leaveType', function($query) {
                $query->where('is_paid', true);
            })
            ->where('status', 'Approved')
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
            ->where(function($query) {
                $query->whereDate('start_date', today())
                      ->orWhereDate('end_date', today())
                      ->orWhere(function($query) {
                          $query->where('start_date', '<=', today())
                                ->where('end_date', '>=', today());
                      });
            })
            ->count();
    
        return view('admin.leave', compact(
            'leaveRequests', 
            'pendingCount', 
            'leaveTypesPaidCountToday', 
            'leaveTypesUnpaidCountToday', 
            'todayLoginCount', 
            'user',
            'leaveTypes',
            'departments',
            'leaveCredits'
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
                'typee' => 'required|exists:leave_types,id', // Ensure leave type ID exists in the leave_types table
                'dayse' => 'required|integer|min:1', // Ensure days are provided and is a positive integer
                'start_datee' => 'required|date|after_or_equal:today', // Ensure start date is today or later
                'end_datee' => 'required|date|after_or_equal:start_datee', // Ensure end date is after the start date
                'reason' => 'nullable|string|max:255', // Optional reason
            ]);
    
            $leave = LeaveRequest::findOrFail($id);
    
            // // Prevent editing approved leave requests
            // if ($leave->status == 'Approved') {
            //     Alert::error('This leave request has already been approved and cannot be edited.');
            //     return redirect()->back();
            // }
    
            $leaveTypeId = $request->input('typee');
            $requestedDays = $request->input('dayse');
            $startDate = $request->input('start_datee');
            $endDate = $request->input('end_datee');
            $userId = $leave->users_id;
    
            // Fetch the leave credit record for the leave type and user
            $leaveCredit = LeaveCredit::where('user_id', $userId)
                ->where('leave_type_id', $leaveTypeId)
                ->first();
    
                if (!$leaveCredit || $leaveCredit->remaining_credits < $requestedDays) {
                    Alert::error('The user’s leave balance is insufficient to fulfill the selected leave request.');
                    return redirect()->back();
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
                Alert::error('This user already has a leave request within the selected dates');
                return redirect()->back();
            }
    
            // Update the leave request details
            $leave->type = $leaveCredit->leaveType->leaveType; // Update to the leave type name if needed
            $leave->leave_type_id = $leaveTypeId; // Ensure the leave type ID is also updated in the request
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
