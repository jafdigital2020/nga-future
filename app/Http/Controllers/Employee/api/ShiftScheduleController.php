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


    public function getUserShiftSchedule(Request $request)
    {

        $user = auth()->user();

        // Default to the current week if no custom date is selected
        $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : Carbon::now()->startOfWeek();
        $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : $startDate->copy()->endOfWeek(); // Show 1 week default

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Fetch the shift schedule for the specific user within the given date range
        $shiftSchedule = $user->shiftSchedule()->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();

        // Return the user's shift schedule as JSON response
        return response()->json([
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'shiftSchedule' => $shiftSchedule,
        ]);
    }


    // public function getUserShiftSchedule(Request $request, $userId)
    // {
    //     // Default to the current week if no custom date is selected
    //     $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate')) : Carbon::now()->startOfWeek();
    //     $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate')) : $startDate->copy()->endOfWeek(); // Show 1 week default

    //     // Fetch the specific user by their ID
    //     $user = User::find($userId);

    //     // Check if the user exists
    //     if (!$user) {
    //         return response()->json(['error' => 'User not found.'], 404);
    //     }

    //     // Fetch the shift schedule for the specific user within the given date range
    //     $shiftSchedule = $user->shiftSchedule()->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();

    //     // Return the user's shift schedule as JSON response
    //     return response()->json([
    //         'user' => $user,
    //         'shiftSchedule' => $shiftSchedule,
    //         'startDate' => $startDate->format('Y-m-d'),
    //         'endDate' => $endDate->format('Y-m-d')
    //     ]);
    // }


}
