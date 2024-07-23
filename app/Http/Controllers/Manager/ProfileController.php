<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Models\BankInformation;
use App\Models\ContactEmergency;
use App\Models\PersonalInformation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function index ()
    {
        $user = Auth::user();
        $contacts = ContactEmergency::where('users_id', $user->id)->get();
        $bankinfo = BankInformation::where('users_id', $user->id)->get();
        $personalInformation = PersonalInformation::where('users_id', $user->id)->get();
        return view('manager.profile', compact('user', 'contacts', 'bankinfo', 'personalInformation'));
    }

    public function update (Request $request) 
    {
        $user = Auth::user();

            if (!($user instanceof \App\Models\User)) {
                return response()->json(['error' => 'Authenticated user is not an instance of User model'], 400);
            }

        $user->name = $request->input('name');
        $user->birthday = $request->input('birthday');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->completeAddress = $request->input('completeAddress');
        if ($request->has('image')) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $user->image = $imageName;
        }

        $user->save();

        Alert::success('Changes Save Successfully', 'Profile Updated');
        return redirect()->back();
    }

    public function contactStore(Request $request)
    {
        $user = Auth::user();
        
        // Check if the user already has contact emergency data
        $contact = ContactEmergency::where('users_id', $user->id)->first();
        
        if ($contact) {
            // If existing contact found, update it
            $contact->primaryName = $request->input('primaryName');
            $contact->primaryRelation = $request->input('primaryRelation');
            $contact->primaryPhone = $request->input('primaryPhone');
            $contact->secondName = $request->input('secondName');
            $contact->secondRelation = $request->input('secondRelation');
            $contact->secondPhone = $request->input('secondPhone');

            $contact->save();

            Alert::success('Contact Emergency Updated');
        } else {
            // If no existing contact, create a new one
            $contact = new ContactEmergency();
            $contact->users_id = $user->id;
            $contact->primaryName = $request->input('primaryName');
            $contact->primaryRelation = $request->input('primaryRelation');
            $contact->primaryPhone = $request->input('primaryPhone');
            $contact->secondName = $request->input('secondName');
            $contact->secondRelation = $request->input('secondRelation');
            $contact->secondPhone = $request->input('secondPhone');

            $contact->save();

            Alert::success('Contact Emergency Added');
        }

        return redirect()->back();
    }

    public function personalInfo (Request $request)
    {
        $user = Auth::user();

        $info = PersonalInformation::where('users_id', $user->id)->first();

        if($info)
        {
            $info->religion = $request->input('religion');
            $info->age = $request->input('age');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');

            $info->save();

            Alert::success('Personal Information Updated');
        } else {
            
            $info = new PersonalInformation();
            $info->users_id = $user->id;
            $info->name = $user->name;
            $info->religion = $request->input('religion');
            $info->age = $request->input('age');
            $info->education = $request->input('education');
            $info->nationality = $request->input('nationality');
            $info->mStatus = $request->input('mStatus');
            $info->numChildren = $request->input('numChildren');

            $info->save();

            Alert::success('Personal Infromation Added');
        }

        return redirect()->back();
    }

    public function changePasswordHr()
    {
        return view('manager.changepassword');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        if (Hash::check($request->current_password, auth()->user()->password)) {
            $this->updatePassword(auth()->user(), $request->password);
    
            // Manually re-authenticate the user to prevent logout
            auth()->login(auth()->user());
    
            Alert::success('Password changed successfully');
        } else {
            Alert::error('Current password does not match. Please try again.');
        }
    
        return redirect()->back();
    }
    
    private function updatePassword($user, $newPassword)
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}
