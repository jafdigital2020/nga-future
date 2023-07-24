<?php

namespace App\Http\Controllers\HR;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeController extends Controller
{
    public function index ()
    {
        $emp = User::all();
        return view ('hr.employee.index', compact('emp'));
    }

    public function store (Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'empNumber' => ['required', 'string', 'max:255'],
            'typeOfContract' => ['required', 'string', 'max:255'],
            'phoneNumber' => ['required', 'string', 'max:255'],
            'dateHired' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'string', 'max:255'],
            'completeAddress' => ['required', 'string', 'max:255'],
            'position' => ['required','string', 'max:255'],
            'hourlyRate' => ['required','string', 'max:255'],
            'role_as' => ['required', 'integer'],
            'sss' => ['required','string', 'max:255'],
            'pagIbig' => ['required','string', 'max:255'],
            'philHealth' => ['required','string', 'max:255'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

            $imageName = time().'.'.$request->image->extension();  
   
            $request->image->move(public_path('images'), $imageName);
           
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
        ]);

        Alert::success('Employee Added Successfully','Employee Added');
        return redirect()->back();
    }

    public function delete_function($user_id)
    {
        $data = User::find($user_id);
        $data->delete();
        return back();
    }

    public function view($user_id)
    {
        $user = User::find($user_id);
        return view('hr.employee.viewemp', compact('user'));
    }

    public function edit($user_id)
    {
        $user = User::find($user_id);
        return view('hr.employee.edit', compact('user'));
    }

    public function update(Request $request, $user_id)
    {
        $user = User::find($user_id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'empNumber' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
        ]);

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

        $user->hourlyRate = $request['hourlyrate'];
        $user->role_as = $request['role_as'];

        if ($request->has('image')) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $user->image = $imageName;
        }

        $user->save();

        return redirect('hr/employee')->with('success', 'Employee information has been updated successfully');
    }

}
