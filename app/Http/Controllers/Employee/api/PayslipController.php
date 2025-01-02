<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalaryTable;

class PayslipController extends Controller
{
    public function payslipView(Request $request)
    {

        $user = auth()->user();

        $cutoffPeriod = $request->input('cutoff_period');
        $selectedYear = $request->input('year', now()->year);

        $data = SalaryTable::query();

        // Filter by authenticated user
        $data->where('users_id', $user->id);


        // Add filter for year if provided
        if (!empty($selectedYear)) {
            $data->where('year', $selectedYear);
        }

        // Add filter for cutoff_period if provided
        if (!empty($cutoffPeriod)) {
            $data->where('cut_off', $cutoffPeriod); // Search the cut_off column
        }

        // Add filter for status being 'Payslip'
        $data->where('status', 'Payslip');

        $payslip = $data->get();


        return response()->json([
            'payslip' => $payslip,
            'cutoffPeriod' => $cutoffPeriod,
            'selectedYear' => $selectedYear,
        ]);
    }
}
