<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LockScreenController extends Controller
{
    // Index
    public function lockScreen()
    {
        session(['user_data' => Auth::user()]);

        Auth::logout();

        return redirect()->route('show.screen');
    }

    public function show()
    {
        if (session()->has('user_data')) {
            $user = session('user_data');
            return view('auth.lockscreen', compact('user'));
        }

        return redirect()->route('login');
    }


    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = session('user_data');

        if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            // Log the user back in
            session()->forget('user_data');
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['password' => 'The provided password is incorrect.']);
    }
}
