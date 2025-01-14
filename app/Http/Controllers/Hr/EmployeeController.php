<?php

namespace App\Http\Controllers\HR;

use Exception;
use Carbon\Carbon;
use App\Models\Memo;
use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use App\Models\BankInformation;
use Illuminate\Validation\Rule;
use App\Models\ContactEmergency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class EmployeeController extends Controller
{
    public function index ()
    {
        $emp = User::where('role_as', '!=', 1)->get();
        $departments = User::distinct()->pluck('department');
        $names = User::select(DB::raw("CONCAT(fName, ' ', lName) as full_name"))
        ->distinct()
        ->pluck('full_name');

        return view ('hr.employee.index', compact('emp', 'departments', 'names'));
    }

    public function gridView(Request $request)
    {
        // Retrieve search inputs
        $name = trim($request->input('name')); // Trim any whitespace
        $empNumber = $request->input('empNumber');
        $department = $request->input('department');

        $departments = User::distinct()->pluck('department');
        $names = User::select(DB::raw("CONCAT(fName, ' ', lName) as full_name"))
        ->distinct()
        ->pluck('full_name');

        // Initialize the query
        $data = User::query()->where('role_as', '!=', 1); // Exclude users with role_as = 1

        // Search by name
        if ($name) {
            $data->where(function ($query) use ($name) {
                // Split the input into parts
                $names = explode(' ', $name);
                
                if (count($names) > 1) {
                    // Assume last part is the last name, and the rest are first name parts
                    $lName = array_pop($names);
                    $fName = implode(' ', $names);
        
                    // Handle search for both combined fName and lName
                    $query->where(function ($query) use ($fName, $lName) {
                        $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($fName) . '%'])
                              ->whereRaw('LOWER(lName) like ?', ['%' . strtolower($lName) . '%']);
                    });
                } else {
                    // Single part, search in both fields
                    $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($name) . '%'])
                          ->orWhereRaw('LOWER(lName) like ?', ['%' . strtolower($name) . '%']);
                }
            });
        }

        // Search by employee number
        if ($empNumber) {
            $data->where('empNumber', 'like', "%$empNumber%");
        }

        // Search by department
        if ($department) {
            $data->where('department', 'like', "%$department%");
        }

        // Execute the query and get results
        $emp = $data->get();

        // Return the view with the search results
        return view('hr.employee.grid', compact('emp', 'departments', 'names'));
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $empNumber = $request->input('empNumber');
        $department = $request->input('department');
        $department = $request->get('department');
        $names = User::select(DB::raw("CONCAT(fName, ' ', lName) as full_name"))
        ->distinct()
        ->pluck('full_name');
    
        $data = User::query();
    
        // Filter by name-related fields if 'name' is provided
        // Search by name
        if ($name) {
            $data->where(function ($query) use ($name) {
                // Split the input into parts
                $names = explode(' ', $name);
                
                if (count($names) > 1) {
                    // Assume last part is the last name, and the rest are first name parts
                    $lName = array_pop($names);
                    $fName = implode(' ', $names);
        
                    // Handle search for both combined fName and lName
                    $query->where(function ($query) use ($fName, $lName) {
                        $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($fName) . '%'])
                              ->whereRaw('LOWER(lName) like ?', ['%' . strtolower($lName) . '%']);
                    });
                } else {
                    // Single part, search in both fields
                    $query->whereRaw('LOWER(fName) like ?', ['%' . strtolower($name) . '%'])
                          ->orWhereRaw('LOWER(lName) like ?', ['%' . strtolower($name) . '%']);
                }
            });
        }
    
        // Filter by employee number if 'empNumber' is provided
        if ($empNumber) {
            $data->where('empNumber', 'like', "%$empNumber%");
        }
    
        // Filter by department if 'department' is provided
        if ($department) {
            $data->where('department', 'like', "%$department%");
        }
    
        $emp = $data->get();
    
        return view('hr.employee.index', compact('emp', 'departments', 'names'));
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'empNumber' => ['required', 'string', 'max:255'],
                'typeOfContract' => ['required', 'string', 'max:255'],
                'dateHired' => ['required', 'string', 'max:255'],
                'birthday' => ['required', 'string', 'max:255'],
                'completeAddress' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'role_as' => ['required', 'integer'],
                'sss' => ['required', 'string', 'max:255'],
                'pagIbig' => ['required', 'string', 'max:255'],
                'philHealth' => ['required', 'string', 'max:255'],
            ]);

            $imageName = 'default.png';
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
            }

            DB::table('users')->insert([
                'name' => $request->input('name'),
                'fName' => $request->input('fName'),
                'mName' => $request->input('mName'),
                'lName' => $request->input('lName'),
                'suffix' => $request->input('suffix'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->password),
                'empNumber' => $request->input('empNumber'),
                'typeOfContract' => $request->input('typeOfContract'),
                'phoneNumber' => $request->input('phoneNumber'),
                'dateHired' => $request->input('dateHired'),
                'birthday' => $request->input('birthday'),
                'completeAddress' => $request->input('completeAddress'),
                'hourly_rate' => $request->input('hourly_rate'),
                'position' => $request->input('position'),
                'role_as' => $request->input('role_as'),
                'sss' => $request->input('sss'),
                'pagIbig' => $request->input('pagIbig'),
                'philHealth' => $request->input('philHealth'),
                'tin' => $request->input('tin'),
                'image' => $imageName,
                'department' => $request->input('department'),
                'bdayLeave' => $request->input('bdayLeave'),
                'vacLeave' => $request->input('vacLeave'),
                'sickLeave' => $request->input('sickLeave'),

            ]);

            Alert::success('Employee Added Successfully', 'Employee Added');
            return redirect()->back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Validation Error', $e->getMessage())->persistent(true);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Alert::error('Error Occurred', $e->getMessage())->persistent(true);
            return redirect()->back()->withInput();
        }
    }

    // ** DELETE ** //
    public function delete_function(Request $request)
    {
        $data = User::find($request->emp_delete_id);
        $data->delete();
        return back();
    }

    // ** EMPLOYEE FETCH (EDIT) ** //
    public function edit($user_id)
    {
        $user = User::with('contactEmergency', 'bankInfo', 'employmentRecord', 'employmentSalary', 'shiftSchedule', 'leaveCredits', 'userAssets.asset', 'memos',)->findOrFail($user_id);
        $department = $user->department;
        $supervisor = $user->supervisor;
        $users = User::all();
        $leaveTypes = LeaveType::all();

        switch ($user->role_as) {
            case 1:
                $user->role_as_text = 'Admin';
                break;
            case 2:
                $user->role_as_text = 'HR';
                break;
            case 3:
                $user->role_as_text = 'Employee';
                break;
            default:
                $user->role_as_text = 'Unknown';
        }
        return view('hr.employee.edit', compact('user', 'supervisor', 'users', 'leaveTypes'));
    }

    // ** EMPLOYEE UPDATE ** //
    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);
    
        // Create a Validator instance
        $validator = Validator::make($request->all(), [
            'empNumber' => 'required|string',
            'typeOfContract' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'hourly_rate' => 'required|string',
            'reporting_to' => [
                'nullable',
                'exists:users,id',
                Rule::notIn([$user->id])
            ]
        ], [
            'reporting_to.required' => 'Please Select "Reporting To".',
            'empNumber.required' => 'Employee number is required.',
            'typeOfContract.required' => 'Please select type of contract.',
            'position.required' => 'Position field is required.',
            'department.required' => 'Department field is required.',
            'hourly_rate.required' => 'Hourly Rate field is required.',
            'reporting_to.exists' => 'The selected Reporting To is invalid or does not exist.',
            'reporting_to.not_in' => 'You cannot select the same user in reporting to.',
        ]);
        
        // Check for validation failure
        if ($validator->fails()) {
            // Get all validation error messages
            $errors = $validator->errors();
    
            if ($errors->has('reporting_to')) {
                $errorMessage = $errors->first('reporting_to');
            } elseif ($errors->has('empNumber')) {
                $errorMessage = $errors->first('empNumber');
            } elseif ($errors->has('typeOfContract')) {
                $errorMessage = $errors->first('typeOfContract');
            } elseif ($errors->has('position')) {
                $errorMessage = $errors->first('position');
            } elseif ($errors->has('department')) {
                $errorMessage = $errors->first('department');
            } elseif ($errors->has('hourly_rate')) {
                $errorMessage = $errors->first('hourly_rate');
            } else {
                $errorMessage = 'Please correct the errors and try again.';
            }
    
            Alert::error('Validation Error', $errorMessage);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Proceed with updating the user if validation passes
        $user->name = $request->input('name');
        $user->fName = $request->input('fName');
        $user->mName = $request->input('mName');
        $user->lName = $request->input('lName');
        $user->suffix = $request->input('suffix');
        $user->empNumber = $request->input('empNumber');
        $user->typeOfContract = $request->input('typeOfContract');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->department = $request->input('department');
        $user->completeAddress = $request->input('completeAddress');
        $user->position = $request->input('position');
        $user->email = $request->input('email');
        $user->hourly_rate = $request->input('hourly_rate');
        $user->reporting_to = $request->input('reporting_to');
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        
        $user->role_as = $request['role_as'];
        if ($request->has('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $user->image = $imageName;
        }
        
        $user->save();
    
        return redirect()->back()->with('success', 'Employee Updated!');
    }

    // ** LEAVE CREDITS ** //
    public function leaveCredits(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
    
            // Validate leave credits input
            $validator = Validator::make($request->all(), [
                'leave_credits.*' => 'required|numeric|min:0',
            ], [
                'leave_credits.*.required' => 'Leave credit is required.',
                'leave_credits.*.numeric' => 'Leave credit must be a number.',
                'leave_credits.*.min' => 'Leave credit cannot be negative.',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            // Fetch leave types to check maximum allowed days for each leave type
            $leaveTypes = LeaveType::whereIn('id', array_keys($request->input('leave_credits', [])))->get()->keyBy('id');
    
            $leaveCreditsData = $request->input('leave_credits', []);
    
            // Iterate through each leave type and validate against maximum allowed days
            foreach ($leaveCreditsData as $leaveTypeId => $remainingCredits) {
                if (!isset($leaveTypes[$leaveTypeId])) {
                    return redirect()->back()->with('error', "Leave type ID $leaveTypeId is invalid.");
                }
    
                $maxAllowedDays = $leaveTypes[$leaveTypeId]->leaveDays;
    
                // Check if the submitted credits exceed the allowed leave days
                if ($remainingCredits > $maxAllowedDays) {
                    return redirect()->back()->with([
                        "error" => "The leave credit for leave type {$leaveTypes[$leaveTypeId]->leaveType} cannot exceed $maxAllowedDays days."
                    ])->withInput();
                }
    
                // Create or update leave credits
                LeaveCredit::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveTypeId,
                    ],
                    [
                        'remaining_credits' => $remainingCredits
                    ]
                );
            }
    
            return redirect()->back()->with('success', 'Leave Updated!');
    
        } catch (\Exception $e) {
            Log::error('Error updating leave credits for user ID ' . $id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while updating leave credits. Please try again.');
        }
    }

    // ** GOVERTNMENT ** //
    public function government (Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $user->sss = $request->input('sss');
        $user->pagIbig = $request->input('pagIbig');
        $user->philHealth = $request->input('philHealth');
        $user->tin = $request->input('tin');

        $user->save();

        Alert::success('Government Mandates Updated');
        return redirect()->back();
    }

    // ** EMERGENCY CONTACT ** //
    public function contactStore (Request $request, $user_id)
    {
        $user = User::with('contactEmergency')->findOrFail($user_id);

        $contact = ContactEmergency::where('users_id', $user->id)->first();

        if ($contact) {
            // If existing contact found, update it
            $contact->primaryName = $request->input('primaryName');
            $contact->primaryRelation = $request->input('primaryRelation');
            $contact->primaryPhone = $request->input('primaryPhone');
            $contact->secondName = $request->input('secondName');
            $contact->secondRelation = $request->input('secondRelation');
            $contact->secondPhone = $request->input('secondPhone');

            $contact->save();

            Alert::success('Contact Emergency Updated');
        } else {
            // If no existing contact, create a new one
            $contact = new ContactEmergency();
            $contact->users_id = $user->id;
            $contact->primaryName = $request->input('primaryName');
            $contact->primaryRelation = $request->input('primaryRelation');
            $contact->primaryPhone = $request->input('primaryPhone');
            $contact->secondName = $request->input('secondName');
            $contact->secondRelation = $request->input('secondRelation');
            $contact->secondPhone = $request->input('secondPhone');

            $contact->save();

            Alert::success('Contact Emergency Added');
        }

        return redirect()->back();
    }

    // ** BANK INFO ** //
    public function bankInfo (Request $request, $user_id)
    {
        $user = User::with('bankInfo')->findOrfail($user_id);

        $bank = BankInformation::where('users_id', $user->id)->first();

        if($bank) {
            $bank->bankName = $request->input('bankName');
            $bank->bankAccName = $request->input('bankAccName');
            $bank->bankAccNumber = $request->input('bankAccNumber');

            $bank->save();

            Alert::success('Bank Information Updated');
        }
        else {
            $bank = new BankInformation();
            $bank->users_id = $user->id;
            $bank->bankName = $request->input('bankName');
            $bank->bankAccName = $request->input('bankAccName');
            $bank->bankAccNumber = $request->input('bankAccNumber');

            $bank->save();

            Alert::success('Bank Information Added');
        }
        return redirect()->back();
    } 

    public function personalInfo (Request $request, $user_id)
    {
        $user = User::with('personalInformation')->findOrfail($user_id);

        $info = PersonalInformation::where('users_id', $user->id)->first();

        if($info)
        {
            $info->religion = $request->input('religion');
            $info->age = $request->input('age');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');
            $info->personalEmail = $request->input('personalEmail');

            $info->save();

            Alert::success('Personal Information Updated');
        } else {
            
            $info = new PersonalInformation();
            $info->users_id = $user->id;
            $info->name = $user->name;
            $info->religion = $request->input('religion');
            $info->age = $request->input('age');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');
            $info->personalEmail = $request->input('personalEmail');

            $info->save();

            Alert::success('Personal Infromation Added');
        }

        return redirect()->back();
    }

    // ** CREATE MEMO ** //
    public function createMemo(Request $request, $id)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'date_issue' => 'required|date',
                'attached_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Allows image and PDF files up to 2MB
            ]);
    
            // Find the user by ID and load any existing memos
            $user = User::with('memos')->findOrFail($id);
    
            // Prepare to create a new memo
            $memo = new Memo();
            $memo->users_id = $user->id;
            $memo->date_issue = $request->input('date_issue');
    
            // Handle file upload if provided
            if ($request->hasFile('attached_file')) {
                $filePath = $request->file('attached_file')->store('memos', 'public');
                $memo->attached_file = $filePath;
            }
    
            // Save the memo
            $memo->save();
    
            // Return success response
            return redirect()->back()->with('success', 'Memo created successfully.');
    
        } catch (\Exception $e) {
            // Log and handle errors
            Log::error('Memo creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the memo. Please try again.');
        }
    }

    // ** UPDATE MEMO **//
    public function updateMemo(Request $request, $id)
    {
        $request->validate([
            'date_issue' => 'required|date',
            'attached_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $memo = Memo::findOrFail($id);
        $memo->date_issue = $request->date_issue;

        if ($request->hasFile('attached_file')) {
            // Delete old file if exists
            if ($memo->attached_file && \Storage::disk('public')->exists($memo->attached_file)) {
                \Storage::disk('public')->delete($memo->attached_file);
            }
            // Store new file
            $memo->attached_file = $request->file('attached_file')->store('memos', 'public');
        }

        $memo->save();

        return redirect()->back()->with('success', 'Memo updated successfully.');
    }

    // ** CHANGE THE PASSWORD OF THE USER ** //
    public function changePassword(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Custom validator to add the custom error message
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Password and confirm password do not match.'
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            Alert::error('Error', 'Password and confirm password do not match.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->updatePassword($user, $request->password);

        Alert::success('Password Change Successfully');
        return redirect()->back();
    }

    private function updatePassword($user, $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    // ** CREATE EMPLOYEE (VIEW) ** //
    public function createView ()
    {
        $users = User::all(); 
        $availableLeaveTypes = LeaveType::all(); 
    
        $assignedLeaveTypes = []; 
        $leaveCredits = []; 

        return view('hr.employee.create', compact('users', 'availableLeaveTypes', 'assignedLeaveTypes', 'leaveCredits'));
    }

    // ** CREATE EMPLOYEE **//
    public function create(Request $request)
    {
        try {

            // Handle image upload
            $imageName = 'default.png';
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
            }
    
            // Create user with Eloquent to handle timestamps automatically
            $user = User::create([
                'name' => $request->input('name'),
                'fName' => $request->input('fName'),
                'mName' => $request->input('mName'),
                'lName' => $request->input('lName'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->password),
                'empNumber' => $request->input('empNumber'),
                'typeOfContract' => $request->input('typeOfContract'),
                'phoneNumber' => $request->input('phoneNumber'),
                'dateHired' => $request->input('dateHired'),
                'birthday' => $request->input('birthday'),
                'completeAddress' => $request->input('completeAddress'),
                'hourly_rate' => $request->input('hourly_rate'),
                'position' => $request->input('position'),
                'role_as' => $request->input('role_as'),
                'sss' => $request->input('sss'),
                'pagIbig' => $request->input('pagIbig'),
                'philHealth' => $request->input('philHealth'),
                'tin' => $request->input('tin'),
                'department' => $request->input('department'),
                'reporting_to' => $request->input('reporting_to'),
                'image' => $imageName,
            ]);
    
            // Handle leave credits
            $leaveTypes = $request->input('leave_types'); // This should be an array of leave type IDs
            if ($leaveTypes && is_array($leaveTypes)) {
                foreach ($leaveTypes as $leaveTypeId) {
                    // Check if the leave type exists
                    $leaveType = LeaveType::find($leaveTypeId);
                    if ($leaveType) {
                        // Save leave credits for the user
                        LeaveCredit::create([
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveTypeId,
                            'remaining_credits' => $leaveType->leaveDays, // Set initial credits based on leaveDays
                        ]);
                    } else {
                        // Handle the case where the leave type does not exist
                        Log::warning("Leave type ID {$leaveTypeId} does not exist. Skipping leave credit assignment for user ID {$user->id}.");
                    }
                }
            }
     
            return redirect()->back()->with('success', 'Employee Added Successfully');
        } catch (ValidationException $e) {
            Alert::error('Validation Error', $e->getMessage())->persistent(true);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Alert::error('Error Occurred', $e->getMessage())->persistent(true);
            return redirect()->back()->withInput();
        }
    }
    
    public function validateStep1(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'cpassword' => 'required|string|same:password',
            'role_as' => 'required|integer',
        ], [
            'cpassword.same' => 'The confirmation password does not match.',
            'cpassword.required' => 'Confirm Password field is required',
            'role_as.required' => 'Role is required.',
        ]);

        return response()->json(['success' => true]);
    }

    public function validateStep2(Request $request)
    {
        $validatedData = $request->validate([
            'fName' => 'required|string',
            'mName' => 'required|string',
            'lName' => 'required|string',
            'name' => 'required|string',
            'phoneNumber' => 'required|string',
            'completeAddress' => 'required|string',
        ], [
            'fName.required' => 'First Name is required.',
            'mName.required' => 'Middle Name is required.',
            'lName.required' => 'Last Name is required.',
            'name.required' => 'Professional Name is required.',
            'phoneNumber.required' => 'Contact Number is required.',
            'completeAddress.required' => 'Address is required.',
        ]);

        return response()->json(['success' => true]);
    }

    public function validateStep3 (Request $request)
    {

        $validatedData = $request->validate([
            'empNumber' => 'required|string|unique:users,empNumber',
            'typeOfContract' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'hourly_rate' => 'required|string',
            'reporting_to' => 'required|string',
        ], [
            'reporting_to.required' => 'Please Select "Reporting To".',
            'empNumber.required' => 'Employee number is required.',
            'empNumber.unique' => 'The employee number has already been taken.',
            'typeOfContract.required' => 'Please select type of contract.',
            'position.required' => 'Position field is required',
            'department.required' => 'Deparment field is required',
            'hourly_rate.required' => 'Hourly Rate Field is required',
        ]);

        return response()->json(['success' => true]);
    }

    public function validateStep4 (Request $request)
    {
        $validatedData = $request->validate([
            'sss' => 'required|string|unique:users,sss',
            'philHealth' => 'required|string|unique:users,philHealth',
            'pagIbig' => 'required|string|unique:users,pagIbig',
            'tin' => 'required|string|unique:users,tin',
        ], [
            'sss.required' => 'SSS field is required.',
            'philHealth.required' => 'Philhealth field is required.',
            'pagIbig.required' => 'Pag-Ibig field is required.',
            'tin.required' => 'Tin field is required.',
            'sss.unique' => 'The SSS number has already been taken.',
            'philHealth.unique' => 'The PhilHealth number has already been taken.',
            'pagIbig.unique' => 'The Pag-ibig number has already been taken.',
            'tin.unique' => 'The TIN number has already been taken.',
        ]);

        return response()->json(['success' => true]);
    }

    public function validateStep5 (Request $request)
    {
        $validatedData = $request->validate([
            'sickLeave' => 'required|integer',
            'vacLeave' => 'required|integer',
            'bdayLeave' => 'required|integer',
        ], [
            'sickLeave.required' => 'Sick Leave field is required.',
            'vacLeave.required' => 'Vacation Leave field is required.',
            'bdayLeave.required' => 'Birthday Leave field is required.',
        ]);
    }

    // ** BULK CREATE USER ** //

    public function bulkCreate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);
    
        // Log start of the bulk upload process
        Log::info('Bulk user creation process started.');
    
        // Get the current user count
        $userCount = User::count();
        Log::info('Current user count: ' . $userCount);
        
        // If the total user count is already at 11, prevent further creation
        if ($userCount >= 100) {
            return redirect()->back()->with('error', 'User limit reached. You cannot add more users.');
        }
    
        if ($file = $request->file('file')) {
            Log::info('File found. Starting file processing.');
    
            $fileData = fopen($file, 'r');
            $header = fgetcsv($fileData); // Get header row
    
            $newUsersCount = 0;
            $errors = []; // Collect errors here
            $existingEmails = User::pluck('email')->toArray(); // Get existing emails
            $existingEmpNumbers = User::pluck('empNumber')->toArray(); // Get existing empNumbers
            $processedEmails = []; // Keep track of processed emails in the CSV
            $processedEmpNumbers = []; // Keep track of processed empNumbers in the CSV
    
            while ($row = fgetcsv($fileData)) {
                $newUsersCount++;
    
                // Combine header with row data
                $userData = array_combine($header, $row);
                Log::info('Processing row ' . $newUsersCount, $userData);
    
                // Check if adding this user will exceed the limit
                if (User::count() >= 100) {
                    $errors[] = "Row $newUsersCount: User limit reached. Cannot add more users.";
                    continue;
                }
    
                // Check if required fields are missing or invalid
                if (empty($userData['fName']) || empty($userData['lName']) || empty($userData['email']) || empty($userData['password'])) {
                    $errors[] = "Row $newUsersCount: Missing required fields (fName, lName, email, or password).";
                    continue; // Skip this row
                }
    
                // Validate email format
                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row $newUsersCount: Invalid email format.";
                    continue; // Skip this row
                }
    
                // Remove commas from hourly_rate and validate it
                $userData['hourly_rate'] = str_replace(',', '', $userData['hourly_rate']);
                if (!is_numeric($userData['hourly_rate'])) {
                    $errors[] = "Row $newUsersCount: Salary must be a numeric value without commas.";
                    continue; // Skip this row
                }
    
                // Check for duplicates in the CSV file
                if (in_array($userData['email'], $processedEmails)) {
                    $errors[] = "Row $newUsersCount: Duplicate entry for email '{$userData['email']}' in the CSV file.";
                    Log::warning("Row $newUsersCount: Duplicate email '{$userData['email']}' found in the CSV file.");
                    continue; // Skip this row
                }
    
                if (in_array($userData['empNumber'], $processedEmpNumbers)) {
                    $errors[] = "Row $newUsersCount: Duplicate entry for employee number '{$userData['empNumber']}' in the CSV file.";
                    Log::warning("Row $newUsersCount: Duplicate employee number '{$userData['empNumber']}' found in the CSV file.");
                    continue; // Skip this row
                }
    
                // Check for duplicates in the database
                if (in_array($userData['email'], $existingEmails)) {
                    $errors[] = "Row $newUsersCount: The email '{$userData['email']}' already exists in the database.";
                    Log::warning("Row $newUsersCount: Email '{$userData['email']}' already exists in the database.");
                    continue; // Skip this row
                }
    
                if (in_array($userData['empNumber'], $existingEmpNumbers)) {
                    $errors[] = "Row $newUsersCount: The employee number '{$userData['empNumber']}' already exists in the database.";
                    Log::warning("Row $newUsersCount: Employee number '{$userData['empNumber']}' already exists in the database.");
                    continue; // Skip this row
                }
    
                // Add email and empNumber to processed arrays to track duplicates
                $processedEmails[] = $userData['email'];
                $processedEmpNumbers[] = $userData['empNumber'];
    
                // Concatenate name fields
                $userData['name'] = $userData['fName'] . ' ' . ($userData['mName'] ?? '') . ' ' . $userData['lName'];
    
                // Hash the password
                $userData['password'] = Hash::make($userData['password']);
    
                try {
                    // Create the user
                    User::create($userData);
                } catch (\Exception $e) {
                    // Log any unexpected errors
                    Log::error('Error creating user on row ' . $newUsersCount . ': ' . $e->getMessage());
                    $errors[] = "Row $newUsersCount: Failed to create user due to a database error.";
                }
            }
    
            fclose($fileData);
    
            // Check if any errors occurred
            if (!empty($errors)) {
                Log::error('Bulk upload errors: ', $errors);
                // Format the errors for the UI
                return redirect()->back()->with('error', 'There were errors in the bulk upload: ' . implode('<br>', $errors));
            }
    
            return redirect()->back()->with('success', 'Users created successfully.');
        }
    
        return redirect()->back()->with('error', 'No file was uploaded.');
    }

}
