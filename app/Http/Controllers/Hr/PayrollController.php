<?php

namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use Illuminate\Http\Request;
use App\Models\EmployeeSalary;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class PayrollController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('hr.payroll.index', compact('users'));
    }

    public function emppayroll(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $userId = $request->input('user_id');

            // Initialize user details
            $hourlyRate = 0;
            $position = 'Unknown';
            $name = 'Unknown';

            if ($userId) {
                // Fetch user details directly
                $user = DB::table('users')->where('id', $userId)->first();
                if ($user) {
                    $hourlyRate = $user->hourlyRate;
                    $position = $user->position;
                    $name = $user->name;
                }
            }

            $data = DB::table('attendance')
                ->join('users', 'attendance.users_id', '=', 'users.id')
                ->select('attendance.*', 'users.hourlyRate', 'users.name', 'users.position');

            if ($startDate && $endDate) {
                $data->whereBetween('date', [$startDate, $endDate]);
            }

            if ($userId) {
                $data->where('users_id', '=', $userId);
            }

            $filteredData = $data->get();
            $totalSeconds = 0;
            $totalLateSeconds = 0;

            foreach ($filteredData as $row) {
                $timeTotal = explode(':', $row->timeTotal);
                if (count($timeTotal) === 3) {
                    $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                    $totalSeconds += $seconds;
                }
            }

            foreach ($filteredData as $row) {
                $totalLate = explode(':', $row->totalLate);
                if (count($totalLate) === 3) {
                    $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                    $totalLateSeconds += $seconds;
                }
            }

            // Initialize total and totalLate
            $total = '00:00:00';
            $totalLate = '00:00:00';

            if ($totalSeconds > 0 || $totalLateSeconds > 0) {
                $totalHours = floor($totalSeconds / 3600);
                $totalMinutes = floor(($totalSeconds % 3600) / 60);
                $remainingSeconds = $totalSeconds % 60;

                $total = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $remainingSeconds);

                $totalLateHours = floor($totalLateSeconds / 3600);
                $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
                $remainingLateSeconds = $totalLateSeconds % 60;

                $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $remainingLateSeconds);
            }

            $users = User::all();
            $salaries = EmployeeSalary::all();

            if ($request->ajax()) {
                return response()->json([
                    'filteredData' => $filteredData,
                    'total' => $total,
                    'totalLate' => $totalLate,
                    'name' => $name,
                    'hourlyRate' => $hourlyRate,
                    'position' => $position,
                    'salaries' => $salaries,
                ]);
            }

            return view('hr.payroll.index', compact('filteredData', 'total', 'users', 'totalLate', 'startDate', 'endDate', 'name', 'salaries', 'hourlyRate', 'position'));

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    // INSERT TO DATABASE


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

        $name = $request->name;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Check if there is an existing record with the same name and date range
        $existingSalary = Salary::where('fullName', $name)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();

        if ($existingSalary) {
            return response()->json(['success' => false, 'message' => 'A record with the same name and date range already exists.']);
        }

        $salary = new Salary;

        $salary->users_id = $request->user_id;
        $salary->employee_name = $name;
        $salary->payroll_date = Carbon::now()->format('F d, Y');
        $salary->payroll_start = $startDate;
        $salary->payroll_end = $endDate;
        $salary->total_late = $request->total_late;
        $salary->total_hours = $request->total_hours;
        $salary->regular_holiday = $request->regular_holiday;
        $salary->special_holiday = $request->special_holiday;
        $salary->working_on_restday = $request->working_on_restday;
        $salary->working_on_weekend = $request->working_on_weekend;
        $salary->working_on_nightshift = $request->working_on_nightshift;
        $salary->birthday_pto_leave = $request->birthday_pto_leave;
        $salary->late = $request->late;
        $salary->absence = $request->absence;
        $salary->withholding_tax = $request->withholding_tax;
        $salary->sss = $request->sss;
        $salary->pag_ibig = $request->pag_ibig;
        $salary->phil_health = $request->phil_health;
        $salary->overtime = $request->overtime;
        $salary->thirteenth_month = $request->thirteenth_month;
        $salary->christmas_bonus = $request->christmas_bonus;
        $salary->food_allowance = $request->food_allowance;
        $salary->performance_bonus = $request->performance_bonus;
        $salary->others = $request->others;
        $salary->salary = $request->total;
        $salary->late_deduction = $request->latetotal;
        $salary->earnings = $request->earnings;
        $salary->total_deduct = $request->totaldeduc;
        $salary->gross_monthly = $request->grossmp;
        $salary->gross_basic = $request->grossb;
        $salary->save();

        return response()->json(['success' => true, 'message' => 'Data saved successfully.']);
    }



    public function check(Request $request)
    {
        $salary = EmployeeSalary::where('employee_name', $request->name)
                                ->where('payroll_start', $request->start_date)
                                ->where('payroll_end', $request->end_date)
                                ->first();

        if ($salary) {
            return response()->json(['message' => 'Payroll data already exists.']);
        }

        return response()->json(['message' => 'OK']);
    }

    public function view($salary_id)
    {
        $viewsalary = EmployeeSalary::with('user')->find($salary_id);
        return view('hr.payroll.view', compact('viewsalary'));
    }

    public function function_delete($salary_id)
    {
        $deletesalary = EmployeeSalary::find($salary_id);
        $deletesalary->delete();
        return back();
    }


}
