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
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class LeaderController extends Controller
{
    use AjaxResponse;

    private function currentTeamUser(): User
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->team_id, 403);

        return $user;
    }

    /**
     * Any team member (Leader, Co Leader, Stack Lead, Member, Probation) can view their team.
     */
    private function memberTeam(): Team
    {
        return Team::findOrFail($this->currentTeamUser()->team_id);
    }

    /**
     * Only the team Leader may add members, manage passwords, etc.
     */
    private function leaderTeam(): Team
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasRole('Leader'), 403);

        return Team::findOrFail($user->team_id);
    }

    public function teamStats()
    {
        $team = $this->memberTeam();

        $members = User::with('roles')
            ->where('team_id', $team->id)
            ->where('is_request', false)
            ->get();

        $stats = [
            'total' => $members->count(),
            'active' => $members->where('status', 'active')->count(),
            'inactive' => $members->where('status', 'inactive')->count(),
            'by_role' => $members->groupBy(fn (User $u) => $u->getRoleNames()->first() ?? 'Unassigned')
                ->map->count(),
            'by_stack' => $members->groupBy(fn (User $u) => $u->stack->name ?? 'Unassigned')
                ->map->count(),
            'probation' => $members->filter(fn (User $u) => $u->hasRole('Probation'))
                ->map(fn (User $u) => [
                    'user' => $u,
                    'overdue' => $u->probation_end_date && \Carbon\Carbon::parse($u->probation_end_date)->lt(now()->startOfDay()),
                ]),
        ];

        return view('admin.pages.leader.team_stats', compact('team', 'stats'));
    }

    public function myTeam()
    {
        $team = $this->memberTeam();
        $actor = $this->currentTeamUser();
        $users = User::with(['stack', 'roles'])
            ->where('team_id', $team->id)
            ->where('is_request', false)
            ->get();

        return view('admin.pages.leader.my_team', compact('team', 'users', 'actor'));
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
        $team = $this->memberTeam();
        $actor = $this->currentTeamUser();

        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        if (!$user->canBeManagedBy($actor)) {
            return $this->error([], 'You are not allowed to manage this member.', 403);
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        if ($user->status === 'inactive') {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return $this->success($user, 'Member status updated successfully', 200);
    }

    public function updateMemberRole(Request $request)
    {
        $team = $this->memberTeam();
        $actor = $this->currentTeamUser();

        $request->validate([
            'id' => 'required|exists:users,id',
            'role' => 'required|string|in:Co Leader,Stack Lead,Member,Probation',
        ]);
        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        if (!$user->canBeManagedBy($actor)) {
            return $this->error([], 'You are not allowed to manage this member.', 403);
        }

        if (in_array($request->role, ['Co Leader', 'Stack Lead'], true)
            && User::hasConflictingTeamRole($request->role, $team->id, $user->stack_id, $user->id)) {
            $message = $request->role === 'Stack Lead'
                ? 'This stack already has a Stack Lead in your team.'
                : 'This team already has a Co Leader.';

            return $this->error([], $message, 422);
        }

        $user->syncRoles([$request->role]);

        return $this->success($user, 'Member role updated successfully', 200);
    }

    public function updateMemberInfo(Request $request)
    {
        $team = $this->memberTeam();
        $actor = $this->currentTeamUser();

        $request->validate([
            'id' => 'required|exists:users,id',
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users', 'employee_id')->ignore($request->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($request->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->id)],
            'whatsapp' => ['nullable', 'string', 'max:20', Rule::unique('users', 'whatsapp')->ignore($request->id)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/'],
            'joining_date' => ['required', 'date'],
            'probation_end_date' => ['required', 'date', 'after_or_equal:joining_date'],
        ]);

        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        if (!$user->canBeManagedBy($actor)) {
            return $this->error([], 'You are not allowed to manage this member.', 403);
        }

        if ($user->hasRole('Probation')
            && \Carbon\Carbon::parse($request->probation_end_date)->lt(now()->startOfDay())) {
            return $this->error([], 'This probation end date has already passed, so the member is not in probation.', 422);
        }

        $user->update($request->only([
            'employee_id', 'username', 'name', 'email', 'whatsapp', 'phone', 'joining_date', 'probation_end_date',
        ]));

        return $this->success($user, 'Member info updated successfully', 200);
    }

    public function updateMemberPassword(Request $request)
    {
        $team = $this->leaderTeam();

        $request->validate([
            'id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('team_id', $team->id)
            ->where('id', $request->id)
            ->where('id', '!=', Auth::id())
            ->firstOrFail();

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return $this->success($user, 'Member password updated successfully', 200);
    }
}
