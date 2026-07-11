<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TodayPlan\StoreAssignedTaskRequest;
use App\Http\Requests\TodayPlan\StorePlanRequest;
use App\Http\Requests\TodayPlan\UpdatePlanItemRequest;
use App\Models\Client;
use App\Models\Team;
use App\Models\TodayPlanTask;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class TodayPlanController extends Controller
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
     * Co Leader, Stack Lead, Member, Probation submit daily plans. Leader never submits.
     */
    private function submitterUser(): User
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasAnyRole(['Co Leader', 'Stack Lead', 'Member', 'Probation']), 403);

        return $user;
    }

    /**
     * Only Leader (not Co Leader, not Stack Lead) reviews, assigns, and monitors.
     */
    private function leaderTeam(): Team
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasRole('Leader'), 403);

        return Team::findOrFail($user->team_id);
    }

    public function createForm()
    {
        $user = $this->submitterUser();

        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.today-plan.create', compact('clients'));
    }

    public function store(StorePlanRequest $request)
    {
        $user = $this->submitterUser();

        $clientsById = Client::with('profile')
            ->forTeam($user->team_id)
            ->get()
            ->keyBy('id');

        foreach ($request->input('items') as $item) {
            $client = $clientsById->get($item['client_id']);

            TodayPlanTask::create([
                'team_id' => $user->team_id,
                'user_id' => $user->id,
                'created_by' => $user->id,
                'plan_date' => today(),
                'client_id' => $client->id,
                'client_name' => $client->client_name ?: $client->username,
                'profile_name' => $client->profile->name ?? '',
                'details' => $item['details'],
                'source' => 'planned',
                'status' => 'pending',
            ]);
        }

        $this->notifyLeader($user->team_id, $user);

        return redirect()->route('today.plan.my.plans')->with('success', "Today's plan submitted for approval.");
    }

    public function edit($id)
    {
        $user = $this->submitterUser();

        $task = TodayPlanTask::where('user_id', $user->id)->where('source', 'planned')->findOrFail($id);
        abort_unless($task->isEditableBy($user), 403);

        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.today-plan.edit', ['task' => $task, 'clients' => $clients]);
    }

    public function update(UpdatePlanItemRequest $request)
    {
        $user = $this->submitterUser();

        $task = TodayPlanTask::where('user_id', $user->id)->where('source', 'planned')->findOrFail($request->id);
        abort_unless($task->isEditableBy($user), 403);

        $client = Client::with('profile')->forTeam($user->team_id)->findOrFail($request->client_id);

        $task->update([
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'details' => $request->details,
        ]);

        return redirect()->route('today.plan.my.plans')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->submitterUser();

        $task = TodayPlanTask::where('user_id', $user->id)->where('source', 'planned')->find($request->id);

        if (!$task) {
            return $this->error([], 'Plan not found', 404);
        }

        if (!$task->isEditableBy($user)) {
            return $this->error([], 'This plan can no longer be deleted.', 403);
        }

        $task->delete();

        return $this->success([], 'Plan deleted successfully', 200);
    }

    public function myPlans()
    {
        $user = $this->currentTeamUser();

        $tasks = TodayPlanTask::forUser($user->id)->forDate(today())->latest()->get();

        $approvedPlanned = $tasks->where('source', 'planned')->where('status', 'approved')->values();
        $pendingPlanned = $tasks->where('source', 'planned')->where('status', 'pending')->values();
        $rejectedPlanned = $tasks->where('source', 'planned')->where('status', 'rejected')->values();
        $leaderAssigned = $tasks->where('source', 'leader_assigned')->values();
        $personal = $tasks->where('source', 'personal')->values();
        $checklist = $tasks->where('status', 'approved')->values();
        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.today-plan.my-plans', compact(
            'approvedPlanned', 'pendingPlanned', 'rejectedPlanned', 'leaderAssigned', 'personal', 'checklist', 'clients'
        ));
    }

    public function toggleComplete(Request $request)
    {
        $user = $this->currentTeamUser();

        $request->validate(['id' => 'required|exists:today_plan_tasks,id']);

        $task = TodayPlanTask::where('user_id', $user->id)->find($request->id);

        if (!$task || !$task->isCompletableBy($user)) {
            return $this->error([], 'Task not found or not completable.', 404);
        }

        $completed = !$task->is_completed;

        $task->update([
            'is_completed' => $completed,
            'completed_at' => $completed ? now() : null,
            'leader_verified' => null,
            'completion_comment' => null,
        ]);

        return $this->success($task, 'Task updated successfully', 200);
    }

    public function storePersonalTask(Request $request)
    {
        $user = $this->currentTeamUser();

        $request->validate([
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where('team_id', $user->team_id),
            ],
            'details' => ['required', 'string', 'max:2000'],
        ]);

        $client = Client::with('profile')->findOrFail($request->client_id);

        $task = TodayPlanTask::create([
            'team_id' => $user->team_id,
            'user_id' => $user->id,
            'created_by' => $user->id,
            'plan_date' => today(),
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'details' => $request->details,
            'source' => 'personal',
            'status' => 'approved',
        ]);

        return $this->success($task, 'Personal task added successfully', 200);
    }

    public function destroyPersonalTask(Request $request)
    {
        $user = $this->currentTeamUser();

        $task = TodayPlanTask::where('user_id', $user->id)->where('source', 'personal')->find($request->id);

        if (!$task) {
            return $this->error([], 'Task not found', 404);
        }

        $task->delete();

        return $this->success([], 'Task deleted successfully', 200);
    }

    public function reviewList()
    {
        $team = $this->leaderTeam();

        $tasks = TodayPlanTask::with('user')
            ->forTeam($team->id)
            ->where('source', 'planned')
            ->where('status', 'pending')
            ->forDate(today())
            ->latest()
            ->get();

        return view('admin.pages.today-plan.review-list', compact('team', 'tasks'));
    }

    public function reviewHistory()
    {
        $team = $this->leaderTeam();

        $tasks = TodayPlanTask::with(['user', 'reviewer'])
            ->forTeam($team->id)
            ->where('source', 'planned')
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->get();

        return view('admin.pages.today-plan.review-history', compact('team', 'tasks'));
    }

    public function reviewShow($id)
    {
        $team = $this->leaderTeam();

        $task = TodayPlanTask::with(['user', 'reviewer'])
            ->forTeam($team->id)
            ->where('source', 'planned')
            ->findOrFail($id);

        return view('admin.pages.today-plan.review-show', ['task' => $task]);
    }

    public function approve(Request $request)
    {
        $team = $this->leaderTeam();

        $request->validate([
            'id' => 'required|exists:today_plan_tasks,id',
            'comment' => 'nullable|string|max:1000',
        ]);

        $task = TodayPlanTask::forTeam($team->id)->where('source', 'planned')->where('status', 'pending')->find($request->id);
        if (!$task) {
            return $this->error([], 'Plan not found or already reviewed.', 404);
        }

        $task->update([
            'status' => 'approved',
            'review_comment' => $request->comment,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::send($task->user, new AdminNotification(
            'Plan Approved',
            'Your plan for ' . $task->client_name . ' was approved by ' . Auth::user()->name . '.',
            'success',
            'ri-checkbox-circle-line'
        ));

        return $this->success($task, 'Plan approved successfully', 200);
    }

    public function reject(Request $request)
    {
        $team = $this->leaderTeam();

        $request->validate([
            'id' => 'required|exists:today_plan_tasks,id',
            'comment' => 'nullable|string|max:1000',
        ]);

        $task = TodayPlanTask::forTeam($team->id)->where('source', 'planned')->where('status', 'pending')->find($request->id);
        if (!$task) {
            return $this->error([], 'Plan not found or already reviewed.', 404);
        }

        $task->update([
            'status' => 'rejected',
            'review_comment' => $request->comment,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::send($task->user, new AdminNotification(
            'Plan Rejected',
            'Your plan for ' . $task->client_name . ' was rejected by ' . Auth::user()->name . '.' . ($request->comment ? ' Reason: ' . $request->comment : ''),
            'danger',
            'ri-close-circle-line'
        ));

        return $this->success($task, 'Plan rejected successfully', 200);
    }

    public function storeAssigned(StoreAssignedTaskRequest $request)
    {
        $team = $this->leaderTeam();

        $client = Client::with('profile')->forTeam($team->id)->findOrFail($request->client_id);

        $task = TodayPlanTask::create([
            'team_id' => $team->id,
            'user_id' => $request->user_id,
            'created_by' => Auth::id(),
            'plan_date' => today(),
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'details' => $request->details,
            'source' => 'leader_assigned',
            'status' => 'approved',
        ]);

        Notification::send($task->user, new AdminNotification(
            'New Task Assigned by Leader',
            Auth::user()->name . ' assigned you a new task for ' . $task->client_name . '.',
            'info',
            'ri-user-add-line'
        ));

        return $this->success($task, 'Task assigned successfully', 200);
    }

    public function editAssigned($id)
    {
        $team = $this->leaderTeam();
        $user = Auth::user();

        $task = TodayPlanTask::where('source', 'leader_assigned')->forTeam($team->id)->findOrFail($id);
        abort_unless($task->isEditableBy($user), 403);

        $clients = Client::with('profile')->forTeam($team->id)->orderBy('username')->get();

        return view('admin.pages.today-plan.edit', ['task' => $task, 'clients' => $clients]);
    }

    public function updateAssigned(UpdatePlanItemRequest $request)
    {
        $team = $this->leaderTeam();
        $user = Auth::user();

        $task = TodayPlanTask::where('source', 'leader_assigned')->forTeam($team->id)->findOrFail($request->id);
        abort_unless($task->isEditableBy($user), 403);

        $client = Client::with('profile')->forTeam($team->id)->findOrFail($request->client_id);

        $task->update([
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'details' => $request->details,
        ]);

        return redirect()->route('today.plan.dashboard')->with('success', 'Assigned task updated successfully.');
    }

    public function destroyAssigned(Request $request)
    {
        $team = $this->leaderTeam();
        $user = Auth::user();

        $task = TodayPlanTask::where('source', 'leader_assigned')->forTeam($team->id)->find($request->id);

        if (!$task) {
            return $this->error([], 'Task not found', 404);
        }

        if (!$task->isEditableBy($user)) {
            return $this->error([], 'This task can no longer be removed.', 403);
        }

        $task->delete();

        return $this->success([], 'Task removed successfully', 200);
    }

    public function dashboard()
    {
        $team = $this->leaderTeam();

        $members = User::where('team_id', $team->id)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Co Leader', 'Stack Lead', 'Member', 'Probation']))
            ->orderBy('name')
            ->get();

        $clients = Client::with('profile')->forTeam($team->id)->orderBy('username')->get();

        $tasksByUser = TodayPlanTask::forTeam($team->id)->forDate(today())->get()->groupBy('user_id');

        $summaries = $members->map(function (User $member) use ($tasksByUser) {
            $memberTasks = $tasksByUser->get($member->id, collect());

            return [
                'user' => $member,
                'approved_planned' => $memberTasks->where('source', 'planned')->where('status', 'approved')->count(),
                'pending_planned' => $memberTasks->where('source', 'planned')->where('status', 'pending')->count(),
                'leader_assigned' => $memberTasks->where('source', 'leader_assigned')->count(),
                'personal' => $memberTasks->where('source', 'personal')->count(),
                'completed' => $memberTasks->where('status', 'approved')->where('is_completed', true)->count(),
                'pending_completion' => $memberTasks->where('status', 'approved')->where('is_completed', false)->count(),
            ];
        });

        return view('admin.pages.today-plan.dashboard', compact('team', 'members', 'clients', 'summaries'));
    }

    public function memberDetail($userId)
    {
        $team = $this->leaderTeam();

        $member = User::where('team_id', $team->id)->findOrFail($userId);

        $tasks = TodayPlanTask::with('reviewer')
            ->forTeam($team->id)
            ->forUser($member->id)
            ->forDate(today())
            ->latest()
            ->get();

        return view('admin.pages.today-plan.member-detail', compact('team', 'member', 'tasks'));
    }

    public function verifyComplete(Request $request)
    {
        $team = $this->leaderTeam();
        $user = Auth::user();

        $request->validate(['id' => 'required|exists:today_plan_tasks,id']);

        $task = TodayPlanTask::forTeam($team->id)->find($request->id);

        if (!$task || !$task->isVerifiableBy($user)) {
            return $this->error([], 'Task not found or not eligible for verification.', 404);
        }

        $task->update(['leader_verified' => true]);

        return $this->success($task, 'Task marked as verified', 200);
    }

    public function reopenTask(Request $request)
    {
        $team = $this->leaderTeam();
        $user = Auth::user();

        $request->validate([
            'id' => 'required|exists:today_plan_tasks,id',
            'comment' => 'nullable|string|max:1000',
        ]);

        $task = TodayPlanTask::forTeam($team->id)->find($request->id);

        if (!$task || !$task->isVerifiableBy($user)) {
            return $this->error([], 'Task not found or not eligible for reopening.', 404);
        }

        $task->update([
            'is_completed' => false,
            'completed_at' => null,
            'leader_verified' => false,
            'completion_comment' => $request->comment,
        ]);

        Notification::send($task->user, new AdminNotification(
            'Task Reopened by Leader',
            Auth::user()->name . ' reopened your task for ' . $task->client_name . '.' . ($request->comment ? ' Comment: ' . $request->comment : ''),
            'warning',
            'ri-refresh-line'
        ));

        return $this->success($task, 'Task reopened successfully', 200);
    }

    private function notifyLeader(int $teamId, User $submitter): void
    {
        $leaders = User::where('team_id', $teamId)
            ->whereHas('roles', fn ($q) => $q->where('name', 'Leader'))
            ->get();

        if ($leaders->isEmpty()) {
            return;
        }

        Notification::send($leaders, new AdminNotification(
            'New Daily Plan Awaiting Approval',
            $submitter->name . " submitted today's plan for review.",
            'info',
            'ri-calendar-todo-line'
        ));
    }
}
