<?php

namespace App\Http\Controllers\Employee\api;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\GeofencingSetting;
use App\Models\UserGeofence;
use Illuminate\Support\Facades\Auth;

class InformationController extends Controller
{
    // <<<<<<<<< --- Profile Information --- >>>>>>>>>>
    public function getProfileInformation(Request $request)
    {
        try {

            $user = auth()->user();

            $data = User::where('id', $user->id)->first();



            $reporting_to = User::where('id', $user->reporting_to)->pluck('name')->first();


            $userGeofences = UserGeofence::where('user_id', $user->id)->get();
            $geofences = [];

            foreach ($userGeofences as $userGeofence) {
                $geofence = GeofencingSetting::where('id', $userGeofence->geofence_id)->first();
                if ($geofence) {
                    $geofences[] = $geofence;
                }
            }

            if (empty($geofences)){
                return response()->json([
                    'status' => 'success',
                    'reporting_to' => $reporting_to,
                    'data' => $data,
                    'geofences' => 'User has no geofence location assign',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'reporting_to' => $reporting_to,
                'data' => $data,
                'geofences' => $geofences,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching profile information',
                'error' => $e->getMessage(),
            ], 500);
        }
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
    public function getAdditionalInformation(Request $request)
    {
        try {

            $user = auth()->user();

            $data = PersonalInformation::where('users_id', $user->id)->first();

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Additional information not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching additional information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createupdateAdditionalInformation(Request $request)
    {
        try {
            $user = auth()->user();
            $info = PersonalInformation::where('users_id', $user->id)->first();

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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating/adding personal information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
