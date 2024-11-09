<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\UserGeofence;
use Illuminate\Http\Request;
use App\Models\SettingsTheme;
use App\Models\SettingsCompany;
use App\Models\SettingsHoliday;
use Illuminate\Validation\Rule;
use App\Models\GeofencingSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    
    public function leaveTypeStore(Request $request)
    {
        try {
            $type = new LeaveType();
            $type->leaveType = $request->input('leaveType');
            $type->leaveDays = $request->input('leaveDays');
            $type->status = $request->input('status');
            $type->is_paid = $request->input('is_paid');

            $type->save();
    
            return back()->with('success', 'Leave Type Added.');
        } catch (\Exception $e) {

            return back()->withErrors(['error' => 'Failed to add Leave Type: ' . $e->getMessage()]);
        }
    }

    public function leaveTypeEdit(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'leaveType' => 'required|string|max:255',
                'leaveDays' => 'required|integer|min:1',
                'is_paid' => 'required|boolean',
            ]);
    
            // Find the leave type by ID
            $leave = LeaveType::findOrFail($id);
    
            // Update the leave type details
            $leave->leaveType = $request->input('leaveType');
            $leave->leaveDays = $request->input('leaveDays');
            $leave->is_paid = $request->input('is_paid');
    
            // Save the changes
            $leave->save();
    
            // Redirect back with a success message
            return redirect()->back()->with('success', 'Edit Successfully!');
        } catch (ModelNotFoundException $e) {
            // Handle the case where the model was not found
            return redirect()->back()->with('error', 'Leave type not found.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Handle any other exceptions
            return redirect()->back()->with('error', 'An error occurred while updating the leave type.');
        }
    }

    public function leaveTypeDelete($id)
    {
        $leave = LeaveType::findOrFail($id);

        $leave->delete();

        return redirect()->back()->with('success', 'Leave type deleted sucessfully!');
    }
    
    // ** GEOFENCING ** //
    
    public function geofencing()
    {
        $geofence = GeofencingSetting::all();

        return view('admin.settings.geofencing', compact('geofence'));
    }

    public function createGeofence(Request $request)
    {
        // Custom validation rules
        $rules = [
            'fencing_name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('geofencing_settings', 'fencing_name') // Ensures unique geofence name
            ],
            'fencing_address' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('geofencing_settings', 'fencing_address') // Ensures unique geofence address
            ],
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'fencing_radius' => 'required|integer|min:1',
        ];
    
        // Custom error messages
        $messages = [
            'fencing_name.unique' => 'A geofence with this name already exists. Please choose a different name.',
            'fencing_address.unique' => 'A geofence with this address already exists. Please choose a different address.',
        ];
    
        // Create the validator instance
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Check if validation fails
        if ($validator->fails()) {
            // Get all error messages as a single string
            $errorMessages = implode('<br>', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
        }
    
        try {
            // Attempt to create a new geofence record
            GeofencingSetting::create([
                'fencing_name' => $request->fencing_name,
                'fencing_address' => $request->fencing_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'fencing_radius' => $request->fencing_radius,
            ]);
    
            // Redirect back with a success message if creation succeeds
            return redirect()->back()->with('success', 'Geofence created successfully!');
        
        } catch (\Exception $e) {
            // Log the error message for debugging purposes
            Log::error('Failed to create geofence: ' . $e->getMessage());
    
            // Redirect back with an error message
            return redirect()->back()->with('error', 'An error occurred while creating the geofence. Please try again.');
        }
    }

    public function updateGeofence(Request $request, $id)
    {
        $geofence = GeofencingSetting::findOrFail($id);
        $request->validate([
            'fencing_namee' => 'required|string|max:255',
            'fencing_addresse' => 'required|string|max:255',
            'latitudee' => 'required|numeric',
            'longitudee' => 'required|numeric',
            'fencing_radiuse' => 'required|integer|min:1',
        ]);

        $geofence->update([
            'fencing_name' => $request->fencing_namee,
            'fencing_address' => $request->fencing_addresse,
            'latitude' => $request->latitudee,
            'longitude' => $request->longitudee,
            'fencing_radius' => $request->fencing_radiuse,
        ]);

        return redirect()->back()->with('success', 'Geofence updated successfully!');
    }

    public function destroyGeofence($id)
    {
        try {
            $geofence = GeofencingSetting::findOrFail($id);
    
            $geofence->delete();

            return redirect()->back()->with('success', 'Deleted Successfully!');
            
        } catch (ModelNotFoundException $e) {

            return redirect()->back()->with('error', 'Geofence not found.');
            
        } catch (Exception $e) {
       
            return redirect()->back()->with('error', 'An error occurred while deleting the geofence.');
        }
    }
    
    // ** Assign Geofencing ** //

    public function geofenceAssign(Request $request)
    {
        $users = User::all();
        $geofences = GeofencingSetting::all();
        $departments = User::distinct()->pluck('department');

        $data = UserGeofence::with(['user', 'geofenceSetting']);

        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }

        $geofence = $request->input('earning');
        if ($geofence) {
            $data->whereHas('geofenceSetting', function ($query) use ($geofence) {
                $query->where('id', $geofence); 
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

        $userGeofences = $data->get();

        return view('admin.settings.assigngeofencing', compact('users', 'geofences', 'userGeofences', 'departments'));;
    }
    
    public function getEmployeesByDepartmentGeofence(Request $request)
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

    public function storeUserGeofence(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|array',
            'geofence_id' => 'required|array',
        ], [
            'user_id.required' => 'Please select at least one user.',
            'geofence_id.required' => 'Please select at least one geofence.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errorMessages = implode('<br>', $validator->errors()->all());
            return redirect()->back()->withErrors($validator)->with('error', $errorMessages);
        }

        try {
            // Loop through each selected employee
            foreach ($request->user_id as $userId) {
                $user = User::findOrFail($userId); // Ensure user exists
                $userFullName = $user->fName . ' ' . $user->lName;

                // Loop through the geofences for each employee
                foreach ($request->geofence_id as $key => $geofenceId) {
                    // Fetch the geofence details
                    $geofence = GeofencingSetting::findOrFail($geofenceId); // Ensure geofence exists
                    $geofenceName = $geofence->fencing_name;

                    // Check if this geofence already exists for the user
                    $existingGeofence = UserGeofence::where('user_id', $userId)
                        ->where('geofence_id', $geofenceId)
                        ->first();

                    // If the geofence already exists, display a user-friendly error message
                    if ($existingGeofence) {
                        return redirect()->back()->with('error', "The geofence '$geofenceName' is already assigned to user $userFullName.");
                    }

                    // Create a new UserGeofence record if it doesn't already exist
                    UserGeofence::create([
                        'user_id' => $userId, // The employee ID
                        'geofence_id' => $geofenceId, // The geofence ID (foreign key)
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Geofences successfully assigned!');

        } catch (Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error assigning geofences: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while assigning geofences. Please try again.');
        }
    }

    public function deleteUserGeofence($id)
    {
        try {
            $UserGeofence = UserGeofence::findOrFail($id); 
            $UserGeofence->delete();

            return response()->json(['success' => true, 'message' => 'Geofence deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete geofence: ' . $e->getMessage()]);
        }
    }


}
