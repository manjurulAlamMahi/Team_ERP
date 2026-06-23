<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingAdminSite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    // View
    public function admin(){
        Gate::authorize('setting_admin');
        return view('settings.admin');
    }
    // Update
    public function admin_update(Request $request){

        $request->validate([
            'name'      => 'required',
            'password'  => 'required',
            'logo'      => 'nullable|image',
            'logo_dark' => 'nullable|image',
            'logo_sm'   => 'nullable|image',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('error', 'The password you entered is incorrect.');
        }

        $data              = $request->all();
        $admin             = SettingAdminSite::first();
        $foler             = 'admin/assets/images/logo';
        $data['logo']      = $this->uploadImage($request->logo,$admin->logo,$foler,270,72,'logo');
        $data['logo_dark'] = $this->uploadImage($request->logo_dark,$admin->logo_dark,$foler,270,72,'logo_dark');
        $data['logo_sm']   = $this->uploadImage($request->logo_sm,$admin->logo_sm,$foler,70,70,'logo_sm');
        $admin->update($data);

        return redirect()->back()->with('success','Updated Successfully');
    }
    // Mail Setting View
    public function mail(){
        Gate::authorize('setting_mail');
        return view('settings.mail');
    }
    public function mail_update(Request $request)
    {
        $request->validate([
            'password'          => 'required',
            'mail_mailer'       => 'required|string',
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|string',
            'mail_username'     => 'nullable|string',
            'mail_password'     => 'nullable|string',
            'mail_encryption'   => 'nullable|string',
            'mail_from_address' => 'required|string',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->with('error', 'The password you entered is incorrect.');
        }

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak = "\n";
            $envContent = preg_replace([
                '/MAIL_MAILER=(.*)\s/',
                '/MAIL_HOST=(.*)\s/',
                '/MAIL_PORT=(.*)\s/',
                '/MAIL_USERNAME=(.*)\s/',
                '/MAIL_PASSWORD=(.*)\s/',
                '/MAIL_ENCRYPTION=(.*)\s/',
                '/MAIL_FROM_ADDRESS=(.*)\s/',
            ], [
                'MAIL_MAILER=' . $request->mail_mailer . $lineBreak,
                'MAIL_HOST=' . $request->mail_host . $lineBreak,
                'MAIL_PORT=' . $request->mail_port . $lineBreak,
                'MAIL_USERNAME=' . $request->mail_username . $lineBreak,
                'MAIL_PASSWORD=' . $request->mail_password . $lineBreak,
                'MAIL_ENCRYPTION=' . $request->mail_encryption . $lineBreak,
                'MAIL_FROM_ADDRESS=' . '"' . $request->mail_from_address . '"' . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update');
        }

        return redirect()->back();
    }
}
