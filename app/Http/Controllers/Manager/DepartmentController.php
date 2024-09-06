<?php

namespace App\Http\Controllers\Manager;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use App\Models\EmployeeSalary;
use App\Models\EmploymentRecord;
use App\Models\EmployementSalary;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class DepartmentController extends Controller
{
    public function index()
    {
        $loggedInUser = auth()->user();
    
        $users = User::where('reporting_to', $loggedInUser->id)->get();
    
        return view('manager.department', compact('users'));
    }
    

    public function edit($user_id)
    {
        $user = User::with('employmentRecord','employmentSalary')->findOrFail($user_id);

        $department = $user->department;
        $supervisor = $user->supervisor;

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

            $info->save();

            Alert::success('Personal Infromation Added');
        }

        return redirect()->back();
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

}
