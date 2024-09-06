<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
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
    
        // Initialize the base query to fetch leave requests for subordinates of the current supervisor
        $data = LeaveRequest::whereHas('user', function ($query) use ($user) {
            $query->where('reporting_to', $user->id); // Ensure only the subordinates of this supervisor are fetched
        });
    
        // Filter by pending status to count the pending leave requests
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
    
        // Execute the query to fetch the leave requests
        $leaveRequests = $data->get();
    
        // Fetch login count for today
        $todayLoginCount = 0;
        $subordinates = $user->subordinates->pluck('id');
    
        // Count today's logins for these subordinates
        if ($subordinates->isNotEmpty()) {
            $todayLoginCount = EmployeeAttendance::query()
                ->whereIn('users_id', $subordinates)
                ->whereDate('date', today())
                ->distinct('users_id') // This will ensure each user is counted only once
                ->count('users_id');
        }

        // Function to count leaves by type for today
        $leaveCountQuery = function ($leaveType) use ($user) {
            return LeaveRequest::where('type', $leaveType)
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('reporting_to', $user->id);
                })
                ->where(function ($query) {
                    $query->whereDate('start_date', today())
                        ->orWhereDate('end_date', today())
                        ->orWhere(function ($query) {
                            $query->where('start_date', '<=', today())
                                ->where('end_date', '>=', today());
                        });
                });
        };

        // Calculate leave counts for today by type
        $vacationLeaveCountToday = $leaveCountQuery('Vacation Leave')->count();
        $sickLeaveCountToday = $leaveCountQuery('Sick Leave')->count();
        $birthdayLeaveCountToday = $leaveCountQuery('Birthday Leave')->count();
        $unpaidLeaveCountToday = $leaveCountQuery('Unpaid Leave')->count();
        
        return view('manager.leave', compact(
            'leaveRequests', 
            'pendingCount',  
            'todayLoginCount', 
            'vacationLeaveCountToday', 
            'sickLeaveCountToday', 
            'birthdayLeaveCountToday', 
            'unpaidLeaveCountToday', 
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


        // Send a notification to the employee who requested the leave
        $user->notify(new RequestApprovedNotification($leave));

        Alert::success('Leave request approved');
        return redirect()->back();
    }

    public function decline($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $user = $leave->user;
        $requestedDays = $leave->days;
    
        // Return the deducted leave balance if the leave was previously approved
        if ($leave->status == 'Approved') {
            if ($leave->type == 'Vacation Leave') {
                $user->vacLeave += $requestedDays;
            } elseif ($leave->type == 'Sick Leave') {
                $user->sickLeave += $requestedDays;
            } elseif ($leave->type == 'Birthday Leave') {
                $user->bdayLeave += $requestedDays;
            }
            $user->save();
        }
    
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

        //Own Leave
    
        public function indexLeave()
        {
          $user = Auth::user();
          $request = LeaveRequest::where('users_id', $user->id)->with('approver')->get();
    
          return view ('manager.leavemanager', compact('request', 'user'));
       }
    
       public function storeLeaveManager(Request $request)
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
    
             // Get the supervisor for the user's department
           $supervisor = User::getSupervisorForDepartment($user->department, $user);
    
           // Get all HR users
           $hrUsers = User::where('role_as', User::ROLE_HR)->get();
    
           // Get all Admin users
           $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
           // Notify the supervisor
           if ($supervisor && $supervisor != 'Management') {
               $supervisor->notify(new LeaveRequestNotification($leaveRequest, $user));  // Pass the leave request and user to the notification
           }
    
           // Notify all HR users
           foreach ($hrUsers as $hr) {
               $hr->notify(new LeaveRequestNotification($leaveRequest, $user));  // Pass the leave request and user to the notification
           }
    
           // Notify all Admin users
           foreach ($adminUsers as $admin) {
               $admin->notify(new LeaveRequestNotification($leaveRequest, $user));  // Pass the leave request and user to the notification
           }
    
    
           Alert::success('Leave Request Sent');
    
           return redirect()->back();
       }
    
       public function updateManager(Request $request, $id)
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
