<?php

namespace App\Http\Controllers\Manager;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ShiftController extends Controller
{
    public function shiftDaily(Request $request)
    {
        $user = auth()->user(); // Get the authenticated user
    
        // Default to the current week if no custom date is selected
        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : Carbon::now()->startOfWeek();
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : $startDate->copy()->endOfWeek(); // Show 1 week default
        $department = $request->input('department');
        $name = trim($request->input('name'));
        
        // Generate an array of Carbon date instances based on the selected range
        $dates = collect([]);
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates->push($date);
        }
        
        // Get distinct departments for filtering
        $departments = User::distinct()->pluck('department');
        
        // Initialize the query to fetch users and their schedules
        $data = User::with(['shiftSchedule' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        }])
        ->where('reporting_to', $user->id); // Only include subordinates of the authenticated user
        
        // Apply department filter if provided
        if ($department) {
            $data->where('department', 'like', "%$department%");
        }
    
        // Apply name filtering if provided
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
    
        // Execute the query and get the users
        $users = $data->get(); // Ensure this is called after all where conditions
    
        return view('manager.shiftschedule.daily', compact('dates', 'users', 'startDate', 'endDate', 'departments'));
    }
    

        // Shift List

        public function storeShift(Request $request)
        {
            try {
                // Validate the request data before creating the shift
                $request->validate([
                    'shift_name' => 'required|string|max:255',
                    'start_time' => 'required|date_format:H:i',
                    'late_threshold' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i',
                    'break_time' => 'nullable|integer',
                    'recurring' => 'nullable|boolean',
                    'repeat_every' => 'nullable|integer',
                    'days' => 'nullable|array',
                    'end_on' => 'nullable|date',
                    'indefinite' => 'nullable|boolean',
                    'tag' => 'nullable|string|max:255',
                    'note' => 'nullable|string|max:500',
                ]);
        
                // Create the shift
                Shift::create([
                    'shift_name' => $request->shift_name,
                    'start_time' => $request->start_time,
                    'late_threshold' => $request->late_threshold,
                    'end_time' => $request->end_time,
                    'break_time' => $request->break_time,
                    'recurring' => $request->recurring ? true : false,
                    'repeat_every' => $request->repeat_every,
                    'days' => $request->days,
                    'end_on' => $request->end_on,
                    'indefinite' => $request->indefinite ? true : false,
                    'tag' => $request->tag,
                    'note' => $request->note,
                ]);
        
                return redirect()->back()->with('success', 'Shift added successfully!');
            
            } catch (QueryException $e) {
                // Handle database errors
                return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
            
            } catch (ValidationException $e) {
                // Handle validation errors
                return redirect()->back()->withErrors($e->validator)->withInput();
        
            } catch (Exception $e) {
                // Handle any other errors
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    
        public function getEmployeesByDepartment(Request $request)
        {
            // Validate department input
            $department = $request->input('department');
            if (!$department) {
                return response()->json([], 400); 
            }
    
            $employees = User::where('department', $department)->get(['id', 'name']);
    
            return response()->json($employees);
        }
    
        public function assignScheduleList(Request $request)
        {
            try {
    
                foreach($request->users_id as $userId) {
                    ShiftSchedule::create([
                        'users_id' => $userId,
                        'shift_id' => $request->input('shift_id'),
                    ]);
                }
    
                return redirect()->back()->with('success', 'Shifts added successfully for selected users!');
            } catch (QueryException $e) {
                return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
            } catch (ValidationException $e) {
                return redirect()->back()->withErrors($e->validator)->withInput();
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    
        // Shift Daily
    
        public function assignSchedule(Request $request)
        {
            try {
                // Check if the recurring option is selected
                $isRecurring = $request->recurring ? true : false;
                
                // Get selected weekdays from checkboxes
                $selectedDays = $request->input('days'); // e.g., ['monday', 'tuesday', 'wednesday']
                
                // Loop through each user
                foreach ($request->users_id as $userId) {
                    // Retrieve the user's first and last name
                    $user = User::find($userId);
                    $userFullName = $user->fName . ' ' . $user->lName;
            
                    if ($selectedDays && !$isRecurring) {
                        // If specific weekdays are selected (e.g., Monday, Tuesday, Wednesday)
                        // Define a start date and end date or number of days (for example, assign for 30 days)
                        $startDate = Carbon::now();
                        $endDate = $startDate->copy()->addDays(30); // Assign for the next 30 days
        
                        // Loop through the range of dates
                        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                            // Get the day of the week for the current date
                            $dayOfWeek = strtolower($date->format('l')); // Get day of the week in lowercase (e.g., 'monday')
        
                            // If the current day of the week is in the selected days, create a schedule
                            if (in_array($dayOfWeek, $selectedDays)) {
                                $formattedDate = $date->format('Y-m-d');
                                
                                // Check if shift schedule already exists for the user on the date
                                $existingSchedule = ShiftSchedule::where('users_id', $userId)
                                    ->where('date', $formattedDate)
                                    ->first();
        
                                if ($existingSchedule) {
                                    continue; // Skip this date if the schedule already exists
                                }
        
                                // Proceed to create shift schedule if no existing schedule found
                                if ($request->flexibleTime) {
                                    // Create a flexible shift schedule
                                    ShiftSchedule::create([
                                        'users_id' => $userId,
                                        'shiftStart' => null,
                                        'lateThreshold' => null,
                                        'shiftEnd' => null,
                                        'isFlexibleTime' => true,
                                        'date' => $formattedDate,
                                        'break_time' => $request->break_time,
                                        'allowedHours' => $request->allowedHours,
                                        'shift_id' => $request->input('shift_id'), 
                                        'recurring' => false, // Not recurring
                                    ]);
                                } else {
                                    // Parse non-flexible shift times
                                    $shiftStart = Carbon::parse($request->shiftStart)->format('H:i:s');
                                    $lateThreshold = Carbon::parse($request->lateThreshold)->format('H:i:s');
                                    $shiftEnd = Carbon::parse($request->shiftEnd)->format('H:i:s');
        
                                    // Create a non-flexible shift schedule
                                    ShiftSchedule::create([
                                        'users_id' => $userId,
                                        'shiftStart' => $shiftStart,
                                        'lateThreshold' => $lateThreshold,
                                        'shiftEnd' => $shiftEnd,
                                        'date' => $formattedDate,
                                        'break_time' => $request->break_time,
                                        'allowedHours' => $request->allowedHours,
                                        'shift_id' => $request->input('shift_id'),  
                                        'isFlexibleTime' => false,
                                        'recurring' => false, // Not recurring
                                    ]);
                                }
                            }
                        }
                    } else {
                        // If not recurring and no specific weekdays selected, use the provided dates
                        $dates = explode(',', $request->input('dates')[0]);
        
                        // Loop through each date
                        foreach ($dates as $date) {
                            try {
                                $formattedDate = Carbon::createFromFormat('Y-m-d', trim($date))->format('Y-m-d');
                            } catch (\Exception $e) {
                                return redirect()->back()->with('error', 'Invalid date format: ' . $date);
                            }
        
                            // Check if shift schedule already exists for the user on the date
                            $existingSchedule = ShiftSchedule::where('users_id', $userId)
                                ->where('date', $formattedDate)
                                ->first();
        
                            if ($existingSchedule) {
                                return redirect()->back()->with('error', 'Shift schedule already exists for ' . $userFullName );
                            }
        
                            // Proceed to create shift schedule if no existing schedule found
                            if ($request->flexibleTime) {
                                // Create a flexible shift schedule
                                ShiftSchedule::create([
                                    'users_id' => $userId,
                                    'shiftStart' => null,
                                    'lateThreshold' => null,
                                    'shiftEnd' => null,
                                    'isFlexibleTime' => true,
                                    'date' => $formattedDate,
                                    'break_time' => $request->break_time,
                                    'allowedHours' => $request->allowedHours,
                                    'shift_id' => $request->input('shift_id'), 
                                    'recurring' => false,
                                    'selected_days' => json_encode($selectedDays),
                                ]);
                            } else {
                                // Parse non-flexible shift times
                                $shiftStart = Carbon::parse($request->shiftStart)->format('H:i:s');
                                $lateThreshold = Carbon::parse($request->lateThreshold)->format('H:i:s');
                                $shiftEnd = Carbon::parse($request->shiftEnd)->format('H:i:s');
        
                                // Create a non-flexible shift schedule
                                ShiftSchedule::create([
                                    'users_id' => $userId,
                                    'shiftStart' => $shiftStart,
                                    'lateThreshold' => $lateThreshold,
                                    'shiftEnd' => $shiftEnd,
                                    'date' => $formattedDate,
                                    'break_time' => $request->break_time,
                                    'allowedHours' => $request->allowedHours,
                                    'shift_id' => $request->input('shift_id'),  
                                    'isFlexibleTime' => false,
                                    'recurring' => false,
                                    'selected_days' => json_encode($selectedDays),
                                ]);
                            }
                        }
                    }
                }
        
                return redirect()->back()->with('success', 'Shifts added successfully for selected users!');
            } catch (QueryException $e) {
                return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
            } catch (ValidationException $e) {
                return redirect()->back()->withErrors($e->validator)->withInput();
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
        
        
        public function dailyScheduling(Request $request)
        {
            try {
                    if ($request->flexibleTime) {
                        $shift = new ShiftSchedule();
                        $shift->users_id = $request->users_id;
                        $shift->shiftStart = null;
                        $shift->lateThreshold = null;
                        $shift->shiftEnd = null;
                        $shift->isFlexibleTime = true;
                        $shift->date = $request->date; 
                        $shift->break_time = $request->break_time;
                        $shift->allowedHours = $request->allowedHours;
                        $shift->shift_id = $request->shift_id; 
                        $shift->save();
    
                        return redirect()->back()->with('success', 'Added Schedule with Flexible Time');
                    } else {
    
                        $shiftStart = Carbon::parse($request->input('shiftStart'))->format('H:i:s');
                        $lateThreshold = Carbon::parse($request->input('lateThreshold'))->format('H:i:s');
                        $shiftEnd = Carbon::parse($request->input('shiftEnd'))->format('H:i:s');
    
                        // Create new shift schedule with specific times
                        ShiftSchedule::create([
                            'users_id' => $request->users_id,
                            'shiftStart' => $shiftStart,
                            'lateThreshold' => $lateThreshold,
                            'shiftEnd' => $shiftEnd,
                            'break_time' => $request->break_time,
                            'allowedHours' => $request->allowedHours,
                            'date' => $request->date,
                            'shift_id' => $request->shift_id,
                            'isFlexibleTime' => false, 
                        ]);
    
                        return redirect()->back()->with('success', 'Shift added successfully!');
                    }
    
                        return redirect()->back()->with('success', 'Shift added successfully!');
                    } catch (QueryException $e) {
                        // Handle database errors
                        return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
                    
                    } catch (ValidationException $e) {
                        // Handle validation errors
                        return redirect()->back()->withErrors($e->validator)->withInput();
                
                    } catch (Exception $e) {
                        // Handle any other errors
                        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
                }
        }
    
        public function dailySchedulingEdit (Request $request, $id)
        {
            try {
                // Find the shift schedule by ID
                $shift = ShiftSchedule::findOrFail($id);
        
                // Update the shift schedule with the form inputs
                $shift->shiftStart = $request->shiftStart ? Carbon::parse($request->shiftStart)->format('H:i:s') : null;
                $shift->lateThreshold = $request->lateThreshold ? Carbon::parse($request->lateThreshold)->format('H:i:s') : null;
                $shift->shiftEnd = $request->shiftEnd ? Carbon::parse($request->shiftEnd)->format('H:i:s') : null;
                $shift->break_time = $request->break_time;
                $shift->allowedHours = $request->allowedHours;
                $shift->date = $request->date;
                $shift->isFlexibleTime = $request->flexibleTime ? true : false;
        
                // Save the updated shift schedule
                $shift->save();
        
                return redirect()->back()->with('success', 'Shift updated successfully!');
            } catch (QueryException $e) {
                // Handle database errors
                return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
            } catch (ValidationException $e) {
                // Handle validation errors
                return redirect()->back()->withErrors($e->validator)->withInput();
            } catch (Exception $e) {
                // Handle any other errors
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
}
