<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuickAccessMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    // Index
    public function index()
    {
        return view('admin.pages.dashboard');
    }
    public function inbox()
    {
        return view('admin.pages.inbox');
    }
    // Index
    public function addQuickAccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'nullable|string',
            'route' => 'required|string',
            'url' => 'required|string',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Failed!');
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();
        // Store the new quick access menu
        QuickAccessMenu::create($data);

        // Redirect or return a response
        return redirect()->back()->with('success', 'Added to quick menu successfully');
    }
    public function removeQuickAccess($route)
    {
        // Store the new quick access menu
        $r = QuickAccessMenu::where('route', $route)->delete();

        if($r){
            return redirect()->back()->with('success', 'Removed from quick menu successfully');
        }else{
            return redirect()->back()->with('error', 'Failed!');
        }
        // Redirect or return a response
    }
}
