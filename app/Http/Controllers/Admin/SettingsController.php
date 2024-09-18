<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\SettingsTheme;
use App\Models\SettingsCompany;
use App\Models\SettingsHoliday;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class SettingsController extends Controller
{
    public function company()
    {
        $company = SettingsCompany::first();

        return view('admin.settings.company', compact('company'));
    }

    public function companyStore(Request $request)
    {
        $company = SettingsCompany::first();

        if ($company) {

            $company->company = $request->input('company');
            $company->contactPerson = $request->input('contactPerson');
            $company->comAddress = $request->input('comAddress');
            $company->country = $request->input('country');
            $company->province = $request->input('province');
            $company->city = $request->input('city');
            $company->postalCode = $request->input('postalCode');
            $company->comEmail = $request->input('comEmail');
            $company->comPhone = $request->input('comPhone');
            $company->comMobile = $request->input('comMobile');
            $company->comFax = $request->input('comFax');
            $company->comWebsite = $request->input('comWebsite');

            $company->save();

            Alert::success('Company Settings Updated');
        } else {

            $company = new SettingsCompany();
            $company->company = $request->input('company');
            $company->contactPerson = $request->input('contactPerson');
            $company->comAddress = $request->input('comAddress');
            $company->country = $request->input('country');
            $company->province = $request->input('province');
            $company->city = $request->input('city');
            $company->postalCode = $request->input('postalCode');
            $company->comEmail = $request->input('comEmail');
            $company->comPhone = $request->input('comPhone');
            $company->comMobile = $request->input('comMobile');
            $company->comFax = $request->input('comFax');
            $company->comWebsite = $request->input('comWebsite');

            $company->save();

            Alert::success('Company Settings Added');
        }

        return redirect()->back();
    }

    public function theme ()
    {
        $theme = SettingsTheme::first();

        return view('admin.settings.theme', compact('theme'));
    }

    public function themeStore(Request $request)
    {
        $theme = SettingsTheme::first();


        if ($theme) {
           
            $theme->webName = $request->input('webName');

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $imageName = time() . '.' . $file->extension();
                $destinationPath = public_path('images');
                $file->move($destinationPath, $imageName);
                $theme->logo = $imageName;
            } else {
                $theme->logo = ''; 
            }

            if ($request->hasFile('favicon')) {
                $file2 = $request->file('favicon'); // Different variable name for favicon
                $imageName2 = time() . '_favicon.' . $file2->extension(); // Unique name for favicon
                $destinationPath2 = public_path('images');
                $file2->move($destinationPath2, $imageName2);
                $theme->favicon = $imageName2;
            } else {
                $theme->favicon = ''; 
            }

            $theme->webName = $request->input('webName');
            $theme->save();

            Alert::success('Updated Successfully');
        } else {

            $theme = new SettingsTheme();

            
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $imageName = time() . '.' . $file->extension();
                $destinationPath = public_path('images');
                $file->move($destinationPath, $imageName);
            
                $theme->logo = $imageName;
            } else {
                $theme->logo = ''; 
            }

            if ($request->hasFile('favicon')) {
                $file2 = $request->file('favicon'); 
                $imageName2 = time() . '_favicon.' . $file2->extension(); 
                $destinationPath2 = public_path('images');
                $file2->move($destinationPath2, $imageName2);
                $theme->favicon = $imageName2;
            } else {
                $theme->favicon = ''; 
            }
           
            $theme->webName = $request->input('webName');
            $theme->save();

            Alert::success('Added Successfully');
        }

        return redirect()->back();
    }

    public function password()
    {
        return view('admin.settings.password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);
    
        if (Hash::check($request->current_password, auth()->user()->password)) {
            $this->updatePassword(auth()->user(), $request->password);
    
            // Manually re-authenticate the user to prevent logout
            // auth()->login(auth()->user());
    
            Alert::success('Password changed successfully');
        } else {
            Alert::error('Current password does not match. Please try again.');
        }
    
        return redirect()->back();
    }
    
    private function updatePassword($user, $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function holiday()
    {
        $holiday = SettingsHoliday::all();

        return view('admin.settings.holiday', compact('holiday'));
    }

    public function holidayStore(Request $request)
    {
        $holiday = new SettingsHoliday();

        $holiday->title = $request->input('title');
        $holiday->holidayDate = $request->input('holidayDate');
        $holiday->holidayDay = $request->input('holidayDay');
        $holiday->type = $request->input('type');
        $holiday->save();

        Alert::success('Holiday Added');
        return redirect()->back();
    }

    public function leaveType()
    {
        $ltype = LeaveType::all();

        return view('admin.settings.leavetype', compact('ltype'));
    }
    
    
}
