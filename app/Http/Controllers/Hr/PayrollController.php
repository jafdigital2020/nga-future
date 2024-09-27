<?php

namespace App\Http\Controllers\Hr;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\EmployeeSalary;
use App\Models\ApprovedAttendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
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

   public function payslipProcess(Request $request)
   {
       $employeeName = $request->input('name');
       $department = $request->input('department');
       $cutoffPeriod = $request->input('cutoff_period'); // Get cutoff_period from request
       $selectedYear = $request->input('year', now()->year);
   
       $data = Payroll::query();
   
       // Apply filters independently
       if (!empty($employeeName)) {
           $data->where('ename', 'like', "%$employeeName%");
       }
   
       if (!empty($department)) {
           $data->where('department', $department);
       }
   
       if (!empty($selectedYear)) {
           $data->where('year', $selectedYear);
       }
   
        // Add filter for cutoff_period if provided
        if ($cutoffPeriod) {
            $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
        }

  
        $data->whereIn('status', ['Processed', 'Revision', 'Declined', 'Revised']);
   
       $payslip = $data->get();
   
       // Assuming you have a list of departments to pass to the view
       $departments = Payroll::select('department')->distinct()->get();
   
       return view('hr.payroll.processed', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
   }

   public function processedApproved(Request $request, $id)
   {
       $edit = Payroll::findOrFail($id);
   
       // Check if the status is already 'Approved'
       if ($edit->status === 'Approved') {
           Alert::error('This is already approved.');
           return redirect()->back();
       }
   
       // Update the status to 'Approved'
       $edit->status = 'Approved';
       $edit->save();
   
       Alert::success('Timesheet Approved.');
       return redirect()->back();
   }
   

   public function approvedPayslip (Request $request)
   {
       $employeeName = $request->input('name');
       $department = $request->input('department');
       $cutoffPeriod = $request->input('cutoff_period'); // Get cutoff_period from request
       $selectedYear = $request->input('year', now()->year);
   
       $data = Payroll::query();
   
       // Apply filters independently
       if (!empty($employeeName)) {
           $data->where('ename', 'like', "%$employeeName%");
       }
   
       if (!empty($department)) {
           $data->where('department', $department);
       }
   
       if (!empty($selectedYear)) {
           $data->where('year', $selectedYear);
       }
   
        // Add filter for cutoff_period if provided
        if ($cutoffPeriod) {
            $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
        }

        // Add filter for status being 'Payslip'
        $data->where('status', 'Approved');
   
       $payslip = $data->get();
   
       // Assuming you have a list of departments to pass to the view
       $departments = Payroll::select('department')->distinct()->get();
   
       return view('hr.payroll.approvepayslip', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
   }

   public function generatePayslip(Request $request, $id)
   {
       $edit = Payroll::findOrFail($id);
   
       if ($edit->status === 'Payslip') {
           Alert::error('This is already generated.');
           return redirect()->back();
       }
   
       $edit->status = 'Payslip';
       $edit->save();
   
       Alert::success('Payslip Generated.');
       return redirect()->back();
   }


   public function payslipView(Request $request)
   {
       $employeeName = $request->input('name');
       $department = $request->input('department');
       $cutoffPeriod = $request->input('cutoff_period'); 
       $selectedYear = $request->input('year', now()->year);
   
       $data = Payroll::query();
   
       // Apply filters independently
       if (!empty($employeeName)) {
           $data->where('ename', 'like', "%$employeeName%");
       }
   
       if (!empty($department)) {
           $data->where('department', $department);
       }
   
       if (!empty($selectedYear)) {
           $data->where('year', $selectedYear);
       }
   
       if (!empty($cutoffPeriod)) {
           $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
       }
   
       // Add filter for status being 'Payslip'
       $data->where('status', 'Payslip');
   
       $payslip = $data->get();
   
       // Assuming you have a list of departments to pass to the view
       $departments = Payroll::select('department')->distinct()->get();
   
       return view('hr.payroll.payslip', compact('payslip', 'departments', 'cutoffPeriod', 'selectedYear'));
   }
   
   

   public function viewPayslip($id)
   {
       $view = Payroll::findOrFail($id);

       return view('hr.payroll.payslipview', compact('view'));
   }

   public function editPayslip($id)
   {
        $pay = Payroll::findOrFail($id);

        return view('hr.payroll.editpayslip', compact('pay'));
   }

   public function updatePayslip(Request $request, $id)
   {
        $pay = Payroll::findOrFail($id);

        $pay->ename = $request->input('ename');
        $pay->position = $request->input('position');
        $pay->department = $request->input('department');
        $pay->cut_off = $request->input('cut_off');
        $pay->year = $request->input('year');
        $pay->transactionDate = $request->input('transactionDate');
        $pay->start_date = $request->input('start_date');
        $pay->end_date = $request->input('end_date');
        $pay->month = $request->input('month');
        $pay->totalHours = $request->input('totalHours');
        $pay->totalLate = $request->input('tLate');
        $pay->sss = $request->input('sss');
        $pay->pagIbig = $request->input('pagIbig');
        $pay->philHealth = $request->input('philHealth');
        $pay->withHolding = $request->input('withHolding');
        $pay->late = $request->input('late');
        $pay->loan = $request->input('loan');
        $pay->advance = $request->input('advance');
        $pay->others = $request->input('others');
        $pay->bdayLeave = $request->input('bdayLeave');
        $pay->vacLeave = $request->input('vacLeave');
        $pay->sickLeave = $request->input('sickLeave');
        $pay->regHoliday = $request->input('regHoliday');
        $pay->otTotal = $request->input('otTotal');
        $pay->nightDiff = $request->input('nightDiff');
        $pay->bonus = $request->input('bonus');
        $pay->totalDeduction = $request->input('totalDeduction');
        $pay->totalEarning = $request->input('totalEarning');
        $pay->grossMonthly = $request->input('grossMonthly');
        $pay->grossBasic = $request->input('grossBasic');
        $pay->dailyRate = $request->input('dailyRate');
        $pay->hourlyRate = $request->input('hourlyRate');
        $pay->netPayTotal = $request->input('netPayTotal');
        $pay->savings = $request->input('savings');
        $pay->reimbursement = $request->input('reimbursement');
        $pay->sssLoan = $request->input('sssLoan');
        $pay->hmo = $request->input('hmo');
        $pay->status = 'Revised';
        $pay->save();

        Alert::success('Updated');
        return redirect()->route('hr.payslipProcess');
   }

   public function download()
   {
    return view('hr.payroll.pdf');
   }
}
