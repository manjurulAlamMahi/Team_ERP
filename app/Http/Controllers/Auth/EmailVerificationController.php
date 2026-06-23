<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        $user = auth()->user(); // Get the logged-in user

        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'Your email is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('email_success', 'Verification link sent!');
    }
    public function verify(EmailVerificationRequest $request, $id)
    {
        $request->fulfill();
        $data['user'] = User::find($id);
        return view('auth.verified-mail',$data);
    }
}
