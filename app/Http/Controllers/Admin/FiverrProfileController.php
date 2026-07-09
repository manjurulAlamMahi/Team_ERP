<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FiverrProfileStoreRequest;
use App\Http\Requests\FiverrProfileUpdateRequest;
use App\Models\FiverrProfile;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class FiverrProfileController extends Controller
{
    use AjaxResponse;

    public function list()
    {
        Gate::authorize('fiverr_profile_list');

        $profiles = FiverrProfile::withCount('clients')->latest()->get();

        return view('admin.pages.fiverr-profile.list', compact('profiles'));
    }

    public function create()
    {
        Gate::authorize('fiverr_profile_create');

        return view('admin.pages.fiverr-profile.create');
    }

    public function store(FiverrProfileStoreRequest $request)
    {
        FiverrProfile::create($request->only(['name', 'status']));

        return redirect()->route('fiverr.profile.list')->with('success', 'Fiverr profile created successfully.');
    }

    public function edit($id)
    {
        Gate::authorize('fiverr_profile_edit');

        $profile = FiverrProfile::findOrFail($id);

        return view('admin.pages.fiverr-profile.edit', compact('profile'));
    }

    public function update(FiverrProfileUpdateRequest $request)
    {
        $profile = FiverrProfile::findOrFail($request->id);
        $profile->update($request->only(['name', 'status']));

        return redirect()->route('fiverr.profile.list')->with('success', 'Fiverr profile updated successfully.');
    }

    public function status(Request $request)
    {
        Gate::authorize('fiverr_profile_edit');

        $profile = FiverrProfile::find($request->id);

        if (!$profile) {
            return $this->error([], 'Fiverr profile not found', 404);
        }

        $profile->update([
            'status' => $profile->status === 'active' ? 'inactive' : 'active',
        ]);

        return $this->success($profile, 'Status updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        Gate::authorize('fiverr_profile_delete');

        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $profile = FiverrProfile::find($request->id);

        if (!$profile) {
            return $this->error([], 'Fiverr profile not found', 404);
        }

        if ($profile->clients()->exists()) {
            return $this->error([], 'This profile is in use by one or more clients and cannot be deleted.', 422);
        }

        $profile->delete();

        return $this->success([], 'Fiverr profile deleted successfully', 200);
    }
}
