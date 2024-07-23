<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ApprovedAttendance;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceApproveController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $cutOff = $request->input('cut_off');
    
        // Initialize the query
        $data = ApprovedAttendance::query();
    
        // If no status is selected, default to showing pending requests
        if (empty($status)) {
            $data->where('status', 'Pending');
        }
    
        if ($user->isAdmin() || $user->isHR()) {
            // Admin and HR can see all leave requests
            $pendingCount = ApprovedAttendance::where('status', 'Pending')->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')->count();
            $declinedCount = ApprovedAtttendance::where('status', 'Declined')->count();
        } elseif ($user->isSupervisor()) {
            // Supervisors can only see leave requests from their department but not their own requests
            $data->whereHas('user', function ($query) use ($user) {
                $query->where('department', $user->department)
                      ->where('role_as', '!=', User::ROLE_HR) // Not HR
                      ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                      ->where('id', '!=', $user->id); // Not their own requests
            });
    
            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();
    
            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();

            $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->whereHas('user', function ($query) use ($user) {
                                    $query->where('department', $user->department)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN) // Not Admin
                                          ->where('id', '!=', $user->id); // Not their own requests
                                })->count();
        } else {
            // Employees can only see their own leave requests
            $data->where('users_id', $user->id);
    
            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->where('users_id', $user->id)
                                ->count();
    
            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->where('users_id', $user->id)
                                ->count();
           $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->where('users_id', $user->id)
                                ->count();
        }
    
        // Apply search filters independently
        if (!empty($name)) {
            $data->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            });
        }
    
        if (!empty($cutOff)) {
            $data->where('cut_off', 'like', "%$cutOff%");
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
    
        $attendance = $data->get();
    
        return view('manager.attendance', compact('attendance', 'pendingCount', 'user', 'approvedCount', 'declinedCount'));
    }
    
    public function approve($id)
    {
        $att = ApprovedAttendance::findOrFail($id);
        $user = $att->user;
        $currentUser = auth()->user();

        $user->save();

        $att->status = 'Approved';
        $att->approved_by = $currentUser->id;
        $att->save();

        Alert::success('Attendance Approved');
        return redirect()->back();
    }

    public function decline($id)
    {
        $att = ApprovedAttendance::findOrFail($id);
        $user = $att->user;
    
        $att->status = 'Declined';
        $att->save();
    
        Alert::success('Attendance Declined');
        return redirect()->back();
    }
    
}
