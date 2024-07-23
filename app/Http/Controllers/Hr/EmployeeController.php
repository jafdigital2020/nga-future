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
        $emp = User::all();
        return view ('hr.employee.index', compact('emp'));
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $empNumber = $request->input('empNumber');
        $department  = $request->input('department');

        $data = User::query();

        if ($name) {
            $data->where('name', 'like', "%$name%");
        }

        if ($empNumber) {
            $data->where('empNumber', 'like', "%$empNumber%");
        }

        if($department) {
            $data->where('department', 'like', "%$department%");
        }

        $emp = $data->get();

        return view('hr.employee.index', compact('emp'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'empNumber' => ['required', 'string', 'max:255'],
                'typeOfContract' => ['required', 'string', 'max:255'],
                'phoneNumber' => ['required', 'string', 'max:255'],
                'dateHired' => ['required', 'string', 'max:255'],
                'birthday' => ['required', 'string', 'max:255'],
                'completeAddress' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
                'hourlyRate' => ['required', 'string', 'max:255'],
                'role_as' => ['required', 'integer'],
                'sss' => ['required', 'string', 'max:255'],
                'pagIbig' => ['required', 'string', 'max:255'],
                'philHealth' => ['required', 'string', 'max:255'],
            ]);

            $imageName = null;
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images'), $imageName);
            }

            DB::table('users')->insert([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->password),
                'empNumber' => $request->input('empNumber'),
                'typeOfContract' => $request->input('typeOfContract'),
                'phoneNumber' => $request->input('phoneNumber'),
                'dateHired' => $request->input('dateHired'),
                'birthday' => $request->input('birthday'),
                'completeAddress' => $request->input('completeAddress'),
                'hourlyRate' => $request->input('hourlyRate'),
                'position' => $request->input('position'),
                'role_as' => $request->input('role_as'),
                'sss' => $request->input('sss'),
                'pagIbig' => $request->input('pagIbig'),
                'philHealth' => $request->input('philHealth'),
                'image' => $imageName,
                'department' => $request->input('department'),
                'bdayLeave' => '1',
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
        $user = User::with('contactEmergency', 'bankInfo')->findOrFail($user_id);

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
        $user->empNumber = $request->input('empNumber');
        $user->typeOfContract = $request->input('typeOfContract');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->completeAddress = $request->input('completeAddress');
        $user->position = $request->input('position');
        $user->email = $request->input('email');
        $user->hourlyRate = $request->input('hourlyRate');

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

        Alert::success('Changes Save Successfully', 'Employee Updated');
        return redirect()->back();
    }

    public function government (Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $user->sss = $request->input('sss');
        $user->pagIbig = $request->input('pagIbig');
        $user->philHealth = $request->input('philHealth');

        $user->save();

        Alert::success('Changes Save Successfully', 'Government Mandates Updated');
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
