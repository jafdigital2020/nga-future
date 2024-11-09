<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OvertimeCredits;
use App\Models\OvertimeRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;
use App\Notifications\OvertimeRequestSubmitted;

class OvertimeController extends Controller
{
    public function overtimeIndex()
    {
        $user= Auth::user()->id;
        $otCredits = OvertimeCredits::where('users_id', $user)->first();
        $overtime = OvertimeRequest::where('users_id', $user)->get();
        // Pending Count
        $pendingCount = OvertimeRequest::where('users_id', $user)->where('status', 'Pending')->count();
        // Rejected Count
        $rejectedCount = OvertimeRequest::where('users_id', $user)->where('status', 'Rejected')->count();
        // Approved Total Hours for the Month
        $approvedHours = OvertimeRequest::where('users_id', $user)
        ->where('status', 'Approved')
        ->whereMonth('date', Carbon::now()->month)
        ->get()
        ->sum(function ($overtime) {
            list($hours, $minutes, $seconds) = explode(':', $overtime->total_hours);

            $totalHours = (int)$hours + ((int)$minutes / 60);
            

            return round($totalHours * 2) / 2; 
        });

        if ($otCredits) {
            $timeParts = explode(':', $otCredits->otCredits);
            $hours = (int)$timeParts[0];
            $minutes = (int)$timeParts[1];
        
            // Convert minutes to decimal
            $decimalHours = $hours + ($minutes / 60);
        } else {
            $decimalHours = 0; 
        }
  
     
        $totalRequestsThisMonth = OvertimeRequest::where('users_id', $user)
        ->where('status', 'Approved')
        ->whereMonth('date', Carbon::now()->month)
        ->count(); 

        return view('emp.overtime.index', compact('overtime', 'pendingCount', 'rejectedCount', 'approvedHours', 'totalRequestsThisMonth', 'otCredits', 'decimalHours'));
    }


        public function overtimeRequest(Request $request)
        {
            try {
                Log::info("Starting overtime request submission", [
                    'user_id' => Auth::id(),
                    'input_data' => $request->all()
                ]);
    
                // Validate the request, including file upload
                $validated = $request->validate([
                    'date' => 'required|date|after:today',
                    'reason' => 'required|string',
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i|after:start_time',
                    'total_hours' => 'required|date_format:H:i:s',
                    'attached_file' => 'nullable|file|mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
                ]);
    
                Log::info("Validation successful");
    
                // Convert total_hours to seconds
                $newRequestHours = Carbon::createFromFormat('H:i:s', $request->total_hours)->secondsSinceMidnight();
    
                // Get existing overtime requests for the same date by the user
                $user = Auth::user();
                $existingOvertimeRequests = OvertimeRequest::where('users_id', $user->id)
                    ->where('date', $request->date)
                    ->get();
    
                // Calculate the total hours already requested for that day
                $existingHours = $existingOvertimeRequests->reduce(function ($carry, $otRequest) {
                    return $carry + Carbon::createFromFormat('H:i:s', $otRequest->total_hours)->secondsSinceMidnight();
                }, 0);
    
                // Define the 4-hour limit in seconds
                $fourHours = 4 * 3600;
    
                // Check if the total of existing hours and the new request exceeds 4 hours
                if (($existingHours + $newRequestHours) > $fourHours) {
                    $existingHoursFormatted = gmdate("H:i:s", $existingHours);
                    $newRequestHoursFormatted = gmdate("H:i:s", $newRequestHours);
                    Log::warning("Overtime request exceeds daily 4-hour limit", [
                        'user_id' => $user->id,
                        'existing_hours' => $existingHoursFormatted,
                        'new_request_hours' => $newRequestHoursFormatted,
                        'total_hours' => gmdate("H:i:s", $existingHours + $newRequestHours)
                    ]);
                    return redirect()->back()->with(['error' => 'Overtime request cannot exceed 4 hours per day.']);
                }
    
                // Check for sufficient overtime credits
                $overtimeCredits = OvertimeCredits::where('users_id', $user->id)->first();
    
                if (!$overtimeCredits || $overtimeCredits->otCredits == '00:00:00') {
                    Log::warning("No overtime credits remaining", ['user_id' => $user->id]);
                    return redirect()->back()->with(['error' => 'You have no overtime credits remaining.']);
                }
    
                // Calculate and compare overtime hours with available credits
                $total_hours_seconds = Carbon::createFromFormat('H:i:s', $request->input('total_hours'))->secondsSinceMidnight();
                $otCredits_seconds = Carbon::createFromFormat('H:i:s', $overtimeCredits->otCredits)->secondsSinceMidnight();
    
                if ($otCredits_seconds < $total_hours_seconds) {
                    Log::warning("Insufficient overtime credits", [
                        'user_id' => $user->id,
                        'requested_hours' => $total_hours_seconds,
                        'available_credits' => $otCredits_seconds,
                    ]);
                    return redirect()->back()->with(['error' => 'You do not have enough overtime credits to cover this request.']);
                }
    
                // Save the overtime request
                $overtime = new OvertimeRequest();
                $overtime->users_id = $user->id;
                $overtime->date = $request->input('date');
                $overtime->start_time = $request->input('start_time');
                $overtime->end_time = $request->input('end_time');
                $overtime->total_hours = $request->input('total_hours');
                $overtime->reason = $request->input('reason');
    
                // Handle file upload if it exists
                if ($request->hasFile('attached_file')) {
                    $file = $request->file('attached_file');
                    $filePath = $file->store('overtime_attachments', 'public');
                    $overtime->attached_file = $filePath;
                    Log::info("File successfully stored", ['file_path' => $filePath]);
                }
    
                $overtime->save();
                Log::info("Overtime request saved successfully", ['overtime_id' => $overtime->id]);
    
                // Notify supervisors, HR, and Admin users
                $supervisor = $user->supervisor;
                $hrUsers = User::where('role_as', User::ROLE_HR)->get();
                $adminUsers = User::where('role_as', User::ROLE_ADMIN)->get();
    
                $notifiableUsers = collect([$supervisor])
                    ->merge($hrUsers)
                    ->merge($adminUsers)
                    ->unique('id')
                    ->filter();
    
                foreach ($notifiableUsers as $notifiableUser) {
                    $notifiableUser->notify(new OvertimeRequestSubmitted($overtime, $user));
                }
    
                Log::info("Overtime request notifications sent");
    
                return redirect()->back()->with('success', 'Overtime request submitted successfully.');
    
            } catch (ValidationException $e) {
                foreach ($e->validator->errors()->all() as $error) {
                    Alert::error('Validation Error', $error);
                    Log::error("Validation error in overtime request", ['error' => $error]);
                }
                return redirect()->back()->withInput();
            } catch (\Exception $e) {
                Log::error("An error occurred while submitting the overtime request", [
                    'error_message' => $e->getMessage(),
                    'user_id' => Auth::id(),
                ]);
                Alert::error('An error occurred while submitting your overtime request.');
                return redirect()->back();
            }
        }

    
        public function updateOT(Request $request, $id)
        {
            try {
                // Find the existing overtime request
                $overtime = OvertimeRequest::findOrFail($id);
                $user = Auth::user();
        
                // Validate the request
                $validated = $request->validate([
                    'datee' => 'required|date|after:today',
                    'start_timee' => 'required|date_format:H:i',
                    'end_timee' => 'required|date_format:H:i|after:start_timee',
                    'total_hourse' => 'required|date_format:H:i:s',
                    'attached_file' => 'nullable|file|mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
                ]);
        
                // Convert total_hours from the new request to seconds
                $newRequestHours = Carbon::createFromFormat('H:i:s', $request->total_hourse)->secondsSinceMidnight();
        
                // Retrieve existing overtime requests for the same date by the user, excluding the current request
                $existingOvertimeRequests = OvertimeRequest::where('users_id', $user->id)
                    ->where('date', $request->input('datee'))
                    ->where('id', '!=', $id) // Exclude the current request
                    ->get();
        
                // Calculate the total hours already requested for that day
                $existingHours = $existingOvertimeRequests->reduce(function ($carry, $otRequest) {
                    return $carry + Carbon::createFromFormat('H:i:s', $otRequest->total_hours)->secondsSinceMidnight();
                }, 0);
        
                // Define the 4-hour limit in seconds
                $fourHours = 4 * 3600;
        
                // Check if the total of existing hours and the new request exceeds 4 hours
                if (($existingHours + $newRequestHours) > $fourHours) {
                    $existingHoursFormatted = gmdate("H:i:s", $existingHours);
                    $newRequestHoursFormatted = gmdate("H:i:s", $newRequestHours);
                    Log::warning("Overtime request exceeds daily 4-hour limit", [
                        'user_id' => $user->id,
                        'existing_hours' => $existingHoursFormatted,
                        'new_request_hours' => $newRequestHoursFormatted,
                        'total_hours' => gmdate("H:i:s", $existingHours + $newRequestHours)
                    ]);
                    return redirect()->back()->with(['error' => 'Overtime request cannot exceed 4 hours per day.']);
                }
        
                // Check for existing overtime requests on the same date and time, excluding the current request
                $duplicateRequest = OvertimeRequest::where('users_id', $user->id)
                    ->where('date', $request->input('datee'))
                    ->where('start_time', $request->input('start_timee'))
                    ->where('end_time', $request->input('end_timee'))
                    ->where('id', '!=', $id)
                    ->first();
        
                if ($duplicateRequest) {
                    return redirect()->back()->with(['error' => 'An overtime request for this date and time already exists.']);
                }
        
                // Update the overtime request
                $overtime->date = $request->input('datee');
                $overtime->start_time = $request->input('start_timee');
                $overtime->end_time = $request->input('end_timee'); 
                $overtime->total_hours = $request->input('total_hourse');
                $overtime->reason = $request->input('reasone');
        
                // Handle file upload if a new file is provided
                if ($request->hasFile('attached_file')) {
                    $file = $request->file('attached_file');
                    $filePath = $file->store('overtime_attachments', 'public');
                    $overtime->attached_file = $filePath; // Update with new file path
                    Log::info("File successfully updated", ['file_path' => $filePath]);
                }
        
                // Save the updated overtime request
                $overtime->save();
        
                Log::info("OT request updated successfully", ['overtime_id' => $overtime->id]);
        
                return redirect()->back()->with('success', 'OT request updated successfully.');
        
            } catch (ValidationException $e) {
                // Handle validation errors with SweetAlert
                foreach ($e->validator->errors()->all() as $error) {
                    Alert::error('Validation Error', $error);
                    Log::error("Validation error in OT update", ['error' => $error]);
                }
                return redirect()->back()->withInput();
            } catch (\Exception $e) {
                // Handle general errors
                Log::error("An error occurred while updating the OT request", [
                    'error_message' => $e->getMessage(),
                    'overtime_id' => $id,
                ]);
                return redirect()->back()->with('error', 'An error occurred while updating your overtime request: ' . $e->getMessage());
            }
        }
        
    

    public function deleteOT($id)
    {
        $overtime = OvertimeRequest::findOrFail($id);

        if ($overtime->status == 'Approved') {
            Alert::error('This leave request has already been approved and cannot be deleted.');
            return redirect()->back();
        }

        $overtime->delete();

        return redirect()->back()->with('success', 'Deleted Successfully');
    }

}
