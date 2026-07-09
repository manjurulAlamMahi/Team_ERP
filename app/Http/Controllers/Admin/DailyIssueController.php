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
        $clients = Client::with('profile')->forTeam($user->team_id)->assignedTo($user->id)->orderBy('username')->get();

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
            'issue' => $request->issue,
            'type' => $request->type,
            'category' => $request->category,
            'status' => 'pending',
        ]);

        $issue->responsibles()->sync($request->responsible_ids);

        $this->notifyResponsibles($issue, $user, User::whereIn('id', $request->responsible_ids)->get());

        return redirect()->route('daily.issue.list')->with('success', 'Issue created successfully.');
    }

    public function edit($id)
    {
        $user = $this->currentTeamUser();

        $issue = DailyIssue::with('responsibles')->forTeam($user->team_id)->findOrFail($id);
        abort_unless($issue->isEditableBy($user), 403);

        $members = $this->responsibleMembers($user->team_id);
        $types = ['Critical', 'Urgent', 'High', 'Normal'];
        $categories = self::CATEGORIES;
        $clients = Client::with('profile')->forTeam($user->team_id)->assignedTo($user->id)->orderBy('username')->get();

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

    public function list()
    {
        $user = $this->currentTeamUser();

        $issues = DailyIssue::with(['responsibles', 'creator', 'lastEditor'])
            ->forTeam($user->team_id)
            ->pending()
            ->latest()
            ->get();

        return view('admin.pages.daily-issue.list', compact('issues'));
    }

    public function myIssues()
    {
        $user = $this->currentTeamUser();

        $issues = DailyIssue::with(['responsibles', 'creator', 'lastEditor'])
            ->forTeam($user->team_id)
            ->pending()
            ->whereHas('responsibles', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        return view('admin.pages.daily-issue.my-issues', compact('issues'));
    }

    public function completedList(Request $request)
    {
        $user   = $this->currentTeamUser();
        $isLead = $user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);

        $date = $request->filled('date') && $isLead
            ? Carbon::parse($request->date)
            : today();

        $query = DailyIssue::with(['responsibles', 'creator', 'completer'])
            ->forTeam($user->team_id)
            ->completed()
            ->whereDate('completed_at', $date);

        if (!$isLead) {
            $query->where('completed_by', $user->id);
        }

        $issues = $query->latest('completed_at')->get();

        return view('admin.pages.daily-issue.completed', compact('issues', 'date', 'isLead'));
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
