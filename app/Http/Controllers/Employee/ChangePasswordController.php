<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('emp.changepassword.index');
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
