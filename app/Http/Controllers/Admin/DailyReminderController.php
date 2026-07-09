<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyReminder\StoreAssignedReminderRequest;
use App\Http\Requests\DailyReminder\StoreReminderRequest;
use App\Http\Requests\DailyReminder\UpdateReminderRequest;
use App\Models\DailyReminder;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Traits\AjaxResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DailyReminderController extends Controller
{
    use AjaxResponse;

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * Only Leader, Co Leader may assign or cancel reminders for team members.
     */
    private function leaderTeam(): Team
    {
        $user = $this->currentUser();
        abort_unless($user->team_id && $user->hasAnyRole(['Leader', 'Co Leader']), 403);

        return Team::findOrFail($user->team_id);
    }

    public function createForm()
    {
        $this->currentUser();

        return view('admin.pages.daily-reminder.create');
    }

    public function store(StoreReminderRequest $request)
    {
        $user = $this->currentUser();

        DailyReminder::create([
            'user_id' => $user->id,
            'created_by' => $user->id,
            'team_id' => $user->team_id,
            'details' => $request->details,
            'due_date' => $request->due_date,
            'source' => 'personal',
        ]);

        return redirect()->route('daily.reminder.my.list')->with('success', 'Reminder created successfully.');
    }

    public function myList()
    {
        $user = $this->currentUser();

        $reminders = DailyReminder::forUser($user->id)->orderBy('due_date')->get();

        return view('admin.pages.daily-reminder.my-list', compact('reminders'));
    }

    public function edit($id)
    {
        $user = $this->currentUser();

        $reminder = DailyReminder::findOrFail($id);
        abort_unless($reminder->isEditableBy($user), 403);

        return view('admin.pages.daily-reminder.edit', compact('reminder'));
    }

    public function update(UpdateReminderRequest $request)
    {
        $user = $this->currentUser();

        $reminder = DailyReminder::findOrFail($request->id);
        abort_unless($reminder->isEditableBy($user), 403);

        $reminder->update([
            'details' => $request->details,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('daily.reminder.my.list')->with('success', 'Reminder updated successfully.');
    }

    public function toggleComplete(Request $request)
    {
        $user = $this->currentUser();

        $request->validate(['id' => 'required|exists:daily_reminders,id']);

        $reminder = DailyReminder::find($request->id);

        if (!$reminder || !$reminder->isCompletableBy($user)) {
            return $this->error([], 'Reminder not found or not eligible for completion.', 404);
        }

        if ($reminder->isAssigned() && $reminder->created_by !== $reminder->user_id) {
            Notification::send($reminder->creator, new AdminNotification(
                'Reminder Completed',
                $user->name . ' completed the reminder: ' . $reminder->details,
                'success',
                'ri-alarm-line'
            ));
        }

        $reminder->delete();

        return $this->success([], 'Reminder marked as completed', 200);
    }

    public function assignForm()
    {
        $team = $this->leaderTeam();
        $actor = $this->currentUser();

        $members = User::where('team_id', $team->id)->where('id', '!=', $actor->id)->orderBy('name')->get();

        return view('admin.pages.daily-reminder.assign', compact('team', 'members'));
    }

    public function storeAssigned(StoreAssignedReminderRequest $request)
    {
        $team = $this->leaderTeam();
        $actor = $this->currentUser();

        $reminder = DailyReminder::create([
            'user_id' => $request->user_id,
            'created_by' => $actor->id,
            'team_id' => $team->id,
            'details' => $request->details,
            'due_date' => $request->due_date,
            'source' => 'assigned',
        ]);

        Notification::send($reminder->user, new AdminNotification(
            'New Reminder Assigned',
            $actor->name . ' assigned you a reminder: ' . $reminder->details,
            'info',
            'ri-alarm-line'
        ));

        return redirect()->route('daily.reminder.my.list')->with('success', 'Reminder assigned successfully.');
    }

    public function destroyAssigned(Request $request)
    {
        $team = $this->leaderTeam();
        $user = $this->currentUser();

        $reminder = DailyReminder::where('team_id', $team->id)->where('source', 'assigned')->find($request->id);

        if (!$reminder) {
            return $this->error([], 'Reminder not found', 404);
        }

        if (!$reminder->isCancelableBy($user)) {
            return $this->error([], 'You are not allowed to cancel this reminder.', 403);
        }

        $reminder->delete();

        return $this->success([], 'Reminder cancelled successfully', 200);
    }
}
