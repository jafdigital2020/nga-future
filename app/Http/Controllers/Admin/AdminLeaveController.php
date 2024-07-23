<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

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

        $data = LeaveRequest::query();

        // Apply filters based on the user's role
        if ($user->isAdmin() || $user->isHR()) {
            // Admin and HR can see all leave requests
            $pendingCount = LeaveRequest::where('status', 'Pending')->count();
        } elseif ($user->isSupervisor()) {
            // Supervisors can only see leave requests from their department
            $data->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department)
                      ->where('role_as', '!=', User::ROLE_HR) // Not HR
                      ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
            });
            $pendingCount = LeaveRequest::where('status', 'Pending')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
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
        $todayLoginCount = 0;
        $attendanceQuery = EmployeeAttendance::query();

        if ($user->isSupervisor()) {
            $attendanceQuery->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department);
            });
        }

        $attendanceQuery->whereDate('date', today());

        $todayLoginCount = $attendanceQuery->distinct('users_id')->count('users_id');

        // Leave Counts
        $vacationLeaveCountToday = LeaveRequest::where('type', 'Vacation Leave')
                                    ->where(function($query) {
                                        $query->whereDate('start_date', today())
                                              ->orWhereDate('end_date', today())
                                              ->orWhere(function($query) {
                                                  $query->where('start_date', '<=', today())
                                                        ->where('end_date', '>=', today());
                                              });
                                    })
                                    ->count();

        $sickLeaveCountToday = LeaveRequest::where('type', 'Sick Leave')
                                    ->where(function($query) {
                                        $query->whereDate('start_date', today())
                                              ->orWhereDate('end_date', today())
                                              ->orWhere(function($query) {
                                                  $query->where('start_date', '<=', today())
                                                        ->where('end_date', '>=', today());
                                              });
                                    })
                                    ->count();

        $birthdayLeaveCountToday = LeaveRequest::where('type', 'Birthday Leave')
                                    ->where(function($query) {
                                        $query->whereDate('start_date', today())
                                              ->orWhereDate('end_date', today())
                                              ->orWhere(function($query) {
                                                  $query->where('start_date', '<=', today())
                                                        ->where('end_date', '>=', today());
                                              });
                                    })
                                    ->count();

        $unpaidLeaveCountToday = LeaveRequest::where('type', 'Unpaid Leave')
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
            'vacationLeaveCountToday', 
            'sickLeaveCountToday', 
            'birthdayLeaveCountToday', 
            'unpaidLeaveCountToday', 
            'todayLoginCount', 
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

        if ($leave->type == 'Vacation Leave') {
            if ($user->vacLeave < $requestedDays) {
                Alert::error('Insufficient vacation leave balance');
                return redirect()->back();
            }
            $user->vacLeave -= $requestedDays;
        } elseif ($leave->type == 'Sick Leave') {
            if ($user->sickLeave < $requestedDays) {
                Alert::error('Insufficient sick leave balance');
                return redirect()->back();
            }
            $user->sickLeave -= $requestedDays;
        } elseif ($leave->type == 'Birthday Leave') {
            if ($user->bdayLeave < $requestedDays) {
                Alert::error('Insufficient birthday leave balance');
                return redirect()->back();
            }
            $user->bdayLeave -= $requestedDays;
        }

        $user->save();

        $leave->status = 'Approved';
        $leave->approved_by = $currentUser->id;
        $leave->save();

        Alert::success('Leave request approved');
        return redirect()->back();
    }

    public function decline($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->status = 'Declined';
        $leave->save();

        Alert::success('Leave request declined');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be edited.');
            return redirect()->back();
        }

        $user = auth()->user();
        $leaveType = $request->input('typee');
        $requestedDays = $request->input('dayse');
        $startDate = $request->input('start_datee');
        $endDate = $request->input('end_datee');

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

        $leave->type = $leaveType;
        $leave->start_date = $startDate;
        $leave->end_date = $endDate;
        $leave->days = $requestedDays;
        $leave->reason = $request->input('reason');
        $leave->save();

        Alert::success('Leave request updated successfully');
        return redirect()->back();
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