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
        
        // Get the IDs of the subordinates of the current supervisor
        $subordinateIds = User::where('reporting_to', $user->id)->pluck('id');
    
        // Initialize the query
        $data = ApprovedAttendance::whereIn('users_id', $subordinateIds);
        
        // Apply status filter
        if (empty($status)) {
            $status = 'Pending';
            $data->where('status', $status);
        } else {
            $data->where('status', 'like', "%$status%");
        }
        
        // Determine counts based on user role
        if ($user->isAdmin() || $user->isHR()) {
            // Admins and HR can see all records
            $pendingCount = ApprovedAttendance::where('status', 'Pending')->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')->count();
        } else {
            // Counts based on subordinates only
            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->whereIn('users_id', $subordinateIds)
                                ->count();
        }
        
        // Apply additional search filters
        if (!empty($name)) {
            $data->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            });
        }
    
        if (!empty($cutOff)) {
            $data->where('cut_off', 'like', "%$cutOff%");
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
        
        // Get the filtered attendance records
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

    public function updateAttendance(Request $request, $id)

    {
        $approved = ApprovedAttendance::findOrFail($id);

        $approved->start_date = $request->input('start_date');
        $approved->end_date = $request->input('end_date');
        $approved->totalHours = $request->input('totalHours');
        $approved->totalLate = $request->input('totalLate');
        $approved->save();

        Alert::success('Attendance Updated');
        return redirect()->back();
    }
    
    public function destroyAttendance($id)
    {
        $appr = ApprovedAttendance::findOrFail($id);

        $appr->delete();

        Alert::success('Attendance deleted successfully');
        return redirect()->back();
    }

    public function attendanceRecord(Request $request)
    {
        $user = auth()->user();
        $employeeName = $request->get('employee_name');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $department = $request->get('department');
        
        // Get the IDs of the subordinates of the current supervisor
        $subordinateIds = User::where('reporting_to', $user->id)->pluck('id');
    
        // Query to fetch users (subordinates) with their attendance records filtered by month, year, and optionally department
        $usersQuery = User::whereIn('id', $subordinateIds) // Only include subordinates of the supervisor
            ->with(['employeeAttendance' => function ($query) use ($month, $year, $department) {
                $query->whereMonth('date', $month)
                      ->whereYear('date', $year);
                if ($department) {
                    $query->whereHas('user', function ($query) use ($department) {
                        $query->where('department', $department);
                    });
                }
            }])
            ->when($employeeName, function ($query) use ($employeeName) {
                $query->where(function ($subQuery) use ($employeeName) {
                    $subQuery->where('fName', 'like', '%' . $employeeName . '%')
                             ->orWhere('lName', 'like', '%' . $employeeName . '%');
                });
            });
    
        // Execute the query to fetch the users with their attendance records
        $users = $usersQuery->get();
    
        return view('manager.attendancerecord', [
            'users' => $users,
            'month' => $month,
            'year' => $year,
            'selectedEmployeeName' => $employeeName,
            'selectedDepartment' => $department,
        ]);
    }
    
    
}
