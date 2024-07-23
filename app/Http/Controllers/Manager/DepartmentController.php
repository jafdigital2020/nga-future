<?php

namespace App\Http\Controllers\Manager;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmploymentRecord;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use App\Models\EmployementSalary;
use RealRashid\SweetAlert\Facades\Alert;

class DepartmentController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();
        $departments = [];

        switch ($loggedInUser->role_as) {
            case User::ROLE_IT_MANAGER:
                $departments = ['IT', 'Website Development'];
                break;
            case User::ROLE_MARKETING_MANAGER:
                $departments = ['Marketing'];
                break;
            case User::ROLE_OPERATIONS_MANAGER:
                $departments = ['SEO', 'Content'];
                break;
            default:
                // Handle other roles or return an error
                return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::getUsersByDepartments($departments);

           
        return view ('manager.department', compact('users','departments'));
    }

    public function edit($user_id)
    {
        $user = User::with('employmentRecord','employmentSalary')->findOrFail($user_id);

        $department = $user->department;
        $supervisor = User::getSupervisorForDepartment($department, $user);

        return view('manager.record', compact('user','supervisor'));
    }

    public function employmentStore(Request $request, $user_id)
    {
        $user = User::with('employmentRecord')->findOrFail($user_id);

        $employment = EmploymentRecord::where('users_id', $user->id)->first();

        if ($employment) {

            $employment->hiredDate = $request->input('eHired');
            $employment->supervisor = $request->input('iSupervisor');
            $employment->jobTitle = $request->input('ePosition');
            $employment->department = $request->input('eDepartment');
            $employment->location = $request->input('location');

            $employment->save();

            //User Database Update also
            $user->dateHired = $employment->hiredDate;
            $user->position = $employment->jobTitle;
            $user->department = $employment->department;

            $user->save();

            Alert::success('Employment Record Updated');

        } else {

            $employment = new EmploymentRecord();
            $employment->users_id = $user_id;
            $employment->name = $user->name;
            $employment->hiredDate = $request->input('eHired');
            $employment->supervisor = $request->input('iSupervisor');
            $employment->jobTitle = $request->input('ePosition');
            $employment->department = $request->input('eDepartment');
            $employment->location = $request->input('location');

            $employment->save();

            Alert::success('Employment Record Added');
        }

        return redirect()->back();
    }

    public function employmentSalaryStore(Request $request, $user_id)
    {
        $user = User::with('employmentSalary')->findOrFail($user_id);

        $esalary =  new EmployementSalary();

        $esalary->users_id = $user_id;
        $esalary->name = $user->name;
        $esalary->annSalary = $request->input('annSalary');
        $esalary->salFreqMonthly = $request->input('salFreqMonthly');
        $esalary->salRate = $request->input('salRate');
        $esalary->currency = $request->input('currency');
        $esalary->proposalReason = $request->input('proposalReason');
        $esalary->proBy = auth()->user()->name;

        $esalary->save();

        Alert::success('Employment Salary Record Added');

        return redirect()->back();
    }

}
