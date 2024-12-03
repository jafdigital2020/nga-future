<?php

namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\Salary;
use App\Models\Payroll;
use App\Models\EarningList;
use App\Models\SalaryTable;
use App\Models\UserEarning;
use Illuminate\Http\Request;
use App\Models\DeductionList;
use App\Models\UserDeduction;
use App\Models\EmployeeSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use RealRashid\SweetAlert\Facades\Alert;

class PayrollController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('hr.payroll.index', compact('users'));
    }

    public function approvedTime(Request $request)
    {
        $employeeName = $request->input('name');
        $department = $request->input('department');
        $cutoffPeriod = $request->input('cutoff_period'); // Get cutoff_period from request
        $status = $request->input('status', 'approved'); 
        $selectedYear = $request->input('year', now()->year); // Get the year from the request or default to the current year

        $data = ApprovedAttendance::query();
    
        // Add filter for employee name if provided
        if ($employeeName) {
            $data->where('name', 'like', "%$employeeName%");
        }
    
        // Add filter for department if provided
        if ($department) {
            $data->where('department', $department);
        }
    
        // Add filter for status
        if ($status) {
            $data->where('status', $status);
        }
    
        // Add filter for year if provided
        if ($selectedYear) {
            $data->where('year', $selectedYear);
        }
    
        // Add filter for cutoff_period if provided
        if ($cutoffPeriod) {
            $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
        }
    
        $approved = $data->get();
    
        // Assuming you have a list of departments to pass to the view
        $departments = ApprovedAttendance::select('department')->distinct()->get();
    
        // Assuming you have a list of possible statuses
        $statuses = ApprovedAttendance::select('status')->distinct()->get();
    
        return view('hr.payroll.approve', compact('approved', 'departments', 'statuses', 'cutoffPeriod', 'status', 'selectedYear'));
    }

        // ** Preview Payroll ** //
    public function previewPayroll(Request $request, $id)
    {
        try {
            $approvedAttendance = ApprovedAttendance::findOrFail($id);
            $user = $approvedAttendance->user;
 
            // Calculate Daily and Hourly Rates
            // Assume you have this field in the User model
            $hourlyRate = $user->hourly_rate; 
            // $dailyRate = $monthlySalary / 22; 
            $dailyRate = $hourlyRate * 8;    
            $monthlySalary = $dailyRate * 22;
            // $monthlySalary = $user->mSalary;
    
            $formattedDailyRate = number_format($dailyRate, 2, '.', '');
            $formattedHourlyRate = number_format($hourlyRate, 2, '.', '');
    
            $totalHours = $approvedAttendance->totalHours;
            list($hours, $minutes, $seconds) = explode(':', $totalHours);
            $totalHoursDecimal = $hours + ($minutes / 60) + ($seconds / 3600);
    
            $overtimeHours = $approvedAttendance->approvedOvertime ?? '00:00:00';
            list($otHours, $otMinutes, $otSeconds) = explode(':', $overtimeHours);
            $overtimeHoursDecimal = $otHours + ($otMinutes / 60) + ($otSeconds / 3600);
    
            $overtimeRate = $hourlyRate * 1.25;
            $overtimePay = $overtimeRate * $overtimeHoursDecimal;
    
            // Fetch Deductions
            $userDeductions = UserDeduction::where('users_id', $user->id)
            ->where('active', 1) 
            ->with('deductionList')
            ->get();
            $deductions = [];
            $totalDeductions = 0;
    
            foreach ($userDeductions as $userDeduction) {
                $deductionName = $userDeduction->deductionList->name;
                $deductionValue = $userDeduction->deductionList->amount;
                $deductionType = $userDeduction->deductionList->type;
                $deductionId = $userDeduction->deductionList->id; 
    
                $deductionAmount = ($deductionType === 'percentage') ? ($deductionValue / 100) * $monthlySalary : $deductionValue;
    
                $deductions[] = [
                    'deduction_id' => $deductionId,
                    'name' => $deductionName,
                    'amount' => number_format($deductionAmount, 2),
                ];
    
                $totalDeductions += $deductionAmount;
            }
    
            // Fetch Loans
            $loans = Loan::where('users_id', $user->id)
            ->where('status', 'Active')
            ->get();
            $totalLoans = 0;
            $loanDetails = [];
    
            foreach ($loans as $loan) {
                $totalLoans += $loan->payable_amount_per_cutoff;
                $loanDetails[] = [
                    'loan_id' => $loan->id,
                    'loan_name' => $loan->loan_name,
                    'amount' => number_format($loan->payable_amount_per_cutoff, 2),
                ];
            }
    
            // Fetch Earnings
            $userEarnings = UserEarning::where('users_id', $user->id)
            ->where('active', 1) 
            ->with('earningList')
            ->get();
            $earnings = [];
            $totalEarnings = 0;
    
            foreach ($userEarnings as $userEarning) {
                $earningName = $userEarning->earningList->name;
                $earningAmount = $userEarning->earningList->amount;
                $earningId = $userEarning->earningList->id;
    
                $earnings[] = [
                    'earning_id' => $earningId,
                    'name' => $earningName,
                    'amount' => number_format($earningAmount, 2),
                ];
    
                $totalEarnings += $earningAmount;
            }
    
            $totalEarnings += $overtimePay;
    
            $paidLeaveDays = $approvedAttendance->paidLeave ?? 0;
            $paidLeaveAmount = $paidLeaveDays * $dailyRate;
            $totalEarnings += $paidLeaveAmount;

            // Basic Pay
            $basicPay = $formattedHourlyRate * $totalHoursDecimal;

            // Gross Pay
            $grossPay = ($formattedHourlyRate * $totalHoursDecimal) + $totalEarnings;

            // Calculate net pay
            $netPay = $basicPay + $totalEarnings - $totalDeductions - $totalLoans;
    
            // Return the preview data
            return response()->json([
                'basicPay' => number_format($basicPay, 2),
                'grossPay' => number_format($grossPay, 2),
                'totalEarnings' => number_format($totalEarnings, 2),
                'totalDeductions' => number_format($totalDeductions, 2),
                'totalLoans' => number_format($totalLoans, 2),
                'netPay' => number_format($netPay, 2),
                'deductions' => $deductions,
                'earnings' => $earnings,
                'loans' => $loanDetails,
                'overtimePay' => number_format($overtimePay, 2),
                'paidLeaveAmount' => number_format($paidLeaveAmount, 2),
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // ** Preview & Edit Processed Payroll ** //
    public function processPreview(Request $request, $id)
    {
        try {
            $approvedAttendance = ApprovedAttendance::findOrFail($id);
            $user = $approvedAttendance->user;
    
            // Calculate Daily and Hourly Rates
            // Assume you have this field in the User model
            $hourlyRate = $user->hourly_rate; 
            // $dailyRate = $monthlySalary / 22; 
            $dailyRate = $hourlyRate * 8;    
            $monthlySalary = $dailyRate * 22;
            // $monthlySalary = $user->mSalary;
    
            // Format daily and hourly rates
            $formattedDailyRate = number_format($dailyRate, 2, '.', '');
            $formattedHourlyRate = number_format($hourlyRate, 2, '.', '');
    
            // Validate input
            $validated = $request->validate([
                'grossPay' => 'required|numeric',
                'basicPay' => 'required|numeric',
                'netPay' => 'required|numeric',
                'totalEarnings' => 'required|numeric',
                'totalDeductions' => 'required|numeric',
                'overtimePay' => 'nullable|numeric',
                'paidLeaveAmount' => 'nullable|numeric',
                'earnings.*' => 'nullable|numeric',
                'deductions.*' => 'nullable|numeric',
                'deduction_names.*' => 'required|string', // Validate deduction names
                'earning_names.*' => 'required|string',   // Validate earnings names
                'loan_names.*' => 'required|string', 
                'loans.*' => 'nullable|numeric',
            ]);
    
            // Check if payroll already exists
            $existingPayroll = SalaryTable::where('users_id', $user->id)
                ->where('month', $approvedAttendance->month)
                ->where('cut_off', $approvedAttendance->cut_off)
                ->first();
    
            if ($existingPayroll) {
                return response()->json(['error' => 'Payroll already exists for this user.'], 400);
            }
    
            // deductions array
            $deductions = [];
            $deductionIds = $request->input('deduction_ids', []);
            $deductionNames = $request->input('deduction_names', []);
            $deductionAmounts = $request->input('deductions', []);
    
            foreach ($deductionAmounts as $index => $amount) {
                if (isset($deductionIds[$index]) && isset($deductionNames[$index])) {
                    $deductions[] = [
                        'deduction_id' => $deductionIds[$index],
                        'name' => $deductionNames[$index],
                        'amount' => $amount,
                    ];
                }
            }
    
            // earnings array
            $earnings = [];
            $earningIds = $request->input('earning_ids', []);
            $earningNames = $request->input('earning_names', []);
            $earningAmounts = $request->input('earnings', []);
    
            foreach ($earningAmounts as $index => $amount) {
                if (isset($earningIds[$index]) && isset($earningNames[$index])) {
                    $earnings[] = [
                        'earning_id' => $earningIds[$index],
                        'name' => $earningNames[$index],
                        'amount' => $amount,
                    ];
                }
            }

            //loans array
            $loans = [];
            $loanIds = $request->input('loan_ids', []);
            $loanNames = $request->input('loan_names', []);
            $loanAmounts = $request->input('loans', []);

            foreach ($loanAmounts as $index => $amount) {
                if(isset($loanIds[$index]) && isset($loanNames[$index])) {
                    $loans[] = [
                        'loan_id' => $loanIds[$index],
                        'loan_name' => $loanNames[$index],
                        'amount' => $amount,
                    ];
                }
            }
    
            // Save to SalaryTable
            SalaryTable::create([
                'users_id' => $user->id,
                'approved_attendance_id' => $approvedAttendance->id,
                'month' => $approvedAttendance->month,
                'cut_off' => $approvedAttendance->cut_off,
                'year' => $approvedAttendance->year,
                'start_date' => $approvedAttendance->start_date,
                'end_date' => $approvedAttendance->end_date,
                'monthly_salary' => $monthlySalary,
                'total_hours' => $approvedAttendance->totalHours,
                'daily_rate' => $formattedDailyRate,
                'hourly_rate' => $formattedHourlyRate,
                'overtimeHours' => $request->input('overtimePay', 0),
                'paidLeave' => $request->input('paidLeaveAmount', 0),
                'basic_pay' => $validated['basicPay'],
                'gross_pay' => $validated['grossPay'],
                'net_pay' => $validated['netPay'],
                'total_earnings' => $validated['totalEarnings'],
                'total_deductions' => $validated['totalDeductions'],
                'deductions' => json_encode($deductions),
                'earnings' => json_encode($earnings),
                'loans' => json_encode($loans),
                'status' => 'Processed',
            ]);
    
            // Mark as processed
            $approvedAttendance->status = 'Processed';
            $approvedAttendance->save();
    
            return response()->json(['success' => 'Payroll processed successfully!']);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    // ** Seamless Process Payroll ** /
    public function processPayroll($approvedAttendanceId)
    {
        try {
            $approvedAttendance = ApprovedAttendance::find($approvedAttendanceId);
            $user = $approvedAttendance->user;
    
            // Check if a record for this user with the same month and cut-off already exists
            $existingSalary = SalaryTable::where('users_id', $user->id)
                ->where('month', $approvedAttendance->month)
                ->where('cut_off', $approvedAttendance->cut_off)
                ->first();
    
            if ($existingSalary) {
                return redirect()->back()->with(['error' => 'Payroll for this user has already been processed for the selected cut-off and month.']);
            }
    
            // Calculate Daily and Hourly Rates
            // Assume you have this field in the User model
            $hourlyRate = $user->hourly_rate; 
            // $dailyRate = $monthlySalary / 22; 
            $dailyRate = $hourlyRate * 8;    
            $monthlySalary = $dailyRate * 22;
            // $monthlySalary = $user->mSalary;
    
            // Format daily and hourly rates
            $formattedDailyRate = number_format($dailyRate, 2, '.', '');
            $formattedHourlyRate = number_format($hourlyRate, 2, '.', '');
    
            // Convert total hours to decimal format
            $totalHours = $approvedAttendance->totalHours; // This should be in H:i:s format
            list($hours, $minutes, $seconds) = explode(':', $totalHours);
            $totalHoursDecimal = $hours + ($minutes / 60) + ($seconds / 3600);
    
            // Convert overtime hours to decimal format
            $overtimeHours = $approvedAttendance->approvedOvertime ?? '00:00:00';
            list($otHours, $otMinutes, $otSeconds) = explode(':', $overtimeHours);
            $overtimeHoursDecimal = $otHours + ($otMinutes / 60) + ($otSeconds / 3600);
    
            // Calculate gross pay (for regular working hours)
            $basicPay = $formattedHourlyRate * $totalHoursDecimal;
    
            // Calculate overtime pay (1.25x the regular hourly rate)
            $overtimeRate = $hourlyRate * 1.25;
            $overtimePay = $overtimeRate * $overtimeHoursDecimal;
    
            // Fetch Deductions
            $userDeductions = UserDeduction::where('users_id', $user->id)
            ->where('active', 1) 
            ->with('deductionList')
            ->get();
            $deductions = [];
            $totalDeductions = 0;
    
            foreach ($userDeductions as $userDeduction) {
                $deductionName = $userDeduction->deductionList->name;
                $deductionValue = $userDeduction->deductionList->amount;
                $deductionType = $userDeduction->deductionList->type;
    
                // Calculate deduction amount based on type
                if ($deductionType === 'percentage') {
                    $deductionAmount = ($deductionValue / 100) * $monthlySalary;
                } else {
                    $deductionAmount = $deductionValue;
                }
    
                $deductions[] = [
                    'deduction_id' => $userDeduction->deduction_id,
                    'name' => $deductionName,
                    'amount' => $deductionAmount,
                ];
    
                $totalDeductions += $deductionAmount;
            }
    
            // Fetch Loans
            $loans = Loan::where('users_id', $user->id)
            ->where('status', 'Active')
            ->get();
            $totalLoans = 0;
            $loanDetails = [];
    
            foreach ($loans as $loan) {
                $totalLoans += $loan->payable_amount_per_cutoff;
                $loanDetails[] = [
                    'loan_id' => $loan->id,
                    'loan_name' => $loan->loan_name,
                    'amount' => $loan->payable_amount_per_cutoff,
                ];
            }
    
            // Fetch Dynamic Earnings
            $userEarnings = UserEarning::where('users_id', $user->id)
            ->where('active', 1) 
            ->with('earningList')
            ->get();
            $earnings = [];
            $totalEarnings = 0;
    
            foreach ($userEarnings as $userEarning) {
                $earningName = $userEarning->earningList->name;
                $earningAmount = $userEarning->earningList->amount;
    
                $earnings[] = [
                    'earning_id' => $userEarning->earning_id,
                    'name' => $earningName,
                    'amount' => $earningAmount,
                ];
    
                $totalEarnings += $earningAmount;
            }
    
            // Add overtime pay to total earnings
            $totalEarnings += $overtimePay;
    
            // Calculate paid leave (assuming 'paidLeave' is available in the attendance)
            $paidLeaveDays = $approvedAttendance->paidLeave ?? 0; // Assuming 'paidLeave' is a field in attendance
            $paidLeaveAmount = $paidLeaveDays * $dailyRate;
    
            // Add paid leave amount to total earnings
            $totalEarnings += $paidLeaveAmount;
 
            // Basic Pay
            $basicPay = $formattedHourlyRate * $totalHoursDecimal;

            // Gross Pay
            $grossPay = ($formattedHourlyRate * $totalHoursDecimal) + $totalEarnings;

            // Calculate net pay
            $netPay = $basicPay + $totalEarnings - $totalDeductions - $totalLoans;

            // Save to SalaryTable
            $newSalary = SalaryTable::create([
                'users_id' => $user->id,
                'approved_attendance_id' => $approvedAttendance->id,
                'month' => $approvedAttendance->month,
                'cut_off' => $approvedAttendance->cut_off,
                'year' => $approvedAttendance->year,
                'start_date' => $approvedAttendance->start_date,
                'end_date' => $approvedAttendance->end_date,
                'monthly_salary' => $monthlySalary,
                'total_hours' => $approvedAttendance->totalHours,
                'daily_rate' => $formattedDailyRate, 
                'hourly_rate' => $formattedHourlyRate, 
                'basic_pay' => $basicPay,
                'gross_pay' => $grossPay,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'total_loans' => $totalLoans,
                'net_pay' => $netPay,
                'deductions' => json_encode($deductions),
                'earnings' => json_encode($earnings),
                'loans' => json_encode($loanDetails),
                'overtimeHours' => number_format($overtimePay, 2),
                'paidLeave' => number_format($paidLeaveAmount, 2),
                'status' => 'Processed',
            ]);

            $approvedAttendance->status = 'Processed';
            $approvedAttendance->save();
    
            $summaryData = [
                'grossPay' => number_format($grossPay, 2),
                'totalEarnings' => number_format($totalEarnings, 2),
                'totalDeductions' => number_format($totalDeductions, 2),
                'totalLoans' => number_format($totalLoans, 2),
                'netPay' => number_format($netPay, 2),
                'overtimePay' => number_format($overtimePay, 2),
                'paidLeaveAmount' => number_format($paidLeaveAmount, 2),
                'deductions' => $deductions,
                'earnings' => $earnings,
                'loans' => $loanDetails,
            ];
    
            return redirect()->back()->with(['success' => 'Payroll processed successfully!', 'summaryData' => $summaryData]);
    
        } catch (\Exception $e) {
            // Catch any errors and return an error message
            return redirect()->back()->with(['error' => 'An error occurred while processing payroll: ' . $e->getMessage()]);
        }
    }

    private function calculateCurrentCutoff($date)
    {
        $day = $date->day;
        $month = $date->month;
        
        // Define cut-off periods with start and end dates
        $cutoffs = [
            0 => [26, 12, 10, 1],
            1 => [11, 1, 25, 1],
            2 => [26, 1, 10, 2],
            3 => [11, 2, 25, 2],
            4 => [26, 2, 10, 3],
            5 => [11, 3, 25, 3],
            6 => [26, 3, 10, 4],
            7 => [11, 4, 25, 4],
            8 => [26, 4, 10, 5],
            9 => [11, 5, 25, 5],
            10 => [26, 5, 10, 6],
            11 => [11, 6, 25, 6],
            12 => [26, 6, 10, 7],
            13 => [11, 7, 25, 7],
            14 => [26, 7, 10, 8],
            15 => [11, 8, 25, 8],
            16 => [26, 8, 10, 9],
            17 => [11, 9, 25, 9],
            18 => [26, 9, 10, 10],
            19 => [11, 10, 25, 10],
            20 => [26, 10, 10, 11],
            21 => [11, 11, 25, 11],
            22 => [26, 11, 10, 12],
            23 => [11, 12, 25, 12],
        ];
    
        // Determine the current cutoff period
        foreach ($cutoffs as $index => $cutoff) {
            [$startDay, $startMonth, $endDay, $endMonth] = $cutoff;
            $startDate = now()->setMonth($startMonth)->setDay($startDay);
            $endDate = now()->setMonth($endMonth)->setDay($endDay);
    
            if ($startMonth > $endMonth) {
                // Adjust the end year for December to January case
                $endDate = $endDate->addYear();
            }
    
            if ($date->between($startDate, $endDate)) {
                return $index;
            }
        }
    
        return 0; // Default to 0 if no match found
    }
    
    private function getCutoffPeriodDates($cutoffPeriod, $year)
    {
        $cutoffs = [
            0 => ['start' => '12-26', 'end' => '01-10'],
            1 => ['start' => '01-11', 'end' => '01-25'],
            2 => ['start' => '01-26', 'end' => '02-10'],
            3 => ['start' => '02-11', 'end' => '02-25'],
            4 => ['start' => '02-26', 'end' => '03-10'],
            5 => ['start' => '03-11', 'end' => '03-25'],
            6 => ['start' => '03-26', 'end' => '04-10'],
            7 => ['start' => '04-11', 'end' => '04-25'],
            8 => ['start' => '04-26', 'end' => '05-10'],
            9 => ['start' => '05-11', 'end' => '05-25'],
            10 => ['start' => '05-26', 'end' => '06-10'],
            11 => ['start' => '06-11', 'end' => '06-25'],
            12 => ['start' => '06-26', 'end' => '07-10'],
            13 => ['start' => '07-11', 'end' => '07-25'],
            14 => ['start' => '07-26', 'end' => '08-10'],
            15 => ['start' => '08-11', 'end' => '08-25'],
            16 => ['start' => '08-26', 'end' => '09-10'],
            17 => ['start' => '09-11', 'end' => '09-25'],
            18 => ['start' => '09-26', 'end' => '10-10'],
            19 => ['start' => '10-11', 'end' => '10-25'],
            20 => ['start' => '10-26', 'end' => '11-10'],
            21 => ['start' => '11-11', 'end' => '11-25'],
            22 => ['start' => '11-26', 'end' => '12-10'],
            23 => ['start' => '12-11', 'end' => '12-25'],
        ];
    
        $startDate = Carbon::createFromFormat('m-d', $cutoffs[$cutoffPeriod]['start'])->year($year);
        $endDate = Carbon::createFromFormat('m-d', $cutoffs[$cutoffPeriod]['end'])->year($year);
    
        // Adjust the end year for December to January case
        if ($cutoffs[$cutoffPeriod]['start'] > $cutoffs[$cutoffPeriod]['end']) {
            $endDate = $endDate->addYear();
        }
    
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }

    public function update (Request $request, $id)
    {
        $approved = ApprovedAttendance::findOrFail($id);

        $approved->start_date = $request->input('start_date');
        $approved->end_date = $request->input('end_date');
        $approved->totalLate = $request->input('total_late');
        $approved->totalHours = $request->input('total_hours');
        $approved->save();

        Alert::success('Attendance Updated');
        return redirect()->back();

    }
    
    public function payroll($id)
    {
        $payroll = ApprovedAttendance::findOrFail($id);

        return view('hr.payroll.edit', compact('payroll'));
    }

    public function destroy($id)
    {
        $approved = ApprovedAttendance::findOrFail($id);

        $approved->delete();

        Alert::success('Attendance deleted successfully');
        return redirect()->back();
    }
    
    public function payslip(Request $request, $id)
    {

       $approvedAttendance = ApprovedAttendance::first();
       $approved = ApprovedAttendance::findOrFail($id);

       $existingPayroll = Payroll::where('ename', $request->input('ename'))
           ->where('cut_off', $request->input('cut_off'))
           ->first();

           if ($existingPayroll) {
            Alert::error('Error', 'Payslip already existed.');
            return redirect()->route('approvedTime');
        }

       // Create a new payroll record for payslip
       $payroll = new Payroll();
       $payroll->users_id = $approved->users_id;
       $payroll->ename = $request->input('ename');
       $payroll->position = $request->input('position');
       $payroll->department = $request->input('department');
       $payroll->cut_off = $request->input('cut_off');
       $payroll->year = $request->input('year');
       $payroll->transactionDate = $request->input('transactionDate');
       $payroll->start_date = $request->input('start_date');
       $payroll->end_date = $request->input('end_date');
       $payroll->month = $request->input('month');
       $payroll->totalHours = $request->input('totalHours');
       $payroll->totalLate = $request->input('tLate');
       $payroll->sss = $request->input('sss');
       $payroll->pagIbig = $request->input('pagIbig');
       $payroll->philHealth = $request->input('philHealth');
       $payroll->withHolding = $request->input('withHolding');
       $payroll->late = $request->input('late');
       $payroll->loan = $request->input('loan');
       $payroll->advance = $request->input('advance');
       $payroll->others = $request->input('others');
       $payroll->bdayLeave = $request->input('bdayLeave');
       $payroll->vacLeave = $request->input('vacLeave');
       $payroll->sickLeave = $request->input('sickLeave');
       $payroll->regHoliday = $request->input('regHoliday');
       $payroll->otTotal = $request->input('otTotal');
       $payroll->nightDiff = $request->input('nightDiff');
       $payroll->bonus = $request->input('bonus');
       $payroll->totalDeduction = $request->input('totalDeduction');
       $payroll->totalEarning = $request->input('totalEarning');
       $payroll->grossMonthly = $request->input('grossMonthly');
       $payroll->grossBasic = $request->input('grossBasic');
       $payroll->dailyRate = $request->input('dailyRate');
       $payroll->hourlyRate = $request->input('hourlyRate');
       $payroll->netPayTotal = $request->input('netPayTotal');
       $payroll->savings = $request->input('savings');
       $payroll->reimbursement = $request->input('reimbursement');
       $payroll->sssLoan = $request->input('sssLoan');
       $payroll->hmo = $request->input('hmo');
       

    // Check which button was pressed and update status accordingly
    $action = $request->input('action');
    if ($action === 'save') {
        $approved->status = 'Processed';
        $payroll->status = 'Processed';
        Alert::success('Success', 'The record has been processed successfully.');
    } elseif ($action === 'generate') {
        $approved->status = 'Payslip';
        $payroll->status = 'Payslip';
        Alert::success('Success', 'The payroll has been generated successfully.');
    }
    $payroll->save();
    $approved->save();

    return redirect()->route('approvedTime');
   }


   // ** Processed Payroll Index ** //
   public function payslipProcess(Request $request)
   {
       $employeeName = $request->input('name');
       $department = $request->input('department');
       $cutoffPeriod = $request->input('cutoff_period'); 
       $selectedYear = $request->input('year', now()->year); 
       $departments = User::distinct()->pluck('department');
   
       // Initialize the query without calling query()
       $data = SalaryTable::with('approvedAttendance');
   
       // Apply filters independently
       if (!empty($employeeName)) {
           $data->where('ename', 'like', "%$employeeName%");
       }
   
       if ($department) {
        $data->whereHas('user', function ($query) use ($department) {
            $query->where('department', 'like', "%$department%");
        });
    }
   
       if($selectedYear) {
            $data->where('year', $selectedYear);
       }
   
       // Add filter for cutoff_period if provided
       if ($cutoffPeriod) {
           $data->where('cut_off', $cutoffPeriod); 
       }
   
       // Add filter for status being 'Processed', 'Revision', 'Declined', 'Revised'
       $data->whereIn('status', ['Processed', 'Revision', 'Declined', 'Revised']);
   
       // Fetch the filtered data
       $payslip = $data->get();
   
       return view('hr.payroll.processed', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
   }

       //  ** Update Payslip Processed ** //
    public function updateSalary(Request $request)
    {
        try {
            // Find the SalaryTable record using salary_id
            $salary = SalaryTable::findOrFail($request->salary_id);
    
            // Update basic salary fields
            $salary->overtimeHours = $request->input('overtimeHours');
            $salary->paidLeave = $request->input('paidLeave');
            $salary->total_deductions = $request->input('total_deductions');
            $salary->total_earnings = $request->input('total_earnings');
            $salary->net_pay = $request->input('net_pay');
    
            // Parse updated deductions, earnings, and loans
            $deductions = [];
            foreach ($request->input('deductions', []) as $key => $amount) {
                $deductions[] = [
                    'deduction_id' => $request->deduction_ids[$key],
                    'name' => $request->deduction_names[$key],
                    'amount' => $amount
                ];
            }
    
            $earnings = [];
            foreach ($request->input('earnings', []) as $key => $amount) {
                $earnings[] = [
                    'earning_id' => $request->earning_ids[$key],
                    'name' => $request->earning_names[$key],
                    'amount' => $amount
                ];
            }
    
            $loans = [];
            foreach ($request->input('loans', []) as $key => $amount) {
                $loans[] = [
                    'loan_id' => $request->loan_ids[$key],
                    'loan_name' => $request->loan_names[$key],
                    'amount' => $amount
                ];
            }
    
            // Encode to JSON and update the salary record
            $salary->deductions = json_encode($deductions);
            $salary->earnings = json_encode($earnings);
            $salary->loans = json_encode($loans);
            $salary->status = 'Revised';
            // Save the updated record
            $salary->save();
    
            // Return with success message
            return redirect()->back()->with(['success' => 'Salary record updated successfully!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'An error occurred while updating the salary: ' . $e->getMessage()]);
        }
    }

    // ** Processed Approved ** //
    public function processedApproved(Request $request, $id)
    {
        Log::info('Starting approval process for SalaryTable ID: ' . $id);
        
        $edit = SalaryTable::findOrFail($id);
        Log::info('Found SalaryTable: ', [$edit->toArray()]);
    
        // Check if the status is already 'Approved'
        if ($edit->status === 'Approved') {
            Log::warning('Approval attempt for already approved SalaryTable ID: ' . $id);
            Alert::error('This is already approved.');
            return redirect()->back();
        }
    
        // Update the status to 'Approved'
        $edit->status = 'Approved';
        $edit->save();
        Log::info('Updated status to Approved for SalaryTable ID: ' . $id);
    
        // Decode the earnings field
        $earningsArray = json_decode($edit->earnings, true);
        Log::info('Earnings Array: ', [$earningsArray]);
    
        if (is_array($earningsArray)) {
            foreach ($earningsArray as $earningData) {
                $earningId = $earningData['earning_id'];
                Log::info('Processing earning ID: ' . $earningId);

                // Fetch the corresponding EarningList record
                $earningList = EarningList::find($earningId);
                if ($earningList) {
                    Log::info('Found EarningList: ', [$earningList->toArray()]);
                } else {
                    Log::warning('No EarningList found for ID: ' . $earningId);
                    continue;
                }

                if (!$earningList->is_every_payroll && $earningList->inclusion_limit) {
                    $userEarning = UserEarning::where('users_id', $edit->users_id)
                        ->where('earning_id', $earningId)
                        ->first();

                    if ($userEarning) {
                        Log::info('Found UserEarning: ', [$userEarning->toArray()]);
                        // Increment inclusion_count
                        if ($userEarning->inclusion_count < $earningList->inclusion_limit) {
                            $userEarning->inclusion_count += 1; // Increment inclusion_count
                            $userEarning->save(); // Save the updated UserEarning
                            Log::info('Incremented inclusion_count for UserEarning ID: ' . $userEarning->id);

                            // Check if inclusion_count now equals inclusion_limit
                            if ($userEarning->inclusion_count === $earningList->inclusion_limit) {
                                // Set active to false (0) when counts match
                                $userEarning->active = 0;
                                $userEarning->save();
                                Log::info('Set active to false for UserEarning ID: ' . $userEarning->id);
                            }
                        } else {
                            Log::warning('Inclusion count limit reached for UserEarning ID: ' . $userEarning->id);
                        }
                    } else {
                        Log::warning('No UserEarning found for User ID: ' . $edit->users_id);
                    }
                }
            }
        }
    
        // Decode the deductions field
        $deductionsArray = json_decode($edit->deductions, true);
        Log::info('Deductions Array: ', [$deductionsArray]);
    
        // Process Deductions
        if (is_array($deductionsArray)) {
            foreach ($deductionsArray as $deductionData) {
                $deductionId = $deductionData['deduction_id'];
                Log::info('Processing deduction ID: ' . $deductionId);

                // Fetch the corresponding DeductionList record
                $deductionList = DeductionList::find($deductionId);
                if ($deductionList) {
                    Log::info('Found DeductionList: ', [$deductionList->toArray()]);
                } else {
                    Log::warning('No DeductionList found for ID: ' . $deductionId);
                    continue;
                }

                if (!$deductionList->is_every_payroll && $deductionList->inclusion_limit) {
                    $userDeduction = UserDeduction::where('users_id', $edit->users_id)
                        ->where('deduction_id', $deductionId)
                        ->first();

                    if ($userDeduction) {
                        Log::info('Found UserDeduction: ', [$userDeduction->toArray()]);
                        // Increment inclusion_count
                        if ($userDeduction->inclusion_count < $deductionList->inclusion_limit) {
                            $userDeduction->inclusion_count += 1; // Increment inclusion_count
                            $userDeduction->save(); // Save the updated UserDeduction
                            Log::info('Incremented inclusion_count for UserDeduction ID: ' . $userDeduction->id);

                            // Check if inclusion_count now equals inclusion_limit
                            if ($userDeduction->inclusion_count === $deductionList->inclusion_limit) {
                                // Set active to false (0) when counts match
                                $userDeduction->active = 0;
                                $userDeduction->save();
                                Log::info('Set active to false for UserDeduction ID: ' . $userDeduction->id);
                            }
                        } else {
                            Log::warning('Inclusion count limit reached for UserDeduction ID: ' . $userDeduction->id);
                        }
                    } else {
                        Log::warning('No UserDeduction found for User ID: ' . $edit->users_id);
                    }
                }
            }
        }
    
        // Decode the loans field
        $loansArray = json_decode($edit->loans, true);
        Log::info('Loans Array: ', [$loansArray]);
    
        // Process Loans
        if (is_array($loansArray)) {
            foreach ($loansArray as $loanData) {
                $loanId = $loanData['loan_id'];
                Log::info('Processing loan ID: ' . $loanId);
    
                // Fetch the corresponding Loan record
                $loan = Loan::find($loanId);
    
                if ($loan) {
                    Log::info('Found Loan: ', [$loan->toArray()]);
    
                    // Increment amount_paid by payable_amount_per_cutoff
                    $loan->amount_paid += $loan->payable_amount_per_cutoff;
                    
                    // Check if amount_paid now matches the total loan amount
                    if ($loan->amount_paid >= $loan->amount) {
                        // Set status to Completed and date_completed to today
                        $loan->status = 'Completed';
                        $loan->date_completed = now();
                        Log::info('Loan completed for Loan ID: ' . $loanId);
                    }
    
                    // Save the updated loan information
                    $loan->save();
                    Log::info('Updated amount_paid for Loan ID: ' . $loanId);
                } else {
                    Log::warning('No Loan found for ID: ' . $loanId);
                }
            }
        }
    
        Alert::success('Payslip Approved.');
        Log::info('Payslip approved successfully for SalaryTable ID: ' . $id);
        return redirect()->back();
    }

    // ** PROCESSED REVISION ** //
    public function processedRevision(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:255'
        ]);
    
        $edit = SalaryTable::findOrFail($id);
    
        if ($edit->status === 'Revision') {
            Alert::error('It is pending for revision.');
            return redirect()->back();
        }
    
        // Update status and save the note
        $edit->status = 'Revision';
        $edit->notes = $request->input('notes');  // Save the notes to the 'notes' column
        $edit->save();
    
        Alert::success('Payslip is for Revision.');
        return redirect()->back();
    }

    // ** PROCESSED DECLINED ** //
   public function processedDeclined(Request $request, $id)
   {
       $edit = SalaryTable::findOrFail($id);
   
       // Check if the status is already 'Approved'
       if ($edit->status === 'Declined') {
           Alert::error('This is already declined.');
           return redirect()->back();
       }
   
       // Update the status to 'Approved'
       $edit->status = 'Declined';
       $edit->save();
   
       Alert::success('Payslip Declined.');
       return redirect()->back();
   }
   

    // ** Approved Payslip (From Processed) ** //
    public function approvedPayslip(Request $request)
    {
        $employeeName = $request->input('name');
        $department = $request->input('department');
        $cutoffPeriod = $request->input('cutoff_period'); 
        $selectedYear = $request->input('year', now()->year); 
        $departments = User::distinct()->pluck('department');
    
        // Start the query on the SalaryTable model
        $data = SalaryTable::with('approvedAttendance')
            ->where('status', 'Approved'); // Filter for status first
    
        // Apply filters independently
        if (!empty($employeeName)) {
            $data->where('ename', 'like', "%$employeeName%");
        }
    
        // Ensure the department filter is applied only on the User model
        if (!empty($department)) {
            // Use whereHas to filter based on the related User's department
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }
    
        // Add filter for cutoff_period if provided
        if ($cutoffPeriod) {
            $data->where('cut_off', $cutoffPeriod); 
        }
    
        // Get the filtered payslip data
        $payslip = $data->get();
   
       return view('hr.payroll.approvepayslip', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
   }

    // ** Generate Payslip Action ** //
    public function generatePayslip(Request $request, $id)
    {
        $edit = SalaryTable::findOrFail($id);
    
        if ($edit->status === 'Payslip') {
            Alert::error('This is already generated.');
            return redirect()->back();
        }
    
        $edit->status = 'Payslip';
        $edit->save();
    
        return redirect()->back()->with('success', 'Payslip Generated');
    }

    // ** Generated Payslip View ** // 
    public function payslipView(Request $request)
    {
         $employeeName = $request->input('name');
         $department = $request->input('department');
         $cutoffPeriod = $request->input('cutoff_period'); 
         $selectedYear = $request->input('year', now()->year); 
         $departments = User::distinct()->pluck('department');
 
         // Start the query on the SalaryTable model
         $data = SalaryTable::with('approvedAttendance')
             ->where('status', 'Payslip'); // Filter for status first
 
         // Apply filters independently
         if (!empty($employeeName)) {
             $data->where('ename', 'like', "%$employeeName%");
         }
 
         // Ensure the department filter is applied only on the User model
         if (!empty($department)) {
             // Use whereHas to filter based on the related User's department
             $data->whereHas('user', function ($query) use ($department) {
                 $query->where('department', 'like', "%$department%");
             });
         }
 
         // Add filter for cutoff_period if provided
         if ($cutoffPeriod) {
             $data->where('cut_off', $cutoffPeriod); 
         }
 
         // Get the filtered payslip data
         $payslip = $data->get();
   
       return view('hr.payroll.payslip', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
    }
   
   public function viewPayslip($id)
   {
        $view = SalaryTable::with('user')->findOrFail($id);
        
        // Decode JSON columns
        $earnings = json_decode($view->earnings, true);
        $loans = json_decode($view->loans, true);
        $deductions = json_decode($view->deductions, true);

       return view('hr.payroll.payslipview', compact('view'));
   }

   public function editPayslip($id)
   {
        $pay = Payroll::findOrFail($id);

        return view('hr.payroll.editpayslip', compact('pay'));
   }

   public function download()
   {
    return view('hr.payroll.pdf');
   }

    // ** Bulk Action For Processed Payroll ** // 
   public function processedBulkAction(Request $request)
   {
       $this->validate($request, [
           'action' => 'required|string',
           'ids' => 'required|array',
           'ids.*' => 'exists:salary_tables,id', 
       ]);
   
       $action = $request->input('action');
       $ids = $request->input('ids');
   
       $successCount = 0;
       $errorMessages = [];
   
       foreach ($ids as $id) {
           $payroll = SalaryTable::find($id);
   
           if (!$payroll) {
               continue; // Skip if the payroll entry is not found
           }
   
           if ($action === 'Approved') {
               if ($payroll->status === 'Approved') {
                   $errorMessages[] = "Payroll ID {$id} is already approved.";
                   continue;
               }
   
               // Update the status to 'Approved'
               $payroll->status = 'Approved';
               $payroll->save();
   
               // Decode the earnings and deductions fields
               $earningsArray = json_decode($payroll->earnings, true);
               $deductionsArray = json_decode($payroll->deductions, true);
               $loansArray = json_decode($payroll->loans, true);
   
               // Process Earnings
               if (is_array($earningsArray)) {
                   foreach ($earningsArray as $earningData) {
                       $earningId = $earningData['earning_id'];
                       $earningList = EarningList::find($earningId);
   
                       if ($earningList && !$earningList->is_every_payroll && $earningList->inclusion_limit) {
                           $userEarning = UserEarning::where('users_id', $payroll->users_id)
                               ->where('earning_id', $earningId)
                               ->first();
   
                           if ($userEarning) {
                               if ($userEarning->inclusion_count < $earningList->inclusion_limit) {
                                   $userEarning->inclusion_count += 1;
                                   if ($userEarning->inclusion_count == $earningList->inclusion_limit) {
                                       $userEarning->active = 0;
                                   }
                                   $userEarning->save();
                               }
                           }
                       }
                   }
               }
   
               // Process Deductions
               if (is_array($deductionsArray)) {
                   foreach ($deductionsArray as $deductionData) {
                       $deductionId = $deductionData['deduction_id'];
                       $deductionList = DeductionList::find($deductionId);
   
                       if ($deductionList && !$deductionList->is_every_payroll && $deductionList->inclusion_limit) {
                           $userDeduction = UserDeduction::where('users_id', $payroll->users_id)
                               ->where('deduction_id', $deductionId)
                               ->first();
   
                           if ($userDeduction) {
                               if ($userDeduction->inclusion_count < $deductionList->inclusion_limit) {
                                   $userDeduction->inclusion_count += 1;
                                   if ($userDeduction->inclusion_count == $deductionList->inclusion_limit) {
                                       $userDeduction->active = 0;
                                   }
                                   $userDeduction->save();
                               }
                           }
                       }
                   }
               }
   
               // Process Loans
               if (is_array($loansArray)) {
                   foreach ($loansArray as $loanData) {
                       $loanId = $loanData['loan_id'];
                       $loan = Loan::find($loanId);
   
                       if ($loan) {
                           $loan->amount_paid += $loan->payable_amount_per_cutoff;
   
                           if ($loan->amount_paid >= $loan->amount) {
                               $loan->status = 'Completed';
                               $loan->date_completed = now();
                           }
                           $loan->save();
                       }
                   }
               }
   
               $successCount++;
           } elseif ($action === 'Revision') {
               if ($payroll->status === 'Revision') {
                   $errorMessages[] = "Payroll ID {$id} is already in revision.";
                   continue;
               }
               $payroll->status = 'Revision';
               $payroll->save();
               $successCount++;
           } elseif ($action === 'Declined') {
               if ($payroll->status === 'Declined') {
                   $errorMessages[] = "Payroll ID {$id} is already declined.";
                   continue;
               }
               $payroll->status = 'Declined';
               $payroll->save();
               $successCount++;
           }
       }
   
       if ($successCount > 0) {
           return response()->json([
               'success' => true,
               'message' => "{$successCount} payroll entries successfully updated."
           ]);
       } else {
           return response()->json([
               'success' => false,
               'message' => 'No payroll entries were updated. ' . implode(' ', $errorMessages)
           ]);
       }
   }
   
    // ** Bulk Action For Generate Payslip (Approve Payslip) ** //  
    public function generatePayslipBulkAction(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'exists:salary_tables,id', // Update table name to salary_tables
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids');

        $successCount = 0;
        $errorMessages = [];

        foreach ($ids as $id) {
            $payroll = SalaryTable::find($id);

            if (!$payroll) {
                continue; // Skip if the payroll entry is not found
            }

            if ($action === 'Payslip') {
                if ($payroll->status === 'Payslip') {
                    $errorMessages[] = "Payroll ID {$id} is already generated.";
                    continue;
                }
                $payroll->status = 'Payslip';
                $payroll->save();
                $successCount++;
            } 
        }

        if ($successCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "{$successCount} payroll entries successfully updated."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No payroll entries were updated. ' . implode(' ', $errorMessages)
            ]);
        }
    }

    public function processBulkPayroll(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');
    
        if ($action !== 'Process' || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Invalid action or no records selected.']);
        }
    
        DB::beginTransaction();
    
        try {
            foreach ($ids as $approvedAttendanceId) {
                $approvedAttendance = ApprovedAttendance::find($approvedAttendanceId);
    
                if (!$approvedAttendance) {
                    continue; // Skip if record is not found
                }
    
                $user = $approvedAttendance->user;
    
                // Check if payroll is already processed for the month and cut-off
                $existingSalary = SalaryTable::where('users_id', $user->id)
                    ->where('month', $approvedAttendance->month)
                    ->where('cut_off', $approvedAttendance->cut_off)
                    ->first();
    
                if ($existingSalary) {
                    continue; // Skip if payroll already processed
                }
    
                // Calculate Daily and Hourly Rates
                // Assume you have this field in the User model
                $hourlyRate = $user->hourly_rate; 
                // $dailyRate = $monthlySalary / 22; 
                $dailyRate = $hourlyRate * 8;    
                $monthlySalary = $dailyRate * 22;
                // $monthlySalary = $user->mSalary;
    
                // Format daily and hourly rates
                $formattedDailyRate = number_format($dailyRate, 2, '.', '');
                $formattedHourlyRate = number_format($hourlyRate, 2, '.', '');
    
                // Convert total hours to decimal format
                $totalHours = $approvedAttendance->totalHours;
                list($hours, $minutes, $seconds) = explode(':', $totalHours);
                $totalHoursDecimal = $hours + ($minutes / 60) + ($seconds / 3600);
    
                // Convert overtime hours to decimal format
                $overtimeHours = $approvedAttendance->approvedOvertime ?? '00:00:00';
                list($otHours, $otMinutes, $otSeconds) = explode(':', $overtimeHours);
                $overtimeHoursDecimal = $otHours + ($otMinutes / 60) + ($otSeconds / 3600);
    
                // Calculate gross pay (for regular working hours)
                $basicPay = $formattedHourlyRate * $totalHoursDecimal;
    
                // Calculate overtime pay (1.25x the regular hourly rate)
                $overtimeRate = $hourlyRate * 1.25;
                $overtimePay = $overtimeRate * $overtimeHoursDecimal;
    
                // Fetch Deductions
                $userDeductions = UserDeduction::where('users_id', $user->id)
                    ->where('active', 1)
                    ->with('deductionList')
                    ->get();
                $deductions = [];
                $totalDeductions = 0;
    
                foreach ($userDeductions as $userDeduction) {
                    $deductionName = $userDeduction->deductionList->name;
                    $deductionValue = $userDeduction->deductionList->amount;
                    $deductionType = $userDeduction->deductionList->type;
    
                    // Calculate deduction amount based on type
                    $deductionAmount = ($deductionType === 'percentage') ? ($deductionValue / 100) * $monthlySalary : $deductionValue;
    
                    $deductions[] = [
                        'deduction_id' => $userDeduction->deduction_id,
                        'name' => $deductionName,
                        'amount' => $deductionAmount,
                    ];
    
                    $totalDeductions += $deductionAmount;
                }
    
                // Fetch Loans
                $loans = Loan::where('users_id', $user->id)
                    ->where('status', 'Active')
                    ->get();
                $loanDetails = [];
                $totalLoans = 0;
    
                foreach ($loans as $loan) {
                    $totalLoans += $loan->payable_amount_per_cutoff;
                    $loanDetails[] = [
                        'loan_id' => $loan->id,
                        'loan_name' => $loan->loan_name,
                        'amount' => $loan->payable_amount_per_cutoff,
                    ];
                }
    
                // Fetch Dynamic Earnings
                $userEarnings = UserEarning::where('users_id', $user->id)
                    ->where('active', 1)
                    ->with('earningList')
                    ->get();
                $earnings = [];
                $totalEarnings = 0;
    
                foreach ($userEarnings as $userEarning) {
                    $earningName = $userEarning->earningList->name;
                    $earningAmount = $userEarning->earningList->amount;
    
                    $earnings[] = [
                        'earning_id' => $userEarning->earning_id,
                        'name' => $earningName,
                        'amount' => $earningAmount,
                    ];
    
                    $totalEarnings += $earningAmount;
                }
    
                // Add overtime pay to total earnings
                $totalEarnings += $overtimePay;
    
                // Calculate paid leave
                $paidLeaveDays = $approvedAttendance->paidLeave ?? 0;
                $paidLeaveAmount = $paidLeaveDays * $dailyRate;
    
                // Add paid leave amount to total earnings
                $totalEarnings += $paidLeaveAmount;
    
                // Basic Pay
                $basicPay = $formattedHourlyRate * $totalHoursDecimal;
    
                // Gross Pay
                $grossPay = $basicPay + $totalEarnings;
    
                // Calculate net pay
                $netPay = $basicPay + $totalEarnings - $totalDeductions - $totalLoans;
    
                // --- End Calculation Logic ---
    
                // Save to SalaryTable
                SalaryTable::create([
                    'users_id' => $user->id,
                    'approved_attendance_id' => $approvedAttendance->id,
                    'month' => $approvedAttendance->month,
                    'cut_off' => $approvedAttendance->cut_off,
                    'year' => $approvedAttendance->year,
                    'start_date' => $approvedAttendance->start_date,
                    'end_date' => $approvedAttendance->end_date,
                    'monthly_salary' => $monthlySalary,
                    'total_hours' => $approvedAttendance->totalHours,
                    'daily_rate' => $formattedDailyRate,
                    'hourly_rate' => $formattedHourlyRate,
                    'basic_pay' => $basicPay,
                    'gross_pay' => $grossPay,
                    'total_earnings' => $totalEarnings,
                    'total_deductions' => $totalDeductions,
                    'total_loans' => $totalLoans,
                    'net_pay' => $netPay,
                    'deductions' => json_encode($deductions),
                    'earnings' => json_encode($earnings),
                    'loans' => json_encode($loanDetails),
                    'overtimeHours' => number_format($overtimePay, 2),
                    'paidLeave' => number_format($paidLeaveAmount, 2),
                    'status' => 'Processed',
                ]);
    
                // Update the attendance status to "Processed"
                $approvedAttendance->status = 'Processed';
                $approvedAttendance->save();
            }
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Bulk payroll processing completed successfully!']);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json(['success' => false, 'message' => 'An error occurred during bulk processing: ' . $e->getMessage()]);
        }
    }
}
