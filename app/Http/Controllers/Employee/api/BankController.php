<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function getBankInformation(Request $request){

        $userId = $request->input('user_id');
        $data = User::where('users_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);

    }
}
