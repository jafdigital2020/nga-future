<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEmergency;
use Illuminate\Support\Facades\Auth;

class EmegencyContactController extends Controller
{
    public function getEmegencyContact(Request $request){
        try {
            $user = auth()->user();

            $data = ContactEmergency::where('users_id', $user->id)->get();


            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User has not set Emergency Contact yet'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createupdateEmegencyContact(Request $request){
        try {
            $user = auth()->user();

            $data = ContactEmergency::where('users_id', $user->id)->first();

            if($data) {
                $data->primaryName = $request->input('primaryName');
                $data->primaryRelation = $request->input('primaryRelation');
                $data->primaryPhone = $request->input('primaryPhone');
                $data->secondName = $request->input('secondName');
                $data->secondRelation = $request->input('secondRelation');
                $data->secondPhone = $request->input('secondPhone');

                $data->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Emergency Contact Updated',
                    'data' => $data
                ], 200);

            } else {
                $data = new ContactEmergency();
                $data->users_id = $user->id;
                $data->primaryName = $request->input('primaryName');
                $data->primaryRelation = $request->input('primaryRelation');
                $data->primaryPhone = $request->input('primaryPhone');
                $data->secondName = $request->input('secondName');
                $data->secondRelation = $request->input('secondRelation');
                $data->secondPhone = $request->input('secondPhone');

                $data->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Emergency Contact Added',
                    'data' => $data
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
