<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberLeave\StoreMemberLeaveRequest;
use App\Http\Requests\MemberLeave\StoreSelfLeaveRequest;
use App\Http\Requests\MemberLeave\UpdateMemberLeaveRequest;
use App\Models\MemberLeave;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MemberLeaveController extends Controller
{
    use AjaxResponse;

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * This entire feature is Leader/Co Leader only - members never see or reach any of these actions.
     */
    private function leaderTeam(): Team
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        return Team::findOrFail($user->team_id);
    }

    public function list()
    {
        $team = $this->leaderTeam();

        $leaves = MemberLeave::forTeam($team->id)->with(['user', 'creator'])->orderByDesc('start_date')->get();

        return view('admin.pages.member-leave.list', compact('leaves'));
    }

    public function createForm()
    {
        $team = $this->leaderTeam();
        $actor = $this->currentUser();

        $members = User::where('team_id', $team->id)->where('id', '!=', $actor->id)->orderBy('name')->get();

        return view('admin.pages.member-leave.create', compact('members'));
    }

    public function store(StoreMemberLeaveRequest $request)
    {
        $team = $this->leaderTeam();
        $actor = $this->currentUser();

        $member = User::where('team_id', $team->id)->findOrFail($request->user_id);

        MemberLeave::create([
            'team_id' => $team->id,
            'user_id' => $member->id,
            'created_by' => $actor->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?: $request->start_date,
            'status' => $request->status,
            'reason' => $request->reason,
        ]);

        return redirect()->route('member.leave.list')->with('success', 'Leave record added successfully.');
    }

    public function edit($id)
    {
        $team = $this->leaderTeam();

        $leave = MemberLeave::forTeam($team->id)->findOrFail($id);
        $members = User::where('team_id', $team->id)->orderBy('name')->get();

        return view('admin.pages.member-leave.edit', compact('leave', 'members'));
    }

    public function update(UpdateMemberLeaveRequest $request)
    {
        $team = $this->leaderTeam();

        $leave = MemberLeave::forTeam($team->id)->findOrFail($request->id);
        $member = User::where('team_id', $team->id)->findOrFail($request->user_id);

        $leave->update([
            'user_id' => $member->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?: $request->start_date,
            'status' => $request->status,
            'reason' => $request->reason,
        ]);

        return redirect()->route('member.leave.list')->with('success', 'Leave record updated successfully.');
    }

    public function destroy(Request $request)
    {
        $team = $this->leaderTeam();

        $leave = MemberLeave::forTeam($team->id)->find($request->id);

        if (!$leave) {
            return $this->error([], 'Leave record not found', 404);
        }

        $leave->delete();

        return $this->success([], 'Leave record deleted successfully', 200);
    }

    /**
     * Self-service leave request: any team member (Stack Lead/Member/Probation)
     * can log their own absence directly into the same Leave Log the
     * Leader/Co Leader already manage - no approval step, just a heads-up.
     */
    public function askForm()
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Stack Lead', 'Member', 'Probation']), 403);

        return view('admin.pages.member-leave.ask');
    }

    public function storeSelf(StoreSelfLeaveRequest $request)
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Stack Lead', 'Member', 'Probation']), 403);

        $leave = MemberLeave::create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'created_by' => $user->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date ?: $request->start_date,
            'status' => $request->status,
            'reason' => $request->reason,
        ]);

        $leads = User::where('team_id', $user->team_id)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Leader', 'Co Leader']))
            ->get();

        if ($leads->isNotEmpty()) {
            Notification::send($leads, new AdminNotification(
                'Leave Logged: ' . $user->name,
                $user->name . ' logged themselves as "' . $leave->statusLabel() . '" (' . $leave->dateRangeLabel() . '). Reason: ' . $leave->reason,
                'info',
                'ri-calendar-close-line'
            ));
        }

        return redirect()->route('dashboard')->with('success', 'Your leave has been logged. Your team lead has been notified.');
    }
}
