<?php

namespace App\Http\Controllers\Hr;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DeductionList;
use App\Models\UserDeduction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeductionController extends Controller
{
    public function deductions()
    {
        $deductions = DeductionList::all();

        return view('hr.deduction.deductiontable', compact('deductions'));
    }

    public function deductionCreate(Request $request)
    {
        // Validation Message
            $messages = [
                'name.required' => 'The deduction name is required.',
                'amount.required' => 'The deduction amount is required.',
                'amount.numeric' => 'The deduction amount must be a valid number.',
                'type.required' => 'The deduction type is required.',
                'inclusion_limit.numeric' => 'The inclusion limit must be a valid number.',
                'is_every_payroll.required' => 'Please specify if this should be included in every payroll.',
            ];

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'type' => 'required|string',
                'inclusion_limit' => 'nullable|numeric|min:1', // Can be empty, but must be a number if provided
                'is_every_payroll' => 'required|boolean', // Either 0 or 1
            ], $messages);

            // Check if validation fails
            if ($validator->fails()) {
                // Get all error messages as a single string
                $errorMessages = implode( $validator->errors()->all());

                return redirect()->back()->with('error', $errorMessages);
            }

            try {
                // Create the earning with the validated data
                DeductionList::create([
                    'name' => $request->name,
                    'amount' => $request->amount,
                    'type' => $request->type,
                    'inclusion_limit' => $request->is_every_payroll == 1 ? null : $request->inclusion_limit, 
                    'is_every_payroll' => $request->is_every_payroll,
                ]);

            return redirect()->back()->with('success', 'Deduction created successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error creating deduction: ' . $e->getMessage());
    
            // Return a generic error message
            return redirect()->back()->with('error', 'An error occurred while creating the deduction. Please try again.');
        }
    }

    public function deductionEdit(Request $request, $id)
    {
         // Custom validation messages
         $messages = [
            'namee.required' => 'The deduction name is required.',
            'amounte.required' => 'The deduction amount is required.',
            'amounte.numeric' => 'The deduction amount must be a valid number.',
            'typee.required' => 'The deduction type is required.',
            'inclusion_limite.numeric' => 'The inclusion limit must be a valid number.',
            'is_every_payrolle.required' => 'Please specify if this should be included in every payroll.',
        ];

        // Validation rules
        $validator = Validator::make($request->all(), [
            'namee' => 'required|string|max:255',
            'amounte' => 'required|numeric|min:0',
            'typee' => 'required|string',
            'inclusion_limite' => 'nullable|numeric|min:1', // Can be empty, but must be a number if provided
            'is_every_payrolle' => 'required|boolean', // Either 0 or 1
        ], $messages);

        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages as a single string
            $errorMessages = implode( $validator->errors()->all());

            return redirect()->back()->with('error', $errorMessages);
        }       

        try {
            $deduction = DeductionList::findOrFail($id);
    
            $deduction->name = $request->input('namee');
            $deduction->amount = $request->input('amounte');
            $deduction->type = $request->input('typee');
            $deduction->inclusion_limit = $request->input('inclusion_limite');
            $deduction->is_every_payroll = $request->input('is_every_payrolle');
            $deduction->save();
    
            return redirect()->back()->with('success', 'Deduction updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update deduction: ' . $e->getMessage());
        }
    }

    public function deductionDestroy($id)
    {
        $deduction = DeductionList::findOrFail($id);

        $deduction->delete();

        return redirect()->back()->with('success', 'Deduction deleted sucessfully.');
    }
    

    public function userDeductionsIndex(Request $request)
    {
        // Fetch all users, deductions, and distinct departments
        $users = User::all();  
        $deductions = DeductionList::all();  
        $departments = User::distinct()->pluck('department');  
    
        // Initialize the query to fetch UserDeduction data
        $data = UserDeduction::with(['user', 'deductionList']);
    
        // Apply department filter if provided
        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }

        $deduction = $request->input('deduction');
        if ($deduction) {
            $data->whereHas('deductionList', function ($query) use ($deduction) {
                $query->where('id', $deduction); 
            });
        }
 
        $name = trim($request->input('name'));
        if ($name) {
            $data->whereHas('user', function ($query) use ($name) {
                $names = explode(' ', $name);
                if (count($names) > 1) {
                    $lName = array_pop($names);
                    $fName = implode(' ', $names);
                    $query->where(function ($query) use ($fName, $lName) {
                        $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($fName) . '%'])
                              ->whereRaw('LOWER(lName) like ?', ['%' . strtolower($lName) . '%']);
                    });
                } else {
                    $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($name) . '%'])
                          ->orWhereRaw('LOWER(lName) like ?', ['%' . strtolower($name) . '%']);
                }
            });
        }
    
        // Execute the query and get the filtered results
        $userDeductions = $data->get();
    
        // Return the view with the necessary data
        return view('hr.deduction.userdeduction', compact('users', 'deductions', 'userDeductions', 'departments'));
    }
    
    
    
    public function getEmployeesByDepartmentDeduction(Request $request)
    {
        // Validate department input
        $department = $request->input('department');
    
        // If department is not selected, return an error response
        if ($department === null) {
            return response()->json([], 400); 
        }
    
        // Check if the selected department is "all"
        if ($department === 'all') {
            // Get all employees
            $employees = User::all(['id', 'name']);
        } else {
            // Get employees for the specific department
            $employees = User::where('department', $department)->get(['id', 'name']);
        }
    
        return response()->json($employees);
    }
    

    public function storeUserDeduction(Request $request)
    {

        // Validation rules
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|array',
            'deduction_id' => 'required|array',
        ], [
            'users_id.required' => 'Please select at least one user.',
            'deduction_id.required' => 'Please select at least one deduction.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages as a single string
            $errorMessages = implode('<br>', $validator->errors()->all());

            return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
        }

        try {
            // Loop through the selected employees
            foreach ($request->users_id as $userId) {
                // Fetch the user's full name
                $user = User::find($userId);
                $userFullName = $user->fName . ' ' . $user->lName;
    
                // Loop through the deductions for each employee
                foreach ($request->deduction_id as $index => $deductionId) {
                    // Fetch the deduction name
                    $deduction = DeductionList::find($deductionId);
                    $deductionName = $deduction->name;
    
                    // Check if this deduction already exists for the user
                    $existingDeduction = UserDeduction::where('users_id', $userId)
                        ->where('deduction_id', $deductionId)
                        ->first();
    
                    // If the deduction already exists, display a user-friendly error message
                    if ($existingDeduction) {
                        return redirect()->back()->with('error', "The deduction '$deductionName' already exists for user $userFullName.");
                    }
    
                    // Create a new UserDeduction record if it doesn't already exist
                    UserDeduction::create([
                        'users_id' => $userId, // The employee ID
                        'deduction_id' => $deductionId, // The deduction ID (foreign key)
                    ]);
                }
            }
    
            return redirect()->back()->with('success', 'Deductions successfully assigned!' );
    
        } catch (Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error assigning deductions: ' . $e->getMessage());
    
            return redirect()->back()->with('error', 'An error occurred while assigning deductions. Please try again.');
        }
    }
    
    public function deleteUserDeduction($id)
    {
        try {
            $userDeduction = UserDeduction::findOrFail($id); 
            $userDeduction->delete();

            return response()->json(['success' => true, 'message' => 'Deduction deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete deduction: ' . $e->getMessage()]);
        }
    }
}
