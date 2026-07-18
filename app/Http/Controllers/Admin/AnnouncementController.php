<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\User;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
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

        $announcements = Announcement::forTeam($user->team_id)->with('creator')->latest()->get();

        return view('admin.pages.announcement.list', compact('announcements'));
    }

    public function createForm()
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        return view('admin.pages.announcement.create');
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        Announcement::create([
            'team_id' => $user->team_id,
            'created_by' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'ends_at' => $request->ends_at,
        ]);

        return redirect()->route('announcement.list')->with('success', 'Announcement posted successfully.');
    }

    public function edit($id)
    {
        $user = $this->currentUser();
        $announcement = Announcement::findOrFail($id);
        abort_unless($announcement->isEditableBy($user), 403);

        return view('admin.pages.announcement.edit', compact('announcement'));
    }

    public function update(UpdateAnnouncementRequest $request)
    {
        $user = $this->currentUser();
        $announcement = Announcement::findOrFail($request->id);
        abort_unless($announcement->isEditableBy($user), 403);

        $announcement->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'ends_at' => $request->ends_at,
        ]);

        return redirect()->route('announcement.list')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->currentUser();
        $announcement = Announcement::find($request->id);

        if (!$announcement) {
            return $this->error([], 'Announcement not found', 404);
        }

        if (!$announcement->isEditableBy($user)) {
            return $this->error([], 'You are not allowed to delete this announcement.', 403);
        }

        $announcement->delete();

        return $this->success([], 'Announcement deleted successfully', 200);
    }
}
