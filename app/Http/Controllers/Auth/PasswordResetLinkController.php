<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResetPassword;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }
    public function confirm()
    {
        return view('auth.confirm-mail');
    }

    public function newpassword($token)
    {
        if (ResetPassword::where('token', $token)->exists()) {
            return view('auth.new-password', compact('token'));
        } else {
            abort(404);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate a secure token
        $token = Str::random(60);

        // If a token already exists for this email, delete it
        ResetPassword::where('email', $request->email)->delete();

        // Insert new token into password reset table
        ResetPassword::create([
            'email' => $request->email,
            'token' => $token,
        ]);

        $user = User::where('email', $request->email)->first();

        // Send the custom notification with the generated token
        $user->notify(new ResetPasswordNotification($token));

        return redirect()->route('confirm.email')->with('success', 'We have sent you the password reset link.');
    }

    public function update(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(),[
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the reset token entry
        $resetEntry = ResetPassword::where('token', $request->token)->first();

        // If the token does not exist, return an error
        if (!$resetEntry) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        // Find the user by email
        $user = User::where('email', $resetEntry->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email.']);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the reset token entry after successful password update
        $resetEntry->delete();

        // Redirect with success message
        return redirect()->route('login')->with('success', 'Your password has been updated successfully.');
    }
}
