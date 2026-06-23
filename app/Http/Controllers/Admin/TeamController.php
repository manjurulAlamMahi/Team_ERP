<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Models\Community;
use App\Models\Team;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    use AjaxResponse;

    public function list()
    {
        Gate::authorize('team_list');

        $data['teams'] = Team::with('community')->withCount('members')->latest()->get();

        return view('admin.pages.team.list', $data);
    }

    public function create()
    {
        Gate::authorize('team_create');

        $data['communities'] = Community::where('status', 'active')->get();

        return view('admin.pages.team.create', $data);
    }

    public function store(TeamStoreRequest $request)
    {
        Team::create($request->only(['community_id', 'name', 'started_at', 'description', 'status']));

        return redirect()->route('team.list')->with('success', 'Team created successfully.');
    }

    public function edit($id)
    {
        Gate::authorize('team_edit');

        $data['team'] = Team::findOrFail($id);
        $data['communities'] = Community::where('status', 'active')->get();

        return view('admin.pages.team.edit', $data);
    }

    public function update(TeamUpdateRequest $request)
    {
        Gate::authorize('team_edit');

        $team = Team::findOrFail($request->id);
        $team->update($request->only(['community_id', 'name', 'started_at', 'description', 'status']));

        return redirect()->route('team.list')->with('success', 'Team updated successfully.');
    }

    public function status(Request $request)
    {
        $team = Team::find($request->id);

        if (!$team) {
            return $this->error([], 'Team not found', 404);
        }

        $team->update([
            'status' => $team->status === 'active' ? 'inactive' : 'active'
        ]);

        return $this->success($team, 'Team Status Updated Successfully', 200);
    }

    public function destroy(Request $request)
    {
        Gate::authorize('team_delete');

        if (!Hash::check($request->password, Auth::user()->password)) {
            return $this->error([], 'Incorrect Password', 401);
        }

        $team = Team::find($request->id);

        if (!$team) {
            return $this->error([], 'Team not found', 404);
        }

        $team->delete();

        return $this->success([], 'Team Deleted Successfully', 200);
    }
}
