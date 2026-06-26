<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Models\CoverBanner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\AjaxResponse;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use AjaxResponse;

    public function avatar_store(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $image = $request->file('avatar');
            $extension = $image->getClientOriginalExtension();
            $image_name = time() . '.' . $extension;
            $image_path = 'user/avatar/' . $image_name;

            $avatar = Avatar::create([
                'image' => $image_path,
                'user_id' => Auth::user()->id
            ]);

            if ($avatar) {
                Image::make($image)->resize(150, 150)->save(public_path($image_path));
            }

            return $this->success([], 'Avatar uploaded successfully', 200);
        } catch (Exception $e) {
            dd($e->getMessage());
            return $this->error([], 'Something went wrong. Please try again.', 500);
        }
    }

    public function avatar_destroy($id)
    {
        try {

            $avatar = Avatar::find($id);
            if (!$avatar) {
                return back()->with('error', 'Avatar not found');
            }
            if (Auth::user()->id != $avatar->user_id) {
                return back()->with('error', 'You are not authorized to delete this avatar');
            }
            if ($avatar->user_id == 0) {
                return back()->with('error', 'Default avatar cannot be deleted');
            }
            if (file_exists($avatar->image)) {
                unlink($avatar->image);
            }
            $avatar->delete();

            return back()->with('success', 'Avatar removed successfully');
        } catch (Exception $e) {

            return back()->with('success', 'Something went wrong. Please try again.');
        }
    }


    public function index()
    {
        $data['avatar'] = Avatar::whereIn('user_id', [Auth::user()->id, 0])->get();
        $data['cover'] = CoverBanner::get();
        return view('profile.index', $data);
    }

    public function password()
    {
        return view('profile.password');
    }

    public function password_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|confirmed',
        ], [
            'old_password.required' => 'The old password is required.',
            'new_password.required' => 'The new password is required.',
            'new_password.confirmed' => 'The new password and confirmation password do not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return redirect()->back()->with('error', 'The old password you entered is incorrect.');
        }

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);

        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('lock.screen')->with('success', 'Your password has been updated successfully.');
    }

    public function cover(Request $request)
    {
        $cover = CoverBanner::find($request->cover_id);

        User::find($request->user_id)->update([
            'cover' => $cover->image,
        ]);

        return $this->success([], 'Cover Photo Updated ', 200);
    }
    public function avatar(Request $request)
    {
        $avatar = Avatar::find($request->avatar_id);

        User::find($request->user_id)->update([
            'avatar' => $avatar->image,
        ]);

        return $this->success([], 'Avatar Updated ', 200);
    }

    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'exclude_id' => 'nullable|integer|exists:users,id',
        ]);

        $exists = User::where('username', $request->username)
            ->when($request->exclude_id, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? '❌ Username Already Exists' : '✅ Username Available',
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|min:3',
            'email'          => 'required|email|unique:users,email,' . (Auth::id() ?? 'NULL'),
            'username'       => 'required|unique:users,username,' . (Auth::id() ?? 'NULL'),
            'official_email' => 'nullable|email|unique:users,official_email,' . (Auth::id() ?? 'NULL'),
            'whatsapp'       => 'required|unique:users,whatsapp,' . (Auth::id() ?? 'NULL'),
            'phone'          => 'nullable|unique:users,phone,' . (Auth::id() ?? 'NULL') . '|regex:/^[0-9]{10,15}$/',
            'telegram'       => 'nullable|string|max:255',
            'github'         => 'nullable|string|max:255',
            'discord'        => 'nullable|string|max:255',
            'facebook'       => 'nullable|url|max:255',
            'linkedin'       => 'nullable|url|max:255',
            'gmail'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'designation'    => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'password'       => 'required',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 3 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username has already been taken.',
            'phone.unique' => 'The phone number has already been taken.',
            'phone.regex' => 'The phone number must be 10-15 digits long.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('profile_update', true)->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('profile_update', true)
                ->withErrors(['password' => 'The password you entered does not match your current password.'])
                ->withInput();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->official_email = $request->official_email;
        $user->address = $request->address;
        $user->whatsapp = $request->whatsapp;
        $user->phone = $request->phone;
        $user->telegram = $request->telegram;
        $user->github = $request->github;
        $user->discord = $request->discord;
        $user->facebook = $request->facebook;
        $user->linkedin = $request->linkedin;
        $user->gmail = $request->gmail;
        $user->designation = $request->designation;
        $user->dob = $request->dob;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Step 5: Return with success message
        return redirect()->back()->with([
            'profile_update' => true,
            'success' => 'Profile Updated Successfully',
        ]);
    }

    public function destroy(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $user = Auth::user();
        $deleted = $user->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();
        if ($deleted) {
            Auth::logout();
            return $this->success([], 'Account Deleted Successfully', 200);
        }

        return $this->error([], 'Account deletion failed. Please try again.', 500);
    }
}
