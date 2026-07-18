<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClientMessage;
use App\Models\DailyIssue;
use App\Models\DailyReminder;
use App\Models\DailyTask;
use App\Models\QuickAccessMenu;
use App\Models\Team;
use App\Models\TodayPlanTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    // Index
    public function index()
    {
        $data = [];
        $user = Auth::user();

        $data['myReminders'] = DailyReminder::forUser($user->id)->orderBy('due_date')->get();

        if ($user->team_id) {
            $team = Team::find($user->team_id);
            $members = User::where('team_id', $team->id)->with('stack')->orderBy('name')->get();

            $planRows = TodayPlanTask::where('team_id', $team->id)
                ->where('source', 'planned')
                ->whereDate('plan_date', today())
                ->get()
                ->groupBy('user_id');

            $issueUserIds = DB::table('daily_issue_responsibles')
                ->join('daily_issues', 'daily_issues.id', '=', 'daily_issue_responsibles.daily_issue_id')
                ->where('daily_issues.team_id', $team->id)
                ->where('daily_issues.status', 'pending')
                ->pluck('daily_issue_responsibles.user_id')
                ->unique();

            $data['team'] = $team;
            $data['activeAnnouncements'] = Announcement::where('team_id', $team->id)->active()->latest()->get();
            $data['totalMembers'] = $members->count();
            $data['stackBreakdown'] = $members->groupBy(fn ($m) => $m->stack->name ?? 'Unassigned')->map->count();
            $data['teamOverview'] = $members->map(function (User $member) use ($planRows, $issueUserIds) {
                $rows = $planRows->get($member->id, collect());
                $planStatus = match (true) {
                    $rows->contains('status', 'pending') => 'pending',
                    $rows->contains('status', 'approved') => 'approved',
                    $rows->isNotEmpty() => 'rejected',
                    default => 'not_submitted',
                };

                return [
                    'user' => $member,
                    'plan_status' => $planStatus,
                    'has_open_issue' => $issueUserIds->contains($member->id),
                ];
            });
            $data['openIssueCount'] = $issueUserIds->count();
            $data['pendingPlanCount'] = $planRows->filter(fn ($rows) => $rows->contains('status', 'pending'))->count();

            $data['pendingClientMessageCount'] = $user->hasAnyRole(['Leader', 'Co Leader'])
                ? ClientMessage::where('team_id', $team->id)->where('status', 'pending')->count()
                : 0;

            // Personal dashboard widgets
            $data['myTodayTasks'] = DailyTask::forUser($user->id)
                ->forTeam($team->id)
                ->forDate(today())
                ->pending()
                ->latest()
                ->get();

            $data['myPendingIssues'] = DailyIssue::with(['creator'])
                ->forTeam($team->id)
                ->pending()
                ->whereHas('responsibles', fn ($q) => $q->where('user_id', $user->id))
                ->latest()
                ->get();

            $data['myPendingClientMessages'] = ClientMessage::where('team_id', $team->id)
                ->where('submitted_by', $user->id)
                ->where('status', 'pending')
                ->count();

            $data['myPendingIssueCount'] = DB::table('daily_issue_responsibles')
                ->join('daily_issues', 'daily_issues.id', '=', 'daily_issue_responsibles.daily_issue_id')
                ->where('daily_issues.team_id', $team->id)
                ->where('daily_issues.status', 'pending')
                ->where('daily_issue_responsibles.user_id', $user->id)
                ->count();
        } else {
            $data['totalTeams'] = Team::count();
            $data['totalOrgMembers'] = User::where('is_request', false)->count();
        }

        return view('admin.pages.dashboard', $data);
    }
    public function inbox()
    {
        return view('admin.pages.inbox');
    }

    public function todoList()
    {
        return view('admin.pages.todo-list');
    }
    // Index
    public function addQuickAccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'nullable|string',
            'route' => 'required|string',
            'url' => 'required|string',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Failed!');
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();
        // Store the new quick access menu
        QuickAccessMenu::create($data);

        // Redirect or return a response
        return redirect()->back()->with('success', 'Added to quick menu successfully');
    }
    public function removeQuickAccess($route)
    {
        // Store the new quick access menu
        $r = QuickAccessMenu::where('route', $route)->delete();

        if($r){
            return redirect()->back()->with('success', 'Removed from quick menu successfully');
        }else{
            return redirect()->back()->with('error', 'Failed!');
        }
        // Redirect or return a response
    }
}
