<?php

namespace App\Http\Controllers\Employee\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PersonalInformation;

class InformationController extends Controller
{
    // <<<<<<<<< --- Profile Information --- >>>>>>>>>>
    public function getProfileInformation($id)
    {

        $data = User::where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);

    }

    // public function updateProfileInformation($id){

    //     $data = User::where('id', $id)->first();

    //     $data->name = $request->input('name');
    //     $data->fName = $request->input('fName');
    //     $data->mName = $request->input('mName');
    //     $data->lName = $request->input('lName');
    //     $data->numChildren = $request->input('numChildren');
    //     $data->personalEmail = $request->input('personalEmail');
    //     $data->mName = $request->input('mName');
    //     $data->lName = $request->input('lName');
    //     $data->numChildren = $request->input('numChildren');
    //     $data->personalEmail = $request->input('personalEmail');

    //     $data->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Personal Information Updated',
    //         'data' => $info
    //     ], 200);


    // }




    // <<<<<<<<< --- Additional Information --- >>>>>>>>>>
    public function getAdditionalInformation($id)
    {

        $data = PersonalInformation::where('users_id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function createupdateAdditionalInformation($id, Request $request)
    {

        $info = PersonalInformation::where('users_id', $id)->first();

        if ($info) {
            $info->religion = $request->input('religion');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');
            $info->personalEmail = $request->input('personalEmail');

            $info->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Personal Information Updated',
                'data' => $info
            ], 200);

        } else {

            $user = User::where('id', $id)->first();

            $info = new PersonalInformation();
            $info->users_id = $id;
            $info->name = $user->name;
            $info->religion = $request->input('religion');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');
            $info->personalEmail = $request->input('personalEmail');

            $info->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Personal Information Added',
                'data' => $info
            ], 200);
        }
    }
}
