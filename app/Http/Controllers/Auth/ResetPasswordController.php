<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected function reset(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        // Attempt to reset the password using the Password facade
        $response = Password::reset(
            $this->credentials($request),
            function ($user, $password) {
                $user->password = bcrypt($password); // Hash the new password
                $user->save(); // Save the updated user
            }
        );

        // If the password was reset successfully, return a success message
        if ($response == Password::PASSWORD_RESET) {
            return redirect($this->redirectTo)
                ->with('success', 'Your password has been reset!'); // Success message
        }

        // If the password reset failed, return back with an error
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
