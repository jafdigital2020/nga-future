<?php

namespace App\Http\Controllers\Manager;

use Exception;
use App\Models\User;
use App\Models\UserGeofence;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\GeofencingSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GeofencingController extends Controller
{
    public function geofencing()
    {
        $geofence = GeofencingSetting::all();

        return view('manager.geofencing.geofencing', compact('geofence'));
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
        
        } catch (Exception $e) {
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
        $users = Auth::user(); // Get the logged-in user
    
        // Fetch all geofence settings and distinct departments
        $geofences = GeofencingSetting::all();
        $departments = User::distinct()->pluck('department');
    
        // Get subordinates of the current user
        $subordinates = User::where('reporting_to', $users->id)->get();
    
        // Filter data for UserGeofences based on subordinates
        $data = UserGeofence::with(['user', 'geofenceSetting'])
            ->whereHas('user', function ($query) use ($users) {
                $query->where('reporting_to', $users->id);
            });
    
        // Apply department filter if provided
        $department = $request->input('department');
        if ($department) {
            $data->whereHas('user', function ($query) use ($department) {
                $query->where('department', 'like', "%$department%");
            });
        }
    
        // Apply geofence filter if provided
        $geofence = $request->input('earning');
        if ($geofence) {
            $data->whereHas('geofenceSetting', function ($query) use ($geofence) {
                $query->where('id', $geofence);
            });
        }
    
        // Apply name filter if provided
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
    
        return view('manager.geofencing.assigngeofencing', compact('users', 'geofences', 'userGeofences', 'departments', 'subordinates'));
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
