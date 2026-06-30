<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class DailyTaskController extends Controller
{
    use AjaxResponse;

    private function teamUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->team_id, 403);
        return $user;
    }

    private function leadUser(): User
    {
        $user = $this->teamUser();
        abort_unless($user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']), 403);
        return $user;
    }

    // ────────────────────────────────────────────────────────────────────────
    // Add Task  (all team roles)
    // ────────────────────────────────────────────────────────────────────────

    public function createForm()
    {
        $this->teamUser();
        return view('admin.pages.daily-task.add');
    }

    public function store(Request $request)
    {
        $user = $this->teamUser();

        $request->validate([
            'client_name'            => 'required|string|max:255',
            'profile_name'           => 'required|string|max:255',
            'plan_details'           => 'required|string',
            'expected_complete_date' => 'nullable|date|after_or_equal:today',
        ]);

        DailyTask::create([
            'team_id'                => $user->team_id,
            'user_id'                => $user->id,
            'created_by'             => $user->id,
            'task_date'              => today(),
            'client_name'            => $request->client_name,
            'profile_name'           => $request->profile_name,
            'plan_details'           => $request->plan_details,
            'expected_complete_date' => $request->expected_complete_date,
            'source'                 => 'self',
        ]);

        return redirect()->route('daily.task.my')->with('success', 'Task added successfully.');
    }

    // ────────────────────────────────────────────────────────────────────────
    // My Tasks (all team roles)
    // ────────────────────────────────────────────────────────────────────────

    public function myTasks()
    {
        $user = $this->teamUser();

        $tasks = DailyTask::with(['creator', 'remarksByUser'])
            ->forUser($user->id)
            ->forTeam($user->team_id)
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pages.daily-task.my-tasks', compact('tasks'));
    }

    public function complete(Request $request)
    {
        $user = $this->teamUser();

        $request->validate(['id' => 'required|exists:daily_tasks,id']);

        $task = DailyTask::forUser($user->id)->forTeam($user->team_id)->findOrFail($request->id);

        if ($task->status === 'pending') {
            $task->update(['status' => 'completed', 'completed_at' => now()]);
        } else {
            $task->update(['status' => 'pending', 'completed_at' => null]);
        }

        return $this->success($task, 'Task status updated', 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Assign Task  (Leader, Co Leader, Stack Lead)
    // ────────────────────────────────────────────────────────────────────────

    public function assignForm()
    {
        $actor = $this->leadUser();
        $team  = Team::findOrFail($actor->team_id);

        // Scope assignable members by role rules
        $members = $this->assignableMembers($actor, $team);

        return view('admin.pages.daily-task.assign', compact('team', 'members'));
    }

    public function storeAssigned(Request $request)
    {
        $actor = $this->leadUser();
        $team  = Team::findOrFail($actor->team_id);

        $request->validate([
            'user_id'                => ['required', 'exists:users,id'],
            'client_name'            => 'required|string|max:255',
            'profile_name'           => 'required|string|max:255',
            'plan_details'           => 'required|string',
            'remarks'                => 'nullable|string',
            'expected_complete_date' => 'nullable|date',
        ]);

        // Ensure the target member is assignable by this actor
        $member = User::where('team_id', $team->id)->findOrFail($request->user_id);
        abort_unless($this->canAssign($actor, $member), 403);

        $source = match (true) {
            $actor->hasRole('Leader')    => 'leader',
            $actor->hasRole('Co Leader') => 'co_leader',
            default                      => 'stack_lead',
        };

        $task = DailyTask::create([
            'team_id'                => $team->id,
            'user_id'                => $member->id,
            'created_by'             => $actor->id,
            'task_date'              => today(),
            'client_name'            => $request->client_name,
            'profile_name'           => $request->profile_name,
            'plan_details'           => $request->plan_details,
            'expected_complete_date' => $request->expected_complete_date,
            'source'                 => $source,
            'remarks'                => $request->remarks ?: null,
            'remarks_by'             => $request->filled('remarks') ? $actor->id : null,
            'remarks_updated_at'     => $request->filled('remarks') ? now() : null,
        ]);

        Notification::send($member, new AdminNotification(
            'New Task Assigned',
            $actor->name . ' (' . $task->task_by_label . ') assigned you a task for ' . $task->client_name . '.',
            'info',
            'ri-task-line'
        ));

        return redirect()->route('daily.task.all')->with('success', 'Task assigned successfully.');
    }

    // ────────────────────────────────────────────────────────────────────────
    // All Tasks  (Leader, Co Leader, Stack Lead)
    // ────────────────────────────────────────────────────────────────────────

    public function allTasks(Request $request)
    {
        $actor = $this->leadUser();
        $team  = Team::findOrFail($actor->team_id);

        $query = DailyTask::with(['user.stack', 'creator', 'remarksByUser'])
            ->forTeam($team->id)
            ->orderByDesc('task_date')
            ->orderByDesc('created_at');

        // Stack Lead scoped to own stack
        if ($actor->hasRole('Stack Lead') && !$actor->hasAnyRole(['Leader', 'Co Leader'])) {
            $stackMemberIds = User::where('team_id', $team->id)
                ->where('stack_id', $actor->stack_id)
                ->pluck('id');
            $query->whereIn('user_id', $stackMemberIds);
        }

        // Filters
        if ($request->filled('member_id')) {
            $query->where('user_id', $request->member_id);
        }
        if ($request->filled('stack_id')) {
            $query->whereHas('user', fn ($q) => $q->where('stack_id', $request->stack_id));
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('date')) {
            $query->whereDate('task_date', $request->date);
        }

        $tasks   = $query->get();
        $members = $this->assignableMembers($actor, $team);

        return view('admin.pages.daily-task.all-tasks', compact('tasks', 'members', 'actor', 'team'));
    }

    public function edit(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->with(['user'])->findOrFail($request->id);
        abort_unless($task->canBeEditedBy($actor), 403);

        return $this->success($task->load(['user', 'remarksByUser']), 'ok', 200);
    }

    public function update(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->findOrFail($request->id);
        abort_unless($task->canBeEditedBy($actor), 403);

        $request->validate([
            'client_name'            => 'required|string|max:255',
            'profile_name'           => 'required|string|max:255',
            'plan_details'           => 'required|string',
            'expected_complete_date' => 'nullable|date',
        ]);

        $task->update($request->only(['client_name', 'profile_name', 'plan_details', 'expected_complete_date']));

        return $this->success($task, 'Task updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->find($request->id);

        if (!$task) return $this->error([], 'Task not found', 404);
        abort_unless($task->canBeDeletedBy($actor), 403);

        $task->delete();

        return $this->success([], 'Task deleted successfully', 200);
    }

    public function updateRemarks(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->findOrFail($request->id);
        abort_unless($task->canRemarksBeEditedBy($actor), 403);

        $request->validate(['remarks' => 'nullable|string|max:2000']);

        $task->update([
            'remarks'            => $request->remarks,
            'remarks_by'         => $actor->id,
            'remarks_updated_at' => now(),
        ]);

        return $this->success($task->load('remarksByUser'), 'Remarks updated successfully', 200);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Completed Tasks
    // ────────────────────────────────────────────────────────────────────────

    public function completedTasks(Request $request)
    {
        $actor  = $this->teamUser();
        $isLead = $actor->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);

        $date = $request->filled('date') && $isLead
            ? Carbon::parse($request->date)
            : today();

        $query = DailyTask::with(['user.stack', 'creator', 'remarksByUser'])
            ->forTeam($actor->team_id)
            ->completed()
            ->whereDate('completed_at', $date)
            ->orderByDesc('completed_at');

        // Non-leads only see their own completed tasks
        if (!$isLead) {
            $query->forUser($actor->id);
        }

        // Stack Lead scoped to own stack
        if ($actor->hasRole('Stack Lead') && !$actor->hasAnyRole(['Leader', 'Co Leader'])) {
            $stackMemberIds = User::where('team_id', $actor->team_id)
                ->where('stack_id', $actor->stack_id)
                ->pluck('id');
            $query->whereIn('user_id', $stackMemberIds);
        }

        $tasks = $query->get();

        return view('admin.pages.daily-task.completed', compact('tasks', 'date', 'isLead'));
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────────

    private function assignableMembers(User $actor, Team $team)
    {
        $query = User::with('stack')
            ->where('team_id', $team->id)
            ->where('is_request', false);

        if ($actor->hasRole('Stack Lead') && !$actor->hasAnyRole(['Leader', 'Co Leader'])) {
            // Stack Lead can only assign within their own stack
            $query->where('stack_id', $actor->stack_id)
                  ->where('id', '!=', $actor->id);
        } elseif ($actor->hasRole('Co Leader')) {
            // Co Leader can assign to everyone including Leader
            $query->where('id', '!=', $actor->id);
        } else {
            // Leader can assign to everyone except themselves
            $query->where('id', '!=', $actor->id);
        }

        return $query->orderBy('name')->get();
    }

    private function canAssign(User $actor, User $member): bool
    {
        if ($actor->team_id !== $member->team_id) return false;
        if ($actor->id === $member->id) return false;

        if ($actor->hasAnyRole(['Leader', 'Co Leader'])) return true;

        if ($actor->hasRole('Stack Lead')) {
            return $actor->stack_id !== null && $actor->stack_id === $member->stack_id;
        }

        return false;
    }
}
