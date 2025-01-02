<?php

namespace App\Http\Controllers\Hr;

use Log;
use Exception;
use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\SettingsHoliday;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
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

        return view('hr.settings.holiday', compact('holidays', 'allUsers', 'contractTypes', 'locations', 'departments'));
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
        } catch (ValidationException $e) {
            \Log::error('Validation Errors:', $e->errors());
            return redirect()->back()->withErrors($e->errors());
        } catch (Exception $e) {
            \Log::error('Error Updating Holiday:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while updating the holiday.');
        }
    }

    public function addUserToHoliday(Request $request, $holidayId)
    {
        Log::info('Add User Request:', $request->all()); // Log the incoming request

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

    public function destroy($id)
    {
        // Find the holiday by ID
        $holiday = SettingsHoliday::findOrFail($id);

        // Delete the holiday
        $holiday->delete();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Holiday deleted successfully.');
    }
}
