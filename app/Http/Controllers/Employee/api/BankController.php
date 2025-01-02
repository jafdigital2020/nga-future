<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankInformation;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function getBankInformation(Request $request){
        try {
            $user = auth()->user();

            $data = BankInformation::where('users_id', $user->id)->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User has not set bank information yet'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching bank information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createupdateBankInformation(Request $request){
        try {
            $user = auth()->user();

            $data = BankInformation::where('users_id', $user->id)->first();

            if($data) {
                $data->bankName = $request->input('bankName');
                $data->bankAccName = $request->input('bankAccName');
                $data->bankAccNumber = $request->input('bankAccNumber');

                $data->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Bank Information Updated',
                    'data' => $data
                ], 200);

            } else {
                $data = new BankInformation();
                $data->users_id = $user->id;
                $data->bankName = $request->input('bankName');
                $data->bankAccName = $request->input('bankAccName');
                $data->bankAccNumber = $request->input('bankAccNumber');

                $data->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Bank Information Added',
                    'data' => $data
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating bank information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
