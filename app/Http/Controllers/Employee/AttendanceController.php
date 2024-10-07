<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Admin\EmployeeController;

class AttendanceController extends Controller
{
    public function index (Request $request)
    {
        $EmployeeAttendance = EmployeeAttendance::latest()->first();
        $att = Auth::user()->id;
        $total = EmployeeAttendance::sum('timeTotal');
        $all = DB::table('attendance')->get();
        $empatt = DB::table('attendance')->where('users_id', auth()->user()->id)->get();
        $latest = EmployeeAttendance::where('users_id', Auth::user()->id)->latest()->first();
        $data = EmployeeAttendance::where('users_id', Auth::user()->id)->get();

        $authUserId = auth()->user()->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = EmployeeAttendance::where('users_id', $authUserId);
        
        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->input('filter') == 'last_30_days') {
            $data->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
        } elseif ($request->input('filter') == 'last_15_days') {
            $data->where('date', '>=', Carbon::now()->subDays(15)->toDateString());
        } elseif ($request->input('filter') == 'last_year') {
            $data->where('date', '>=', Carbon::now()->subYear()->toDateString());
        }
        
        $filteredData = $data->get();
       
        $totalSeconds = 0;
        $totalLateSeconds = 0;

        foreach ($filteredData as $row) {
            $timeTotal = explode(':', $row->timeTotal);
            if (count($timeTotal) === 3 && is_numeric($timeTotal[0]) && is_numeric($timeTotal[1]) && is_numeric($timeTotal[2])) {
                $seconds = ($timeTotal[0] * 3600) + ($timeTotal[1] * 60) + $timeTotal[2];
                $totalSeconds += $seconds;
            }
        }

        foreach ($filteredData as $row) {
            $totalLate = explode(':', $row->totalLate);
            if (count($totalLate) === 3 && is_numeric($totalLate[0]) && is_numeric($totalLate[1]) && is_numeric($totalLate[2])) {
                $seconds = ($totalLate[0] * 3600) + ($totalLate[1] * 60) + $totalLate[2];
                $totalLateSeconds += $seconds;
            }
        }
        
        $totalHours = floor($totalSeconds / 3600);
        $totalMinutes = floor(($totalSeconds % 3600) / 60);
        $totalSeconds = $totalSeconds % 60;
        
        $totalTime = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);
        
        $totalLateHours = floor($totalLateSeconds / 3600);
        $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
        $totalLateSeconds = $totalLateSeconds % 60;
        
        $totalLate = sprintf("%02d:%02d:%02d", $totalLateHours, $totalLateMinutes, $totalLateSeconds);
        return view('emp.attendance.index', compact('att', 'empatt', 'all', 'total', 'latest', 'data', 'filteredData', 'totalTime', 'totalLate'));
    }

    public function overtimeRequest(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'date' => 'required|date|after:today',
                'reason' => 'required|string',
            ], [
                'date.after' => 'The date must be at least one day ahead.',
                'date.required' => 'Please select a date for the overtime request.',
                'date.date' => 'Please provide a valid date.',
            ]);
    
            // Save the overtime request
            $overtime = new OvertimeRequest();
            $overtime->users_id = Auth::user()->id;
            $overtime->date = $request->input('date');
            $overtime->start_time = $request->input('start_time');
            $overtime->end_time = $request->input('end_time');
            $overtime->total_hours = $request->input('total_hours');
            $overtime->reason = $request->input('reason');
            $overtime->save();
    
            // Success message
            return redirect()->back()->with('success', 'Submitted Successfully');
    
        } catch (ValidationException $e) {
            // Handle validation errors with SweetAlert
            foreach ($e->validator->errors()->all() as $error) {
                Alert::error('Validation Error', $error);
            }
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            // Handle general errors
            return redirect()->back()->with('error', 'An error occurred while submitting your overtime request.' );
        }
    }

}
