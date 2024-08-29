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
            $status = 'Pending';
            $data->where('status', $status);
        } else {
            $data->where('status', 'like', "%$status%");
        }
    
        if ($user->isAdmin() || $user->isHR()) {
            $pendingCount = ApprovedAttendance::where('status', 'Pending')->count();
            $approvedCount = ApprovedAttendance::where('status', 'Approved')->count();
            $declinedCount = ApprovedAttendance::where('status', 'Declined')->count();
        } elseif ($user->isSupervisor()) {
            // Supervisors can see requests from multiple departments they manage
            $departments = [];
            if ($user->role_as == User::ROLE_OPERATIONS_MANAGER) {
                // Add departments handled by the Operations Manager
                $departments = ['SEO', 'Content'];
            } elseif ($user->role_as == User::ROLE_IT_MANAGER) {
                $departments = ['IT', 'Website Development'];
            } elseif ($user->role_as == User::ROLE_MARKETING_MANAGER) {
                $departments = ['Marketing'];
            }
    
            $data->whereHas('user', function ($query) use ($departments) {
                $query->whereIn('department', $departments)
                      ->where('role_as', '!=', User::ROLE_HR) // Not HR
                      ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
            });
    
            $pendingCount = ApprovedAttendance::where('status', 'Pending')
                                ->whereHas('user', function ($query) use ($departments) {
                                    $query->whereIn('department', $departments)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
                                })->count();
    
            $approvedCount = ApprovedAttendance::where('status', 'Approved')
                                ->whereHas('user', function ($query) use ($departments) {
                                    $query->whereIn('department', $departments)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
                                })->count();
    
            $declinedCount = ApprovedAttendance::where('status', 'Declined')
                                ->whereHas('user', function ($query) use ($departments) {
                                    $query->whereIn('department', $departments)
                                          ->where('role_as', '!=', User::ROLE_HR) // Not HR
                                          ->where('role_as', '!=', User::ROLE_ADMIN); // Not Admin
                                })->count();
        } else {
            // Regular users can only see their own requests
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
    
        // Define the departments that each manager role can view
        $departmentManagerRoles = [
            User::ROLE_IT_MANAGER => ['IT', 'Website Development'],
            User::ROLE_OPERATIONS_MANAGER => ['SEO', 'Content'],
            User::ROLE_MARKETING_MANAGER => ['Marketing'],
            // Add more roles and departments as needed
        ];
    
        // Get the departments the authenticated user can view based on their role
        $allowedDepartments = $departmentManagerRoles[$user->role_as] ?? [];
    
        // Query users with their attendance records filtered by month, year, and optionally department
        $usersQuery = User::query()
            ->where('role_as', '!=', 1) // Exclude role with ID 1 (assuming it's not a manager role)
            ->whereIn('department', $allowedDepartments) // Filter users by allowed departments
            ->with(['employeeAttendance' => function ($query) use ($month, $year, $department) {
                $query->whereMonth('date', $month)->whereYear('date', $year);
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
