<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Location;
use App\Models\LeaveType;
use App\Models\UserGeofence;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Models\SettingsTheme;
use App\Models\SettingsCompany;
use App\Models\SettingsHoliday;
use Illuminate\Validation\Rule;
use App\Models\GeofencingSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
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
        $currentYear = now()->year;
    
        // Fetch holidays for the current year with associated users
        $holidays = SettingsHoliday::whereYear('holidayDate', $currentYear)->with('users')->get();
    
        // Fetch all users for adding to holidays
        $allUsers = User::all();
    
        $contractTypes = User::distinct()->pluck('typeOfContract');
        $departments = User::distinct()->pluck('department');
        $locations = Location::all();
    
        return view('admin.settings.holiday', compact('holidays', 'allUsers', 'contractTypes', 'locations', 'departments'));
    }
    
    public function filterEmployees(Request $request)
    {
        try {
            \Log::info('Filter Employees Request:', $request->all()); // Log request parameters
    
            $contractType = $request->query('contractType');
            $location = $request->query('location');
            $department = $request->query('department');
    
            $employees = User::query()
                ->when($contractType, fn($query) => $query->where('typeOfContract', $contractType))
                ->when($location, function ($query) use ($location) {
                    $query->whereExists(function ($subQuery) use ($location) {
                        $subQuery->select(DB::raw(1))
                            ->from('user_locations')
                            ->whereColumn('user_locations.user_id', 'users.id')
                            ->where('user_locations.location_id', $location);
                    });
                })
                ->when($department && $department !== 'all', fn($query) => $query->where('department', $department))
                ->get(['id', 'fName', 'lName']);
    
            \Log::info('Filtered Employees:', $employees->toArray()); // Log employees
    
            return response()->json($employees); // Return JSON response
        } catch (\Exception $e) {
            \Log::error('Error in filterEmployees: ' . $e->getMessage()); // Log errors
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
    
    public function holidayStore(Request $request)
    {
        try {
            // Validate inputs
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'holidayDate' => 'required|date',
                'holidayDay' => 'required|string',
                'type' => 'required|string|in:Regular,Special',
                'recurring' => 'nullable|in:on,0,1,true,false', // Adjusted validation for recurring
                'employees' => 'nullable|array',
                'employees.*' => 'integer|exists:users,id',
            ]);
    
            // Convert recurring to boolean
            $validatedData['recurring'] = $request->has('recurring');
    
            // Log the validated data
            \Log::info('Validated Data:', $validatedData);
    
            // Create the holiday
            $holiday = SettingsHoliday::create([
                'title' => $validatedData['title'],
                'holidayDate' => $validatedData['holidayDate'],
                'holidayDay' => $validatedData['holidayDay'],
                'type' => $validatedData['type'],
                'recurring' => $validatedData['recurring'],
            ]);
    
            \Log::info('Holiday Created Successfully:', $holiday->toArray());
    
            // Sync employees
            if ($request->filled('employees')) {
                $holiday->users()->sync($validatedData['employees']);
                \Log::info('Employees Synced:', $validatedData['employees']);
            }
    
            // Handle recurring holidays
            if ($validatedData['recurring']) {
                $this->handleRecurringHolidays($holiday);
            }
    
            Alert::success('Holiday Added', 'The holiday has been added successfully.');
            return redirect()->back();
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Errors:', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error Saving Holiday:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while saving the holiday.');
        }
    }
  
    /** Handle recurring holidays. **/
    protected function handleRecurringHolidays(SettingsHoliday $holiday)
    {
        // Determine the next 5 years for recurrence
        $currentYear = date('Y');
        for ($i = 1; $i <= 5; $i++) {
            $newYear = $currentYear + $i;
            $nextDate = date('Y-m-d', strtotime("$newYear-" . date('m-d', strtotime($holiday->holidayDate))));
    
            // Create the recurring holiday
            SettingsHoliday::updateOrCreate([
                'title' => $holiday->title,
                'holidayDate' => $nextDate,
            ], [
                'holidayDay' => date('l', strtotime($nextDate)),
                'type' => $holiday->type,
                'recurring' => true,
            ]);
        }
    }
    
    public function updateHoliday(Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('Update Holiday Request:', $request->all());
    
            // Validate inputs
            $validatedData = $request->validate([
                'id' => 'required|exists:settings_holidays,id',
                'title' => 'required|string|max:255',
                'holidayDate' => 'required|date',
                'holidayDay' => 'required|string',
                'type' => 'required|string|in:Regular,Special',
                'recurring' => 'nullable|in:on,0,1,true,false', // Adjust validation for recurring
            ]);
    
            // Convert "on" to boolean
            $validatedData['recurring'] = $request->has('recurring');
    
            // Log the validated data
            \Log::info('Validated Data:', $validatedData);
    
            // Find the holiday and update it
            $holiday = SettingsHoliday::findOrFail($validatedData['id']);
            $isUpdated = $holiday->update([
                'title' => $validatedData['title'],
                'holidayDate' => $validatedData['holidayDate'],
                'holidayDay' => $validatedData['holidayDay'],
                'type' => $validatedData['type'],
                'recurring' => $validatedData['recurring'],
            ]);
    
            // Log update status
            \Log::info('Holiday Update Status:', ['updated' => $isUpdated]);
    
            if ($isUpdated) {
                return redirect()->back()->with('success', 'Holiday updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to update the holiday.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Errors:', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error Updating Holiday:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while updating the holiday.');
        }
    }

    public function addUserToHoliday(Request $request, $holidayId)
    {
        \Log::info('Add User Request:', $request->all()); // Log the incoming request

        $request->validate([
            'users' => 'required|array',
            'users.*' => 'integer|exists:users,id',
        ]);
        
        $holiday = SettingsHoliday::findOrFail($holidayId);
        $holiday->users()->syncWithoutDetaching($request->input('users'));

        return redirect()->back()->with('success', 'Users added to holiday successfully!');
    }

    public function removeUserFromHoliday($holidayId, $userId)
    {
        $holiday = SettingsHoliday::findOrFail($holidayId);
        $holiday->users()->detach($userId);

        return redirect()->back()->with('success', 'User removed from holiday successfully!');
    }

    
    public function leaveType()
    {
        $ltype = LeaveType::all();

        return view('admin.settings.leavetype', compact('ltype'));
    }
    
    public function leaveTypeStore(Request $request)
    {
        try {

            // Validate the request data
            $request->validate([
                'leaveType' => 'required|string|max:255',
                'leaveDays' => 'required|integer|min:1',
                'restriction_days' => 'required|integer|min:1',
                'is_paid' => 'required|boolean',
            ]);

            $type = new LeaveType();
            $type->leaveType = $request->input('leaveType');
            $type->leaveDays = $request->input('leaveDays');
            $type->restriction_days = $request->input('restriction_days');
            $type->status = 'Active';
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
                'created_by' => Auth::user()->id,
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
            'edit_by' => Auth::user()->id,
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

    // ** LOCATON ** //

    public function location()
    {
        $locations = Location::all();

        return view('admin.settings.location', compact('locations'));
    }

    public function locationCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255|unique:locations,location_name',
            'location_address' => 'required|string|max:255|unique:locations,location_address',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errorMessages = implode(' ', $validator->errors()->all());
            return redirect()->back()->with('error', $errorMessages);
        }

        try {

        $location = new Location();
        $location->location_name = $request->input('location_name');
        $location->location_address = $request->input('location_address');
        $location->created_by = Auth::user()->id;
        $location->save();

        return redirect()->back()->with('success' , 'Location Added!');
        } catch(QueryException $e) {
            return redirect()->back()->with('error', 'Failed to add location. Please try again.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occured. Please try again');
        }
    }

    public function locationEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location_name' => 'required|string|max:255',
            'location_address' => 'required|string|max:255|unique:locations,location_address,' . $id,
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errorMessages = implode(' ', $validator->errors()->all());
            return redirect()->back()->with('error', $errorMessages);
        }

        try {
            
            $location = Location::findOrFail($id);
            $location->location_name = $request->input('location_name');
            $location->location_address = $request->input('location_address');
            $location->edit_by = Auth::user()->id;
            $location->save();

            return redirect()->back()->with('success', 'Location Edited!');
        } catch(QueryException $e) {
            return redirect()->back()->with('error', 'Failed to edit location. Please try again.');
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occured', 'Please try again');
        }
    }

    public function locationDelete ($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return redirect()->back()->with('success', 'Location deleted successfully!');
    }

    // ** ASSIGN LOCATION ** //

    public function locationAssign(Request $request)
    {
        $users = User::all();
        $locations = Location::all();
        $departments = User::distinct()->pluck('department');

        $data = UserLocation::with(['user', 'locationSetting']);

        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }

        $location = $request->input('earning');
        if ($location) {
            $data->whereHas('locationSetting', function ($query) use ($location) {
                $query->where('id', $location); 
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

        $userLocations = $data->get();

        return view('admin.settings.assignlocation', compact('users', 'locations', 'userLocations', 'departments'));;
    }

    public function storeUserLocation(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|array',
            'location_id' => 'required|array',
        ], [
            'user_id.required' => 'Please select at least one user.',
            'location_id.required' => 'Please select at least one location.',
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

                // Loop through the locations for each employee
                foreach ($request->location_id as $key => $locationId) {
                    // Fetch the locations details
                    $location = Location::findOrFail($locationId); // Ensure location exists
                    $locationName = $location->location_name;

                    // Check if this location already exists for the user
                    $existingLocation = UserLocation::where('user_id', $userId)
                        ->where('location_id', $locationId)
                        ->first();

                    // If the geofence already exists, display a user-friendly error message
                    if ($existingLocation) {
                        return redirect()->back()->with('error', "The location '$locationName' is already assigned to user $userFullName.");
                    }

                    // Create a new UserLocation record if it doesn't already exist
                    UserLocation::create([
                        'user_id' => $userId, // The employee ID
                        'location_id' => $locationId, // The location ID (foreign key)
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Location successfully assigned!');

        } catch (Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error assigning location: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while assigning location. Please try again.');
        }
    }

}
