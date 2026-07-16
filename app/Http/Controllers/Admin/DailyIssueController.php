<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyIssue\StoreIssueRequest;
use App\Http\Requests\DailyIssue\UpdateIssueRequest;
use App\Models\Client;
use App\Models\DailyIssue;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DailyIssueController extends Controller
{
    use AjaxResponse;

    private const CATEGORIES = [
        'Close Revision', 'Client Asking For Cancel', 'Send Extension', 'Delivery Approach',
        'Delivered Project', 'Client Hyper', 'Client Asking For Update', 'Send Update',
        'Check Profile', 'Need Reply', 'Check Telegram', 'Send Meeting Follow Up',
        'Send Last Message Follow Up', 'Ask For Meeting', 'Other',
    ];

    private function responsibleMembers(int $teamId)
    {
        return User::where('team_id', $teamId)
            ->where('is_request', false)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation']))
            ->orderBy('name')
            ->get();
    }

    /**
     * Only Leader/Co Leader/Stack Lead can create issues, so those are the only
     * possible values for the "Assigned By" filter.
     */
    private function assignerMembers(int $teamId)
    {
        return User::where('team_id', $teamId)
            ->where('is_request', false)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Leader', 'Co Leader', 'Stack Lead']))
            ->orderBy('name')
            ->get();
    }

    private function currentTeamUser(): User
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->team_id, 403);

        return $user;
    }

    /**
     * Only Leader, Co Leader, Stack Lead may create issues.
     */
    private function creatorUser(): User
    {
        $user = $this->currentTeamUser();
        abort_unless($user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']), 403);

        return $user;
    }

    public function createForm()
    {
        $user = $this->creatorUser();

        $members = $this->responsibleMembers($user->team_id);
        $types = ['Critical', 'Urgent', 'High', 'Normal'];
        $categories = self::CATEGORIES;
        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.daily-issue.create', compact('members', 'types', 'categories', 'clients'));
    }

    public function store(StoreIssueRequest $request)
    {
        $user = $this->creatorUser();

        $client = Client::with('profile')->findOrFail($request->client_id);

        $issue = DailyIssue::create([
            'team_id' => $user->team_id,
            'created_by' => $user->id,
            'issue_date' => today(),
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'issue' => $request->issue ?: null,
            'type' => $request->type,
            'category' => $request->category,
            'status' => 'pending',
        ]);

        $issue->responsibles()->sync($request->responsible_ids);

        $this->notifyResponsibles($issue, $user, User::whereIn('id', $request->responsible_ids)->get());

        return $this->success(['redirect' => route('daily.issue.list')], 'Issue created successfully.', 200);
    }

    public function edit($id)
    {
        $user = $this->currentTeamUser();

        $issue = DailyIssue::with('responsibles')->forTeam($user->team_id)->findOrFail($id);
        abort_unless($issue->isEditableBy($user), 403);

        $members = $this->responsibleMembers($user->team_id);
        $types = ['Critical', 'Urgent', 'High', 'Normal'];
        $categories = self::CATEGORIES;
        $clients = Client::with('profile')->forTeam($user->team_id)->orderBy('username')->get();

        return view('admin.pages.daily-issue.edit', compact('issue', 'members', 'types', 'categories', 'clients'));
    }

    public function update(UpdateIssueRequest $request)
    {
        $user = $this->currentTeamUser();

        $issue = DailyIssue::forTeam($user->team_id)->findOrFail($request->id);
        abort_unless($issue->isEditableBy($user), 403);

        $client = Client::with('profile')->findOrFail($request->client_id);

        $issue->update([
            'client_id' => $client->id,
            'client_name' => $client->client_name ?: $client->username,
            'profile_name' => $client->profile->name ?? '',
            'issue' => $request->issue,
            'type' => $request->type,
            'category' => $request->category,
            'last_edited_by' => $user->id,
        ]);

        $issue->responsibles()->sync($request->responsible_ids);

        return redirect()->route('daily.issue.list')->with('success', 'Issue updated successfully.');
    }

    public function destroy(Request $request)
    {
        $user = $this->currentTeamUser();

        $issue = DailyIssue::forTeam($user->team_id)->find($request->id);

        if (!$issue) {
            return $this->error([], 'Issue not found', 404);
        }

        if (!$issue->isDeletableBy($user)) {
            return $this->error([], 'Only the creator can delete this issue.', 403);
        }

        $issue->delete();

        return $this->success([], 'Issue deleted successfully', 200);
    }

    /**
     * Shared filter/sort logic for "All Issues" and "My Issues".
     *
     * Pending: never date-restricted (an issue from a week ago still shows),
     * sorted Critical > Urgent > High > Normal. Filterable by who assigned it
     * (created_by) and type, plus responsible person when $allowResponsibleFilter.
     *
     * Completed: defaults to "completed today" (by completed_at, not issue_date),
     * filterable by date/assigned-by/type, plus responsible person when allowed.
     */
    private function filteredIssues(Request $request, int $teamId, ?int $onlyResponsibleTo, bool $allowResponsibleFilter)
    {
        $status = $request->get('status') === 'completed' ? 'completed' : 'pending';

        $query = DailyIssue::with(['responsibles', 'creator', 'lastEditor', 'completer'])
            ->withCount('comments')
            ->forTeam($teamId);

        if ($onlyResponsibleTo) {
            $query->whereHas('responsibles', fn ($q) => $q->where('user_id', $onlyResponsibleTo));
        }

        $date = null;

        if ($status === 'completed') {
            $date = $request->filled('date') ? Carbon::parse($request->date) : today();
            $query->completed()->whereDate('completed_at', $date);
        } else {
            $query->pending();
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($allowResponsibleFilter && $request->filled('responsible_id')) {
            $query->whereHas('responsibles', fn ($q) => $q->where('user_id', $request->responsible_id));
        }

        if ($status === 'completed') {
            $query->latest('completed_at');
        } else {
            $query->orderByRaw("FIELD(type, 'Critical', 'Urgent', 'High', 'Normal')")->latest();
        }

        return [$query->get(), $status, $date ?? today()];
    }

    /**
     * "All Issues" — team-wide, filterable by status (pending/completed).
     */
    public function list(Request $request)
    {
        $user = $this->currentTeamUser();

        [$issues, $status, $date] = $this->filteredIssues($request, $user->team_id, null, true);

        $members = $this->responsibleMembers($user->team_id);
        $creators = $this->assignerMembers($user->team_id);
        $types = ['Critical', 'Urgent', 'High', 'Normal'];

        return view('admin.pages.daily-issue.list', compact('issues', 'status', 'members', 'creators', 'types', 'date'));
    }

    /**
     * "My Issues" — issues where the current user is a responsible person,
     * filterable by status (pending/completed).
     */
    public function myIssues(Request $request)
    {
        $user = $this->currentTeamUser();

        [$issues, $status, $date] = $this->filteredIssues($request, $user->team_id, $user->id, false);

        $creators = $this->assignerMembers($user->team_id);
        $types = ['Critical', 'Urgent', 'High', 'Normal'];

        return view('admin.pages.daily-issue.my-issues', compact('issues', 'status', 'creators', 'types', 'date'));
    }

    public function markComplete(Request $request)
    {
        $user = $this->currentTeamUser();

        $request->validate(['id' => 'required|exists:daily_issues,id']);

        $issue = DailyIssue::forTeam($user->team_id)->find($request->id);

        if (!$issue || !$issue->isCompletableBy($user)) {
            return $this->error([], 'Issue not found or not eligible for completion.', 404);
        }

        $issue->update([
            'status' => 'completed',
            'completed_by' => $user->id,
            'completed_at' => now(),
        ]);

        return $this->success($issue, 'Issue marked as completed', 200);
    }

    public function reverseComplete(Request $request)
    {
        $user = $this->currentTeamUser();

        $request->validate([
            'id' => 'required|exists:daily_issues,id',
            'comment' => 'required|string|max:1000',
        ]);

        $issue = DailyIssue::forTeam($user->team_id)->find($request->id);

        if (!$issue || !$issue->isReversibleBy($user)) {
            return $this->error([], 'Issue not found or not eligible for reversal.', 404);
        }

        $issue->update([
            'status' => 'pending',
            'completed_by' => null,
            'completed_at' => null,
        ]);

        $issue->comments()->create([
            'user_id' => $user->id,
            'comment' => $request->comment,
            'type' => 'reopen',
        ]);

        return $this->success($issue, 'Issue reversed to not completed', 200);
    }

    public function storeComment(Request $request)
    {
        $user = $this->currentTeamUser();

        $request->validate([
            'id' => 'required|exists:daily_issues,id',
            'comment' => 'required|string|max:1000',
        ]);

        $issue = DailyIssue::forTeam($user->team_id)->find($request->id);

        if (!$issue || !$issue->canCommentBy($user)) {
            return $this->error([], 'You are not allowed to comment on this issue.', 403);
        }

        $comment = $issue->comments()->create([
            'user_id' => $user->id,
            'comment' => $request->comment,
            'type' => 'comment',
        ]);

        return $this->success($comment->load('user'), 'Comment added successfully', 200);
    }

    public function comments($id)
    {
        $user = $this->currentTeamUser();

        $issue = DailyIssue::forTeam($user->team_id)->findOrFail($id);

        $comments = $issue->comments()->with('user')->get();

        return $this->success($comments, 'Comments fetched successfully', 200);
    }

    private function notifyResponsibles(DailyIssue $issue, User $creator, $responsibles): void
    {
        if ($responsibles->isEmpty()) {
            return;
        }

        Notification::send($responsibles, new AdminNotification(
            'New Issue Assigned',
            $creator->name . ' assigned you to an issue for ' . $issue->client_name . '.',
            'warning',
            'ri-alert-line'
        ));
    }
}
