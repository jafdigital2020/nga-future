<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Models\Payroll;
use App\Models\SalaryTable;
use Illuminate\Http\Request;
use App\Models\EmployeeSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use RealRashid\SweetAlert\Facades\Alert;


class PayslipController extends Controller
{
    public function payslipView(Request $request)
    {
        $cutoffPeriod = $request->input('cutoff_period');
        $selectedYear = $request->input('year', now()->year);

        $data = SalaryTable::query();
    
        // Filter by authenticated user
        $data->where('users_id', auth()->id());
    
    
        // Add filter for year if provided
        if (!empty($selectedYear)) {
            $data->where('year', $selectedYear);
        }
    
        // Add filter for cutoff_period if provided
        if (!empty($cutoffPeriod)) {
            $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
        }
        
        // Add filter for status being 'Payslip'
        $data->where('status', 'Payslip');
        
        $payslip = $data->get();
    

        return view('emp.payslip.index', compact('payslip', 'cutoffPeriod', 'selectedYear'));
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
    
 
    public function viewPayslip($id)
    {
        $view = SalaryTable::findOrFail($id);

         // Decode JSON columns
        $earnings = json_decode($view->earnings, true);
        $loans = json_decode($view->loans, true);
        $deductions = json_decode($view->deductions, true);
            
 
        return view('emp.payslip.view', compact('view', 'earnings', 'loans', 'deductions'));
    }
 
    public function download()
    {
     return view('emp.payslip.pdf');
    }

}
