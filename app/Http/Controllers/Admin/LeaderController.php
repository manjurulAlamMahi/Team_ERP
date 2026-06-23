<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaderMemberStoreRequest;
use App\Models\Stack;
use App\Models\Team;
use App\Models\User;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class LeaderController extends Controller
{
    use AjaxResponse;

    private function leaderTeam(): Team
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->hasRole('Leader'), 403);
        abort_unless($user->team_id, 403);

        return Team::findOrFail($user->team_id);
    }

    public function teamStats()
    {
        $team = $this->leaderTeam();

        return view('admin.pages.leader.team_stats', compact('team'));
    }

    public function myTeam()
    {
        $team = $this->leaderTeam();
        $users = User::with(['stack', 'roles'])
            ->where('team_id', $team->id)
            ->where('id', '!=', Auth::id())
            ->where('is_request', false)
            ->get();

        return view('admin.pages.leader.my_team', compact('team', 'users'));
    }

    public function createMember()
    {
        $team = $this->leaderTeam();
        $roles = Role::whereIn('name', ['Co Leader', 'Stack Lead', 'Member', 'Probation'])
            ->orderBy('name')
            ->get();
        $stacks = Stack::where('status', 'active')->get();

        return view('admin.pages.leader.add_member', compact('team', 'roles', 'stacks'));
    }

    public function storeMember(LeaderMemberStoreRequest $request)
    {
        $team = $this->leaderTeam();

        $employeeId = $request->employee_id;

        if (!$employeeId || !preg_match('/^EMP-\d{4}$/', $employeeId)) {
            $lastEmployeeId = User::withTrashed()->orderByDesc('id')->value('employee_id');
            $nextNumber = $lastEmployeeId ? ((int) substr($lastEmployeeId, 4)) + 1 : 1;
            $employeeId = 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        $user = User::create([
            'employee_id' => $employeeId,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'team_id' => $team->id,
            'community_id' => $team->community_id,
            'stack_id' => $request->stack_id,
            'joining_date' => $request->joining_date,
            'probation_end_date' => $request->probation_end_date,
            'reporting_to' => Auth::id(),
            'added_by' => Auth::id(),
            'is_request' => false,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);
        $user->sendEmailVerificationNotification();

        return redirect()->route('leader.my.team')->with('success', 'Team member added successfully.');
    }

    public function updateMemberStatus(Request $request)
    {
        $team = $this->leaderTeam();

        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        if ($user->status === 'inactive') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return $this->success($user, 'Member status updated successfully', 200);
    }

    public function updateMemberRole(Request $request)
    {
        $team = $this->leaderTeam();

        $request->validate([
            'id' => 'required|exists:users,id',
            'role' => 'required|string|in:Co Leader,Stack Lead,Member,Probation',
        ]);
        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        $user->syncRoles([$request->role]);

        return $this->success($user, 'Member role updated successfully', 200);
    }
}
