<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactEmergency;

class EmegencyContactController extends Controller
{
    public function getEmegencyContact($id){

        $data = ContactEmergency::where('users_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function createupdateEmegencyContact($id, Request $request){

        $data = ContactEmergency::where('users_id', $id)->first();

        if($data)
        {

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
            $data->users_id = $id;
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
    }


}
