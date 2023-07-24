<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceReportController extends Controller
{

    public function empreport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');

        $data = DB::table('attendance');

        if ($startDate && $endDate) {
            $data->whereBetween('date', [$startDate, $endDate]);
        } elseif ($request->input('filter') == 'last_30_days') {
            $data->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
        } elseif ($request->input('filter') == 'last_15_days') {
            $data->where('date', '>=', Carbon::now()->subDays(15)->toDateString());
        } elseif ($request->input('filter') == 'last_year') {
            $data->where('date', '>=', Carbon::now()->subYear()->toDateString());
        }

        if ($userId) {
            $data->where('users_id', '=', $userId);
        }

        $filteredData = $data->get();
        $totalSeconds = 0;
        $totalLateSeconds = 0;

        foreach ($filteredData as $row) {
            $timeTotal = strtotime($row->timeTotal);
            $totalSeconds += $timeTotal;
        }
        foreach($filteredData as $row) {
            $totalLate = strtotime($row->totalLate);
            $totalLateSeconds += $totalLate;
        }

        $total = gmdate("H:i:s", $totalSeconds);
        $totalLate = gmdate("H:i:s", $totalLateSeconds);

        $users = User::all();
        return view('admin.attendance.index', compact('filteredData', 'total', 'users', 'totalLate'));

    }
}
