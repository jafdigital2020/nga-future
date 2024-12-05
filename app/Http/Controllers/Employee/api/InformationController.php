<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class InformationController extends Controller
{
    //
    public function getProfileInformation(Request $request){

        $userId = $request->input('user_id');
        $data = User::where('id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);

    }
}
