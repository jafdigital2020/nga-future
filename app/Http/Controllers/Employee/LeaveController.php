<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


class LeaveController extends Controller
{

    public function index()
    {
      $user = Auth::user();
      $request = LeaveRequest::where('users_id', $user->id)->with('approver')->get();

      return view ('emp.leave', compact('request', 'user'));
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
