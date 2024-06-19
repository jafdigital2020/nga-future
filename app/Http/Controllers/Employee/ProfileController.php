<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankInformation;
use App\Models\ContactEmergency;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    public function index ()
    {
        $user = Auth::user();
        $contacts = ContactEmergency::where('users_id', $user->id)->get();
        $bankinfo = BankInformation::where('users_id', $user->id)->get();
        return view('emp.profile.index', compact('user', 'contacts', 'bankinfo'));
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

}
