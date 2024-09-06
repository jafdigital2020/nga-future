<?php

namespace App\Http\Controllers\HR;

use App\Models\User;
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


class EmployeeController extends Controller
{
    public function index ()
    {
        $emp = User::where('role_as', '!=', 1)->get();
        $departments = User::distinct()->pluck('deparment');

        return view ('hr.employee.index', compact('emp', 'deparments'));
    }

    public function gridView(Request $request)
    {
        // Retrieve search inputs
        $name = trim($request->input('name')); // Trim any whitespace
        $empNumber = $request->input('empNumber');
        $department = $request->input('department');

        $departments = User::distinct()->pluck('deparment');

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
        return view('hr.employee.grid', compact('emp'));
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $empNumber = $request->input('empNumber');
        $department = $request->input('department');
        $department = $request->get('department');
    
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
    
        return view('hr.employee.index', compact('emp'));
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
                'mSalary' => $request->input('mSalary'),
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

    public function delete_function(Request $request)
    {
        $data = User::find($request->emp_delete_id);
        $data->delete();
        return back();
    }

    public function edit($user_id)
    {
        $user = User::with('contactEmergency', 'bankInfo', 'employmentRecord','employmentSalary','shiftSchedule')->findOrFail($user_id);
        $department = $user->department;
        $supervisor = $user->supervisor;
        $users = User::all();

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
        return view('hr.employee.edit', compact('user', 'supervisor', 'users'));
    }


    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Create a Validator instance
        $validator = Validator::make($request->all(), [
            'empNumber' => 'required|string',
            'typeOfContract' => 'required|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'mSalary' => 'required|string',
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
            'mSalary.required' => 'Monthly Salary field is required.',
            'reporting_to.exists' => 'The selected Reporting To is invalid or does not exist.',
            'reporting_to.not_in' => 'You cannot select the same user in reporting to.',
        ]);
        
        // Check for validation failure
        if ($validator->fails()) {
            // Get all validation error messages
            $errors = $validator->errors();

            // Check for specific field errors and set the message accordingly
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
            } elseif ($errors->has('mSalary')) {
                $errorMessage = $errors->first('mSalary');
            } else {
                // General error message if no specific field error
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
        $user->mSalary = $request->input('mSalary');
        $user->vacLeave = $request->input('vacLeave');
        $user->sickLeave = $request->input('sickLeave');
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
        
        Alert::success('Employee Updated');
        return redirect()->back();
    }

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

    public function shiftSchedule(Request $request, $user_id)
    {
        // Find the user and their shift schedule
        $user = User::with('shiftSchedule')->findOrFail($user_id);
        $shift = ShiftSchedule::where('users_id', $user->id)->first();
    
        // Determine if flexible time is enabled
        $flexibleTime = $request->input('flexibleTime') == '1';
    
        if ($flexibleTime) {
            // If flexible time is enabled, save this state
            if ($shift) {
                $shift->isFlexibleTime = true;
                $shift->save();
                Alert::success('Flexible Time Set');
            } else {
                // Create new shift schedule with flexible time
                $shift = new ShiftSchedule();
                $shift->users_id = $user->id;
                $shift->isFlexibleTime = true;
                $shift->save();
                Alert::success('Added Schedule with Flexible Time');
            }
        } else {
            // If flexible time is not enabled, save shift details
            $shiftStart = Carbon::parse($request->input('shiftStart'))->format('H:i:s');
            $lateThreshold = Carbon::parse($request->input('lateThreshold'))->format('H:i:s');
            $shiftEnd = Carbon::parse($request->input('shiftEnd'))->format('H:i:s');
    
            if ($shift) {
                // Update existing shift
                $shift->shiftStart = $shiftStart;
                $shift->lateThreshold = $lateThreshold;
                $shift->shiftEnd = $shiftEnd;
                $shift->isFlexibleTime = false;
                $shift->save();
                Alert::success('Shift Successfully Changed');
            } else {
                // Create new shift
                $shift = new ShiftSchedule();
                $shift->users_id = $user->id;
                $shift->shiftStart = $shiftStart;
                $shift->lateThreshold = $lateThreshold;
                $shift->shiftEnd = $shiftEnd;
                $shift->isFlexibleTime = false;
                $shift->save();
                Alert::success('Added Schedule');
            }
        }
    
        return redirect()->back();
    }

    public function createView ()
    {
        $users = User::all();

        return view('hr.employee.create', compact('users'));
    }

    public function create (Request $request)
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
                'mSalary' => $request->input('mSalary'),
                'position' => $request->input('position'),
                'role_as' => $request->input('role_as'),
                'sss' => $request->input('sss'),
                'pagIbig' => $request->input('pagIbig'),
                'philHealth' => $request->input('philHealth'),
                'tin' => $request->input('tin'),
                'department' => $request->input('department'),
                'bdayLeave' => $request->input('bdayLeave'),
                'vacLeave' => $request->input('vacLeave'),
                'sickLeave' => $request->input('sickLeave'),
                'reporting_to' => $request->input('reporting_to'),
                'image' => $imageName,
            ]);
    
            // Handle shift schedule
            $shift = ShiftSchedule::firstOrNew(['users_id' => $user->id]);
            $flexibleTime = $request->input('flexibleTime') == '1';
    
            if ($flexibleTime) {
                $shift->isFlexibleTime = true;
            } else {
                $shift->shiftStart = Carbon::parse($request->input('shiftStart'))->format('H:i:s');
                $shift->lateThreshold = Carbon::parse($request->input('lateThreshold'))->format('H:i:s');
                $shift->shiftEnd = Carbon::parse($request->input('shiftEnd'))->format('H:i:s');
                $shift->isFlexibleTime = false;
            }
            
            $shift->save();

            Alert::success('Employee Added Successfully', 'Employee Added');
            return redirect()->route('hr.employeeindex');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Alert::error('Validation Error', $e->getMessage())->persistent(true);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
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
            'mSalary' => 'required|string',
            'reporting_to' => 'required|string',
        ], [
            'reporting_to.required' => 'Please Select "Reporting To".',
            'empNumber.required' => 'Employee number is required.',
            'empNumber.unique' => 'The employee number has already been taken.',
            'typeOfContract.required' => 'Please select type of contract.',
            'position.required' => 'Position field is required',
            'department.required' => 'Deparment field is required',
            'mSalary.required' => 'Monthly Salary Field is required',
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

}
