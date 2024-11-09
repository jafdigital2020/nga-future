<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SalaryTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    // ** Payroll Summary **//

    public function payrollSummary(Request $request)
    {
        // Retrieve selected filters from the request
        $selectedEarnings = $request->input('earnings', []);
        $selectedDeductions = $request->input('deductions', []);
        $selectedLoans = $request->input('loans', []);
        $department = $request->input('department', null);
        $dateRange = $request->input('date_range', null);
    
        // Get unique departments for the dropdown
        $departments = User::distinct()->pluck('department');
    
        // Start building the query
        $query = SalaryTable::query();
    
        // Apply department filter if selected
        if ($department) {
            $query->whereHas('user', function($q) use ($department) {
                $q->where('department', $department);
            });
        }
    
        // Apply date range filter if selected
        if ($dateRange) {
            [$startDate, $endDate] = explode(' - ', $dateRange);
            $query->whereDate('start_date', '>=', $startDate)
                  ->whereDate('end_date', '<=', $endDate);
        }
    
        // Execute the query to get the filtered results
        $summary = $query->get()->map(function ($payroll) use ($selectedEarnings, $selectedDeductions, $selectedLoans) {
            $payroll->earnings = json_decode($payroll->earnings, true) ?? [];
            $payroll->deductions = json_decode($payroll->deductions, true) ?? [];
            $payroll->loans = json_decode($payroll->loans, true) ?? [];
    
            // Filter each category based on selected items
            if (!empty($selectedEarnings)) {
                $payroll->earnings = array_filter($payroll->earnings, function ($earning) use ($selectedEarnings) {
                    return in_array($earning['earning_id'], $selectedEarnings);
                });
            }
    
            if (!empty($selectedDeductions)) {
                $payroll->deductions = array_filter($payroll->deductions, function ($deduction) use ($selectedDeductions) {
                    return in_array($deduction['deduction_id'], $selectedDeductions);
                });
            }
    
            if (!empty($selectedLoans)) {
                $payroll->loans = array_filter($payroll->loans, function ($loan) use ($selectedLoans) {
                    return in_array($loan['loan_id'], $selectedLoans);
                });
            }
    
            return $payroll;
        });
    
        // Retrieve dropdown options for Earnings, Deductions, and Loans
        $earningsOptions = [];
        $deductionsOptions = [];
        $loansOptions = [];
    
        SalaryTable::all()->each(function ($payroll) use (&$earningsOptions, &$deductionsOptions, &$loansOptions) {
            foreach (json_decode($payroll->earnings, true) ?? [] as $earning) {
                $earningsOptions[$earning['earning_id']] = $earning['name'];
            }
            foreach (json_decode($payroll->deductions, true) ?? [] as $deduction) {
                $deductionsOptions[$deduction['deduction_id']] = $deduction['name'];
            }
            foreach (json_decode($payroll->loans, true) ?? [] as $loan) {
                $loansOptions[$loan['loan_id']] = $loan['loan_name'];
            }
        });
    
        return view('admin.report.payrollsummary', compact(
            'summary', 
            'selectedEarnings', 
            'selectedDeductions', 
            'selectedLoans',
            'earningsOptions', 
            'deductionsOptions', 
            'loansOptions',
            'departments',
        ));
    }

    // ** Employee List ** //

    public function employeeList(Request $request)
    {
        $departments = User::distinct()->pluck('department');

        return view('admin.report.employeelist', compact('departments'));
    }

    public function getEmployeesByDepartment(Request $request)
    {
        $department = $request->input('department');
    
        // Log department value for debugging
        Log::info("Department selected: " . $department);
    
        if ($department === null) {
            return response()->json([], 400); 
        }
    
        // Check if the selected department is "all"
        if ($department === 'all') {
            $employees = User::all(['id', 'fName as name', 'lName']);
        } else {
            $employees = User::where('department', $department)->get(['id', 'fName as name', 'lName']);
        }
    
        // Combine first and last names for employee display
        $employees = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name . ' ' . $employee->lName,
            ];
        });
    
        return response()->json($employees);
    }

    public function getEmployeeData(Request $request)
    {
        $employeeIds = explode(',', $request->input('employees'));
        $attributes = explode(',', $request->input('attributes'));
    
        // Separate PersonalInformation and BankInformation attributes
        $personalInfoAttributes = array_intersect($attributes, [
            'religion', 'age', 'education', 'nationality', 'mStatus', 'numChildren'
        ]);
    
        $bankInfoAttributes = array_intersect($attributes, [
            'bankName', 'bankAccName', 'bankAccNumber'
        ]);
    
        // Main query with necessary relationships
        $query = User::whereIn('id', $employeeIds);
    
        // Include supervisor's name if selected
        if (in_array('reporting_to', $attributes)) {
            $query->with(['supervisor:id,fName,lName']);
        }
    
        // Include PersonalInformation and BankInformation if any of their attributes are selected
        if ($personalInfoAttributes) {
            $query->with(['personalInformation:users_id,' . implode(',', $personalInfoAttributes)]);
        }
        if ($bankInfoAttributes) {
            $query->with(['bankInfo:users_id,' . implode(',', $bankInfoAttributes)]);
        }
    
        // Fetch the required attributes from User and add ID for relationships
        $userAttributes = array_diff($attributes, array_merge($personalInfoAttributes, $bankInfoAttributes));
        $employees = $query->get(array_merge(['id'], $userAttributes));
    
        // Flatten and transform the data
        $flattenedEmployees = $employees->map(function ($employee) use ($personalInfoAttributes, $bankInfoAttributes) {
            $flatEmployee = $employee->toArray();
        
            // Supervisor transformation to display full name instead of ID
            if (!empty($flatEmployee['supervisor'])) {
                $flatEmployee['reporting_to'] = $flatEmployee['supervisor']['fName'] . ' ' . $flatEmployee['supervisor']['lName'];
            } else {
                $flatEmployee['reporting_to'] = null;
            }
        
            // Flatten personal_information attributes if available
            if (!empty($flatEmployee['personal_information'])) {
                foreach ($personalInfoAttributes as $attr) {
                    $flatEmployee[$attr] = $flatEmployee['personal_information'][0][$attr] ?? null;
                }
            }
        
            // Flatten bank_info attributes if available, using the first entry only
            if (!empty($flatEmployee['bank_info'])) {
                foreach ($bankInfoAttributes as $attr) {
                    $flatEmployee[$attr] = $flatEmployee['bank_info'][0][$attr] ?? null;
                }
            }
        
            // Remove nested structures
            unset($flatEmployee['personal_information'], $flatEmployee['bank_info'], $flatEmployee['supervisor']);
            return $flatEmployee;
        });
        
    
        return response()->json($flattenedEmployees);
    } 
    
}
