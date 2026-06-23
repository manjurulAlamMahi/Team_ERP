<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityStoreRequest;
use App\Http\Requests\CommunityUpdateRequest;
use App\Models\Community;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class CommunityController extends Controller
{
    use AjaxResponse;

    public function list()
    {
        Gate::authorize('community_list');

        $data['communities'] = Community::withCount('teams')->latest()->get();

        return view('admin.pages.community.list', $data);
    }

    public function create()
    {
        Gate::authorize('community_create');

        return view('admin.pages.community.create');
    }

    public function store(CommunityStoreRequest $request)
    {
        Community::create($request->only(['name', 'description', 'status']));

        return redirect()->route('community.list')->with('success', 'Community created successfully.');
    }

    public function edit($id)
    {
        Gate::authorize('community_edit');

        $data['community'] = Community::findOrFail($id);

        return view('admin.pages.community.edit', $data);
    }

    public function update(CommunityUpdateRequest $request)
    {
        Gate::authorize('community_edit');

        $community = Community::findOrFail($request->id);
        $community->update($request->only(['name', 'description', 'status']));

        return redirect()->route('community.list')->with('success', 'Community updated successfully.');
    }

    public function status(Request $request)
    {
        $community = Community::find($request->id);

        if (!$community) {
            return $this->error([], 'Community not found', 404);
        }

        $community->update([
            'status' => $community->status === 'active' ? 'inactive' : 'active'
        ]);

        return $this->success($community, 'Community Status Updated Successfully', 200);
    }

    public function destroy(Request $request)
    {
        Gate::authorize('community_delete');

        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $community = Community::find($request->id);

        if (!$community) {
            return $this->error([], 'Community not found', 404);
        }

        $community->delete();

        return $this->success([], 'Community Deleted Successfully', 200);
    }
}
