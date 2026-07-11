<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
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
        $user = $this->teamUser();

        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.daily-task.add', compact('clients'));
    }

    public function store(Request $request)
    {
        $user = $this->teamUser();

        $request->validate([
            'client_id'              => ['required', 'integer', Rule::exists('clients', 'id')->where('team_id', $user->team_id)],
            'plan_details'           => 'required|string',
            'expected_complete_date' => 'nullable|date|after_or_equal:today',
        ]);

        $client = Client::with('profile')->findOrFail($request->client_id);

        DailyTask::create([
            'team_id'                => $user->team_id,
            'user_id'                => $user->id,
            'created_by'             => $user->id,
            'task_date'              => today(),
            'client_id'              => $client->id,
            'client_name'            => $client->client_name ?: $client->username,
            'profile_name'           => $client->profile->name ?? '',
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
        $clients = Client::with('profile')->forTeam($team->id)->orderBy('username')->get();

        return view('admin.pages.daily-task.assign', compact('team', 'members', 'clients'));
    }

    public function storeAssigned(Request $request)
    {
        $actor = $this->leadUser();
        $team  = Team::findOrFail($actor->team_id);

        $request->validate([
            'user_id'                => ['required', 'exists:users,id'],
            'client_id'              => ['required', 'integer', 'exists:clients,id'],
            'plan_details'           => 'required|string',
            'remarks'                => 'nullable|string',
            'expected_complete_date' => 'nullable|date',
        ]);

        // Ensure the target member is assignable by this actor
        $member = User::where('team_id', $team->id)->findOrFail($request->user_id);
        abort_unless($this->canAssign($actor, $member), 403);

        $client = Client::with('profile')->forTeam($team->id)->find($request->client_id);
        abort_unless($client, 422);

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
            'client_id'              => $client->id,
            'client_name'            => $client->client_name ?: $client->username,
            'profile_name'           => $client->profile->name ?? '',
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

        // Default to "Today's Tasks" when no date range is specified.
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : today();
        $endDate   = $request->filled('end_date') ? Carbon::parse($request->end_date) : today();

        $query = DailyTask::with(['user.stack', 'creator', 'remarksByUser'])
            ->forTeam($team->id)
            ->whereDate('task_date', '>=', $startDate)
            ->whereDate('task_date', '<=', $endDate)
            ->orderByDesc('task_date')
            ->orderByDesc('created_at');

        // Stack Lead scoped to own stack (their own tasks stay included, since
        // a Stack Lead's user row is itself part of their own stack).
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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks   = $query->paginate(20)->withQueryString();
        $members = $this->assignableMembers($actor, $team);

        return view('admin.pages.daily-task.all-tasks', compact('tasks', 'members', 'actor', 'team', 'startDate', 'endDate'));
    }

    public function edit(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->with(['user'])->findOrFail($request->id);
        abort_unless($task->canBeEditedBy($actor), 403);

        $clients = Client::with('profile')
            ->forTeam($actor->team_id)
            ->orderBy('username')
            ->get()
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'label' => ($client->client_name ?: $client->username) . ' - ' . ($client->profile->name ?? 'N/A'),
                'profile' => $client->profile->name ?? '',
            ]);

        $data = $task->load(['user', 'remarksByUser'])->toArray();
        $data['assignable_clients'] = $clients;

        return $this->success($data, 'ok', 200);
    }

    public function update(Request $request)
    {
        $actor = $this->leadUser();
        $task  = DailyTask::forTeam($actor->team_id)->findOrFail($request->id);
        abort_unless($task->canBeEditedBy($actor), 403);

        $request->validate([
            'client_id'              => ['required', 'integer', 'exists:clients,id'],
            'plan_details'           => 'required|string',
            'expected_complete_date' => 'nullable|date',
        ]);

        $client = Client::with('profile')->forTeam($actor->team_id)->find($request->client_id);
        abort_unless($client, 422);

        $task->update([
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'plan_details' => $request->plan_details,
            'expected_complete_date' => $request->expected_complete_date,
        ]);

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
