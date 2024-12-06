<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankInformation;

class BankController extends Controller
{
    public function getBankInformation($id){

        $data = BankInformation::where('users_id', $id)->get();

        if ($data->isEmpty()) {
            return response()->json([
            'status' => 'error',
            'message' => 'User does not exist or user has not set bank information'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);

    }

    public function createupdateBankInformation($id, Request $request){

        $data = BankInformation::where('users_id', $id)->first();

        if($data)
        {

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
            $data->users_id = $id;
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
    }
}
