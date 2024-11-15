<?php

namespace App\Http\Controllers\Hr;

use Exception;
use App\Models\User;
use App\Models\EarningList;
use App\Models\UserEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EarningController extends Controller
{
    public function earnings()
    {
        $earnings = EarningList::all();

        return view('hr.earning.earningtable', compact('earnings'));
    }

    public function earningCreate(Request $request)
    {
        // Validation Message
        $messages = [
            'name.required' => 'The earning name is required.',
            'amount.required' => 'The earning amount is required.',
            'amount.numeric' => 'The earning amount must be a valid number.',
            'type.required' => 'The earning type is required.',
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
            $errorMessages = implode($validator->errors()->all());
    
            return redirect()->back()->with('error', $errorMessages);
        }
    
        try {
            // Create the earning with the validated data
            EarningList::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'type' => $request->type,
                'inclusion_limit' => $request->is_every_payroll == 1 ? null : $request->inclusion_limit, // Null if every payroll
                'is_every_payroll' => $request->is_every_payroll,
            ]);
    
            return redirect()->back()->with('success', 'Earning created successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error creating earning: ' . $e->getMessage());
    
            // Return a generic error message
            return redirect()->back()->with('error', 'An error occurred while creating the earning. Please try again.');
        }
    }
    
    public function earningEdit(Request $request, $id)
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
            $earning = EarningList::findOrFail($id);
    
            $earning->name = $request->input('namee');
            $earning->amount = $request->input('amounte');
            $earning->type = $request->input('typee');
            $earning->inclusion_limit = $request->input('inclusion_limite');
            $earning->is_every_payroll = $reqiest->input('is_every_payrolle');
            $earning->save();
    
            return redirect()->back()->with('success', 'Earning updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update earning: ' . $e->getMessage());
        }
    }

    public function earningDestroy($id)
    {
        $earning = EarningList::findOrFail($id);

        $earning->delete();

        return redirect()->back()->with('success', 'Earning deleted sucessfully.');
    }

    public function userEarningsIndex(Request $request)
    {
        $users = User::all();
        $earnings = EarningList::all();
        $departments = User::distinct()->pluck('department');

        $data = UserEarning::with(['user', 'earningList']);

        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }

        $earning = $request->input('earning');
        if ($earning) {
            $data->whereHas('earningList', function ($query) use ($earning) {
                $query->where('id', $earning); 
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

        $userEarnings = $data->get();

        return view('hr.earning.userearning', compact('users', 'earnings', 'userEarnings', 'departments'));
    }

    public function getEmployeesByDepartmentEarning(Request $request)
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

    public function storeUserEarning(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|array',
            'earning_id' => 'required|array',
            'inclusion_count' => 'required|array', 
        ], [
            'users_id.required' => 'Please select at least one user.',
            'earning_id.required' => 'Please select at least one earning.',
            'inclusion_count.required' => 'Please provide an inclusion limit.',
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
                $user = User::findOrFail($userId); // Using findOrFail to ensure user exists
                $userFullName = $user->fName . ' ' . $user->lName;
    
                // Loop through the earnings for each employee
                foreach ($request->earning_id as $key => $earningId) {
                    // Fetch the earning name
                    $earning = EarningList::findOrFail($earningId); // Using findOrFail to ensure earning exists
                    $earningName = $earning->name;
    
                    // Check if this earning already exists for the user
                    $existingEarning = UserEarning::where('users_id', $userId)
                        ->where('earning_id', $earningId)
                        ->first();
    
                    // If the earning already exists, display a user-friendly error message
                    if ($existingEarning) {
                        return redirect()->back()->with('error', "The earning '$earningName' already exists for user $userFullName.");
                    }
    
                    // Create a new UserEarning record if it doesn't already exist
                    UserEarning::create([
                        'users_id' => $userId, // The employee ID
                        'earning_id' => $earningId, // The earning ID (foreign key)

                        'is_active' => true, // Assuming the default is active
                    ]);
                }
            }
    
            return redirect()->back()->with('success', 'Earnings successfully assigned!');
    
        } catch (Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error assigning earnings: ' . $e->getMessage());
    
            return redirect()->back()->with('error', 'An error occurred while assigning earnings. Please try again.');
        }
    }
    
    public function deleteUserEarning($id)
    {
        try {
            $userEarning = UserEarning::findOrFail($id); 
            $userEarning->delete();

            return response()->json(['success' => true, 'message' => 'Earning deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete earning: ' . $e->getMessage()]);
        }
    }
}
