<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeamSheet\StoreTeamSheetRequest;
use App\Http\Requests\TeamSheet\UpdateTeamSheetRequest;
use App\Models\TeamSheet;
use App\Models\User;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamSheetController extends Controller
{
    use AjaxResponse;

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function list()
    {
        $user = $this->currentUser();
        abort_unless($user->team_id, 403);

        $sheets = TeamSheet::forTeam($user->team_id)->with('creator')->latest()->get();

        return view('admin.pages.team-sheet.list', compact('sheets'));
    }

    public function createForm()
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        return view('admin.pages.team-sheet.create');
    }

    public function store(StoreTeamSheetRequest $request)
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        TeamSheet::create([
            'team_id' => $user->team_id,
            'created_by' => $user->id,
            'title' => $request->title,
            'link' => $request->link,
        ]);

        return redirect()->route('team.sheet.list')->with('success', 'Sheet added successfully.');
    }

    public function edit($id)
    {
        $user = $this->currentUser();
        $sheet = TeamSheet::findOrFail($id);
        abort_unless($sheet->isEditableBy($user), 403);

        return view('admin.pages.team-sheet.edit', compact('sheet'));
    }

    public function update(UpdateTeamSheetRequest $request)
    {
        $user = $this->currentUser();
        $sheet = TeamSheet::findOrFail($request->id);
        abort_unless($sheet->isEditableBy($user), 403);

        $sheet->update([
            'title' => $request->title,
            'link' => $request->link,
        ]);

        return redirect()->route('team.sheet.list')->with('success', 'Sheet updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->currentUser();
        $sheet = TeamSheet::find($request->id);

        if (!$sheet) {
            return $this->error([], 'Sheet not found', 404);
        }

        if (!$sheet->isEditableBy($user)) {
            return $this->error([], 'You are not allowed to delete this sheet.', 403);
        }

        $sheet->delete();

        return $this->success([], 'Sheet deleted successfully', 200);
    }
}
