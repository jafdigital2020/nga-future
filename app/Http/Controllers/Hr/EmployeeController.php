<?php

namespace App\Http\Controllers\HR;

use App\Models\User;
use Illuminate\Http\Request;
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
        return view ('hr.employee.index', compact('emp'));
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $empNumber = $request->input('empNumber');
        $department = $request->input('department');
        $department = $request->get('department');
    
        $data = User::query();
    
        // Filter by name-related fields if 'name' is provided
        if ($name) {
            $data->where(function ($query) use ($name) {
                $query->where('name', 'like', "%$name%")
                      ->orWhere('fName', 'like', "%$name%")
                      ->orWhere('mName', 'like', "%$name%")
                      ->orWhere('lName', 'like', "%$name%");
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
        $user = User::with('contactEmergency', 'bankInfo', 'personalInformation')->findOrFail($user_id);

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
        return view('hr.employee.edit', compact('user'));
    }


    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $user->name = $request->input('name');
        $user->fName = $request->input('fName');
        $user->mName = $request->input('mName');
        $user->lName = $request->input('lName');
        $user->suffix = $request->input('suffix');
        $user->empNumber = $request->input('empNumber');
        $user->typeOfContract = $request->input('typeOfContract');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->completeAddress = $request->input('completeAddress');
        $user->position = $request->input('position');
        $user->email = $request->input('email');
        $user->mSalary = $request->input('mSalary');
        $user->vacLeave = $request->input('vacLeave');
        $user->sickLeave = $request->input('sickLeave');


        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->hourlyRate = $request['hourlyRate'];
        $user->role_as = $request['role_as'];
        if ($request->has('image')) {
            $imageName = time().'.'.$request->image->extension();  
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

}
