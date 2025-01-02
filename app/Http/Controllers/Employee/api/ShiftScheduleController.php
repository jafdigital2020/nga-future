<?php

namespace App\Http\Controllers\Employee\api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\ShiftSchedule;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class ShiftScheduleController extends Controller
{
    public function shiftDaily(Request $request)
    {
        $user = auth()->user(); // Get the authenticated user

        // Get the user ID for the specific user (can come from the request, or use the authenticated user)
        $specificUserId = $request->input('userId', $user->id); // Default to authenticated user if no user ID is provided

        // Get today's date
        $today = Carbon::today(); // This is today's date in 'Y-m-d' format

        // Get shift data only for today for the specific user
        $user = User::with(['shiftSchedule' => function ($query) use ($today) {
            // Filter for today's shifts
            $query->whereDate('date', $today->format('Y-m-d'));
        }])
        ->where('id', $specificUserId) // Only include the specific user
        ->where('reporting_to', $user->id) // Only include subordinates of the authenticated user
        ->first(); // Fetch the specific user with today's shifts

        // Return the user's shift for today as JSON response
        return response()->json([
            'user' => $user,
            'today' => $today->format('Y-m-d')
        ]);
    }

}
