@extends('admin.master')

@section('title', 'Dashboard')
@section('quickAccessicon', 'ri-dashboard-2-fill')

@push('style')
    <style>
        .stat-card-link {
            text-decoration: none;
            display: block;
        }

        .stat-card-link .card {
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .stat-card-link:hover .card {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
        }

        .team-logo-frame {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            object-fit: cover;
            background: #f0f2f8;
            flex-shrink: 0;
        }

        .team-logo-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: #eef1fb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sheet-row-link {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sheet-row-link:hover {
            color: #0ab39c;
        }
    </style>
@endpush

@section('content')

    {{-- Alerts --}}
    @if (!($profileComplete ?? true))
        <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <span><i class="ri-error-warning-line me-1"></i> Your profile is incomplete. Please add your phone, address,
                designation, and date of birth.</span>
            <a href="{{ route('profile.index') }}" class="btn btn-sm btn-warning">Complete Profile</a>
        </div>
    @endif

    @if (isset($team))
        @php
            $isLead = Auth::user()->hasAnyRole(['Leader', 'Co Leader']);
        @endphp

        {{-- ── TEAM MEMBER / LEADER DASHBOARD ─────────────────────── --}}
        <div class="row g-3">

            {{-- ── COL 6: GOOD MORNING COLUMN ────────────────────── --}}
            <div class="col-lg-6">
                {{-- Greeting --}}
                <div class="card mb-3">
                    <div class="card-body row align-items-center">
                        <div class="col-lg-6">
                            @if (Auth::user()->email_verified_at == null)
                                <div class="alert alert-warning py-1 px-2 fs-12 mb-2">
                                    <strong>Email not verified.</strong>
                                    <a href="{{ route('email.verify') }}" class="text-warning-emphasis fw-medium">Verify
                                        now</a>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if ($greetings == 'Good Morning!')
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/004-sunrise.png') }}"
                                        alt="">
                                @elseif ($greetings == 'Good Afternoon!')
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/002-sunsets.png') }}"
                                        alt="">
                                @else
                                    <img width="50"
                                        src="{{ asset('admin/assets/images/greetings/003-cloudy-night.png') }}"
                                        alt="">
                                @endif
                                <div>
                                    <h5 class="text-primary mb-0">{{ $greetings }}</h5>
                                    <p class="fs-13 mb-0">{{ Auth::user()->name }} —
                                        {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
                                </div>
                            </div>
                            @if (!empty($eventMessages))
                                @foreach ($eventMessages as $event)
                                    <div class="alert alert-info py-1 px-2 fs-12 mb-1">🎉 {{ $event->message }}
                                    </div>
                                @endforeach
                            @endif
                            @if (!empty($upcomingEvents) && $upcomingEvents->isNotEmpty())
                                @foreach ($upcomingEvents as $event)
                                    <p class="fs-12 text-muted mb-0">
                                        📅 <strong>{{ $event->name }}</strong>
                                        @if ($event->name !== 'Birthday')
                                            on {{ \Carbon\Carbon::parse($event->start_date)->format('M d') }}
                                        @endif
                                    </p>
                                @endforeach
                            @endif
                        </div>
                        <div class="col-lg-6"></div>
                    </div>
                </div>

                {{-- Stat / action cards --}}
                <div class="row g-3 mb-3">
                    @if ($isLead)
                        <div class="col-4">
                            <a href="{{ route('daily.task.assign') }}" class="stat-card-link">
                                <div class="card widget-icon-box text-bg-success mb-0 h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">Assign
                                                    Tasks</h5>
                                                <h3 class="my-2 text-white">{{ $teamPendingTaskCount ?? 0 }}</h3>
                                                <p class="mb-0 text-white text-opacity-75 fs-13">Pending today</p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3">
                                                    <i class="ri-task-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('daily.issue.create') }}" class="stat-card-link">
                                <div
                                    class="card widget-icon-box mb-0 h-100 {{ ($openIssueCount ?? 0) > 0 ? 'text-bg-danger' : 'text-bg-info' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">Add Issue
                                                </h5>
                                                <h3 class="my-2 text-white">{{ $openIssueCount ?? 0 }}</h3>
                                                <p class="mb-0 text-white text-opacity-75 fs-13">Open in team</p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3">
                                                    <i class="ri-alert-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('client.message.review.list') }}" class="stat-card-link">
                                <div
                                    class="card widget-icon-box mb-0 h-100 {{ ($pendingClientMessageCount ?? 0) > 0 ? 'text-bg-warning' : 'text-bg-primary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5
                                                    class="text-uppercase fs-13 mt-0 {{ ($pendingClientMessageCount ?? 0) > 0 ? 'text-dark' : 'text-white text-opacity-75' }}">
                                                    Check Message</h5>
                                                <h3
                                                    class="my-2 {{ ($pendingClientMessageCount ?? 0) > 0 ? 'text-dark' : 'text-white' }}">
                                                    {{ $pendingClientMessageCount ?? 0 }}</h3>
                                                <p
                                                    class="mb-0 fs-13 {{ ($pendingClientMessageCount ?? 0) > 0 ? 'text-dark opacity-75' : 'text-white text-opacity-75' }}">
                                                    {{ ($pendingClientMessageCount ?? 0) > 0 ? 'Pending review' : 'All clear' }}
                                                </p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span
                                                    class="avatar-title bg-white bg-opacity-25 {{ ($pendingClientMessageCount ?? 0) > 0 ? 'text-dark' : 'text-white' }} rounded-3 fs-3">
                                                    <i class="ri-mail-send-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="col-4">
                            <a href="{{ route('daily.task.my') }}" class="stat-card-link">
                                <div class="card widget-icon-box text-bg-success mb-0 h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">My Tasks
                                                </h5>
                                                <h3 class="my-2 text-white">{{ ($myTodayTasks ?? collect())->count() }}
                                                </h3>
                                                <p class="mb-0 text-white text-opacity-75 fs-13">Pending today</p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3">
                                                    <i class="ri-task-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('daily.issue.my') }}" class="stat-card-link">
                                <div
                                    class="card widget-icon-box mb-0 h-100 {{ ($myPendingIssues ?? collect())->isNotEmpty() ? 'text-bg-danger' : 'text-bg-info' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">My Issue
                                                </h5>
                                                <h3 class="my-2 text-white">{{ ($myPendingIssues ?? collect())->count() }}
                                                </h3>
                                                <p class="mb-0 text-white text-opacity-75 fs-13">Assigned to me</p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3">
                                                    <i class="ri-alert-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('client.message.my.list') }}" class="stat-card-link">
                                <div
                                    class="card widget-icon-box mb-0 h-100 {{ ($myPendingClientMessages ?? 0) > 0 ? 'text-bg-warning' : 'text-bg-primary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5
                                                    class="text-uppercase fs-13 mt-0 {{ ($myPendingClientMessages ?? 0) > 0 ? 'text-dark' : 'text-white text-opacity-75' }}">
                                                    Pending Message</h5>
                                                <h3
                                                    class="my-2 {{ ($myPendingClientMessages ?? 0) > 0 ? 'text-dark' : 'text-white' }}">
                                                    {{ $myPendingClientMessages ?? 0 }}</h3>
                                                <p
                                                    class="mb-0 fs-13 {{ ($myPendingClientMessages ?? 0) > 0 ? 'text-dark opacity-75' : 'text-white text-opacity-75' }}">
                                                    {{ ($myPendingClientMessages ?? 0) > 0 ? 'Awaiting approval' : 'All clear' }}
                                                </p>
                                            </div>
                                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                                <span
                                                    class="avatar-title bg-white bg-opacity-25 {{ ($myPendingClientMessages ?? 0) > 0 ? 'text-dark' : 'text-white' }} rounded-3 fs-3">
                                                    <i class="ri-mail-send-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Reminders --}}
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <span class="fw-semibold"><i class="ri-alarm-line me-1 text-primary"></i> Today's Reminders</span>
                        <span class="text-muted fs-12">{{ now()->format('d M') }}</span>
                    </div>
                    <div class="card-body p-0" style="overflow-y:auto;max-height:340px;">

                        {{-- Daily Reminders --}}
                        @if (($myReminders ?? collect())->isNotEmpty())
                            <div class="px-3 pt-3 pb-1">
                                <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                                    <i class="ri-alarm-line text-warning"></i> Daily Reminders
                                    <span
                                        class="badge bg-warning text-dark ms-1">{{ ($myReminders ?? collect())->count() }}</span>
                                </div>
                                @foreach ($myReminders ?? [] as $reminder)
                                    <div class="d-flex align-items-start gap-2 mb-2 p-2 rounded"
                                        style="background:#fff8e1;">
                                        <i class="ri-circle-fill text-warning mt-1 flex-shrink-0"
                                            style="font-size:7px;"></i>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fs-13" title="{{ $reminder->daysLeftLabel() }}">
                                                {{ Str::limit($reminder->daysLeftLabel(), 70) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                <a href="{{ route('daily.reminder.my.list') }}"
                                    class="fs-12 text-warning fw-medium d-block text-end mb-2">View all →</a>
                            </div>
                            <hr class="my-0">
                        @endif

                        {{-- Pending Tasks --}}
                        @if (($myTodayTasks ?? collect())->isNotEmpty())
                            <div class="px-3 pt-3 pb-1">
                                <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                                    <i class="ri-task-line text-primary"></i> Pending Tasks
                                    <span class="badge bg-primary ms-1">{{ ($myTodayTasks ?? collect())->count() }}</span>
                                </div>
                                @foreach ($myTodayTasks ?? [] as $task)
                                    <div class="d-flex align-items-start gap-2 mb-2 p-2 rounded"
                                        style="background:#f0f5ff;">
                                        <i class="ri-circle-fill text-primary mt-1 flex-shrink-0"
                                            style="font-size:7px;"></i>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fs-13 fw-medium text-truncate" title="{{ $task->plan_details }}">
                                                {{ Str::limit($task->plan_details, 45) }}</div>
                                            <div class="text-muted fs-11">{{ $task->client_name }} ·
                                                {{ $task->profile_name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                <a href="{{ route('daily.task.my') }}"
                                    class="fs-12 text-primary d-block text-end mb-2">View all →</a>
                            </div>
                            <hr class="my-0">
                        @endif

                        {{-- Open Issues --}}
                        @if (($myPendingIssues ?? collect())->isNotEmpty())
                            <div class="px-3 pt-3 pb-1">
                                <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                                    <i class="ri-alert-line text-danger"></i> Open Issues
                                    <span
                                        class="badge bg-danger ms-1">{{ ($myPendingIssues ?? collect())->count() }}</span>
                                </div>
                                @foreach ($myPendingIssues ?? [] as $issue)
                                    @php
                                        $ic = match ($issue->type) {
                                            'Critical' => '#dc3545',
                                            'Urgent' => '#e07b80',
                                            'High' => '#0d6efd',
                                            default => '#198754',
                                        };
                                    @endphp
                                    <div class="d-flex align-items-start gap-2 mb-2 p-2 rounded border-start border-3"
                                        style="border-color:{{ $ic }} !important;background:#fff8f8;">
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fs-13 fw-medium" style="color:{{ $ic }}">
                                                {{ Str::limit($issue->category ?: $issue->issue, 45) }}</div>
                                            <div class="text-muted fs-11">{{ $issue->client_name }} ·
                                                {{ $issue->profile_name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                <a href="{{ route('daily.issue.my') }}"
                                    class="fs-12 text-danger d-block text-end mb-2">View all →</a>
                            </div>
                            <hr class="my-0">
                        @endif

                        {{-- Client Messages --}}
                        @if (($myPendingClientMessages ?? 0) > 0 || ($pendingClientMessageCount ?? 0) > 0)
                            <div class="px-3 pt-3 pb-3">
                                <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                                    <i class="ri-mail-send-line text-warning"></i> Client Messages
                                </div>
                                @if (($myPendingClientMessages ?? 0) > 0)
                                    <div class="p-2 rounded mb-1" style="background:#fff8e1;">
                                        <div class="fs-13">Your <strong>{{ $myPendingClientMessages }}</strong>
                                            message(s) pending approval.</div>
                                        <a href="{{ route('client.message.my.list') }}"
                                            class="fs-12 text-warning fw-medium">View →</a>
                                    </div>
                                @endif
                                @if (($pendingClientMessageCount ?? 0) > 0 && $isLead)
                                    <div class="p-2 rounded" style="background:#fff3cd;">
                                        <div class="fs-13"><strong>{{ $pendingClientMessageCount }}</strong> message(s)
                                            waiting your review.</div>
                                        <a href="{{ route('client.message.review.list') }}"
                                            class="fs-12 text-warning fw-medium">Review Now →</a>
                                    </div>
                                @endif
                            </div>
                        @else
                            @if (
                                ($myReminders ?? collect())->isEmpty() &&
                                    ($myTodayTasks ?? collect())->isEmpty() &&
                                    ($myPendingIssues ?? collect())->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="ri-checkbox-circle-line fs-1 d-block mb-2 text-success"></i>
                                    <div class="fs-14 fw-medium">All clear!</div>
                                    <div class="fs-13">No pending tasks or issues.</div>
                                </div>
                            @else
                                <div class="px-3 py-3">
                                    <div class="p-2 rounded" style="background:#f0fff4;">
                                        <div class="fs-13 text-success"><i class="ri-mail-check-line me-1"></i> No pending
                                            client messages.</div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

                {{-- Todo List --}}
                <div class="card mb-0">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold fs-13">Todo List</span>
                        <a href="{{ route('todo.list') }}" class="text-muted fs-11"><i
                                class="ri-external-link-line"></i></a>
                    </div>
                    <div class="card-body py-2 px-3" style="max-height:220px;overflow-y:auto;" id="dash-todo-scroll">
                        <form id="todo-form" class="mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" id="todo-input-text" class="form-control form-control-sm"
                                    placeholder="Add todo...">
                                <button class="btn btn-sm btn-primary" type="submit">+</button>
                            </div>
                        </form>
                        <ul class="list-unstyled mb-0" id="todo-list"></ul>
                    </div>
                </div>

            </div>{{-- end col-6: Good Morning column --}}

            {{-- ── COL 6: TEAM COLUMN ────────────────────────────── --}}
            <div class="col-lg-6">

                <div class="row match-height align-items-center">
                    <div class="col-lg-6">
                        {{-- Team Name & Logo --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($team->logo)
                                        <img src="{{ asset($team->logo) }}" alt="{{ $team->name }}"
                                            class="team-logo-frame">
                                    @else
                                        <div class="team-logo-placeholder">
                                            <i class="ri-team-line fs-3 text-primary"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">{{ $team->name }}</h4>
                                        <p class="text-muted fs-13 mb-0">{{ $totalMembers }} Active Members</p>
                                    </div>
                                    @if ($isLead)
                                        <button type="button" class="btn btn-sm btn-soft-secondary"
                                            data-bs-toggle="modal" data-bs-target="#teamProfileModal"
                                            title="Edit team name & logo">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        {{-- Leader Name & Avatar --}}
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="text-muted small text-uppercase fw-semibold mb-2">Team Leader</div>
                                @if ($teamLeader)
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($teamLeader->avatar) }}" alt="{{ $teamLeader->name }}"
                                            class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                        <div class="flex-grow-1">
                                            <div class="fw-medium">{{ $teamLeader->name }}</div>
                                            <div class="text-muted fs-12">{{ $teamLeader->designation ?: 'Leader' }}</div>
                                        </div>
                                        @if ($teamLeader->id === Auth::id())
                                            <span class="badge bg-info-subtle text-info rounded-pill">That's you</span>
                                        @else
                                            <a href="{{ route('dashboard.inbox') }}?user={{ $teamLeader->id }}"
                                                class="btn btn-sm btn-soft-primary">
                                                <i class="ri-send-plane-line me-1"></i> Send Message
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-muted fs-13">No leader assigned to this team yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Team Announcement --}}
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <span class="fw-semibold fs-13"><i class="ri-megaphone-line me-1 text-primary"></i> Team
                            Announcement</span>
                    </div>
                    <div class="card-body" style="max-height:220px;overflow-y:auto;">
                        @forelse ($activeAnnouncements ?? [] as $announcement)
                            <div
                                class="alert {{ $announcement->priorityAlertClass() }} d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <span>
                                    <strong>{{ $announcement->title }}</strong> &mdash; {{ $announcement->description }}
                                </span>
                                <span class="text-nowrap small fw-semibold">Ends
                                    {{ $announcement->ends_at->format('d M Y') }}</span>
                            </div>
                        @empty
                            <div class="text-muted text-center py-3 fs-13">
                                <i class="ri-megaphone-line d-block mb-1 fs-3"></i> No active announcements.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Team Sheets --}}
                <div class="card mb-3">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold fs-13"><i class="ri-file-excel-2-line me-1 text-success"></i> Team
                            Sheets</span>
                        @if (($teamSheetTotal ?? 0) > ($teamSheets ?? collect())->count())
                            <a href="{{ route('team.sheet.list') }}" class="fs-12 text-muted">View All →</a>
                        @endif
                    </div>
                    <div class="card-body" style="max-height:200px;overflow-y:auto;">
                        @forelse ($teamSheets ?? [] as $sheet)
                            <a href="{{ $sheet->link }}" target="_blank" rel="noopener" class="sheet-row-link py-1">
                                {{-- <i class="ri-file-excel-2-line text-success"></i> --}}
                                <img src="{{ asset('excel.png') }}" alt="Excel Icon" class="excel-icon"
                                    style="width:16px;height:16px;margin-right:6px;">
                                <span class="fs-13">{{ $sheet->title }}</span>
                            </a>
                        @empty
                            <div class="text-muted text-center py-3 fs-13">No sheets added yet.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Role-specific footer --}}
                @if ($isLead)
                    <div class="card mb-3">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-medium"><i class="ri-megaphone-line me-1 text-primary"></i> Make A
                                    Announcement</div>
                                <div class="text-muted fs-12">Notify your team about something important.</div>
                            </div>
                            <a href="{{ route('announcement.create') }}" class="btn btn-sm btn-soft-primary">Create</a>
                        </div>
                    </div>
                    <div class="card mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-medium"><i class="ri-calendar-close-line me-1 text-danger"></i> Leave Log
                                </div>
                                <div class="text-muted fs-12">See who's absent, on leave, or WFH.</div>
                            </div>
                            <a href="{{ route('member.leave.list') }}" class="btn btn-sm btn-soft-secondary">Open</a>
                        </div>
                    </div>
                @else
                    <div class="card mb-0">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-medium"><i class="ri-calendar-close-line me-1 text-danger"></i> Ask For
                                    Leave</div>
                                <div class="text-muted fs-12">Log an absence, leave, or home office day.</div>
                            </div>
                            <a href="{{ route('member.leave.ask') }}" class="btn btn-sm btn-soft-primary">Ask</a>
                        </div>
                    </div>
                @endif

            </div>{{-- end col-6: Team column --}}

        </div>{{-- end main row --}}

        @if ($isLead)
            <div class="modal fade" id="teamProfileModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('leader.team.profile.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="ri-team-line me-1"></i> Edit Team Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Team Name</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ $team->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Team Logo</label>
                                    <input type="file" class="form-control" name="logo" accept="image/*">
                                    <div class="form-text">PNG/JPG, up to 2MB. Leave empty to keep the current logo.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success"><i class="ri-save-line me-1"></i>
                                    Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- ── ADMIN / NON-TEAM DASHBOARD ─────────────────────────── --}}
        <div class="row">
            <div class="col-lg-5">
                <div class="row">
                    @if (Auth::user()->email_verified_at == null)
                        <div class="col-12 mb-3">
                            <div class="alert alert-warning">
                                <strong>{{ Auth::user()->email }}</strong> is not verified.
                                <a href="{{ route('email.verify') }}">Verify now</a>
                            </div>
                        </div>
                    @endif
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-body row">
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($greetings == 'Good Morning!')
                                            <img width="50"
                                                src="{{ asset('admin/assets/images/greetings/004-sunrise.png') }}"
                                                alt="">
                                        @elseif ($greetings == 'Good Afternoon!')
                                            <img width="50"
                                                src="{{ asset('admin/assets/images/greetings/002-sunsets.png') }}"
                                                alt="">
                                        @else
                                            <img width="50"
                                                src="{{ asset('admin/assets/images/greetings/003-cloudy-night.png') }}"
                                                alt="">
                                        @endif
                                        <div>
                                            <h5 class="text-primary mb-0">{{ $greetings }}</h5>
                                            <p class="fs-13 mb-0">{{ Auth::user()->name }} —
                                                {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <img class="w-100" src="{{ asset('admin/assets/images/admin.png') }}"
                                        alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title mb-0"><i class="ri-alarm-line me-1 text-warning"></i> Daily
                                    Reminders</h4>
                                <a href="{{ route('daily.reminder.create') }}" class="fs-13">Create Daily Reminder</a>
                            </div>
                            <div class="card-body py-2" data-simplebar style="max-height:200px;">
                                @forelse ($myReminders ?? [] as $reminder)
                                    <div class="py-1 border-bottom fs-13">{{ $reminder->daysLeftLabel() }}</div>
                                @empty
                                    <div class="text-muted text-center py-2 fs-13">No reminders. You're all caught up!
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h4 class="header-title">Todo List</h4>
                            </div>
                            <div class="todoapp">
                                <div class="card-body pt-2">
                                    <form name="todo-form" id="todo-form">
                                        <div class="row">
                                            <div class="col"><input type="text" id="todo-input-text"
                                                    class="form-control" placeholder="Add new todo"></div>
                                            <div class="col-auto"><button class="btn btn-primary btn-md"
                                                    type="submit">Add</button></div>
                                        </div>
                                    </form>
                                    <ul class="list-group list-group-flush todo-list mt-2" id="todo-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="card widget-icon-box text-bg-purple h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="text-uppercase fs-13 mt-0">Total Teams</h5>
                                        <h3 class="my-3">{{ $totalTeams }}</h3>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0"><span
                                            class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3"><i
                                                class="ri-briefcase-4-line"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card widget-icon-box text-bg-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="text-uppercase fs-13 mt-0">Total Members</h5>
                                        <h3 class="my-3">{{ $totalOrgMembers }}</h3>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0"><span
                                            class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3"><i
                                                class="ri-group-line"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let userID = "{{ Auth::id() }}";
            let storageKey = 'tasks_' + userID;
            let fixedTasks = [];

            function getLocalTasks() {
                return JSON.parse(localStorage.getItem(storageKey)) || [];
            }

            function setLocalTasks(tasks) {
                localStorage.setItem(storageKey, JSON.stringify(tasks));
            }

            function buildItem(text, completed, source, key, fixed) {
                return '<li class="list-group-item border-0 ps-0 py-1 ' + (completed ? 'completed' : '') +
                    '" data-source="' + source + '" data-key="' + key + '">' +
                    '<div class="form-check mb-0">' +
                    '<input type="checkbox" class="form-check-input todo-done" ' + (completed ? 'checked' : '') +
                    '>' +
                    (fixed ?
                        '<i class="ri-pushpin-2-fill text-primary fs-11 ms-1" title="Fixed - synced across your devices"></i>' :
                        '') +
                    '<label class="form-check-label fs-13">' + (completed ? '<s>' + text + '</s>' : text) +
                    '</label>' +
                    '<span style="cursor:pointer;" class="delete-btn text-danger float-end"><i class="ri-eraser-line"></i></span>' +
                    '</div></li>';
            }

            function loadTasks() {
                let $list = $('#todo-list');
                $list.empty();
                fixedTasks.forEach(function(task) {
                    $list.append(buildItem(task.text, task.completed, 'server', task.id, true));
                });
                getLocalTasks().forEach(function(task, index) {
                    $list.append(buildItem(task.text, task.completed, 'local', index, false));
                });
            }

            function fetchFixedTasks(callback) {
                $.get("{{ route('todo.items') }}", function(res) {
                    fixedTasks = (res && res.data) ? res.data : [];
                }).fail(function() {
                    fixedTasks = [];
                }).always(function() {
                    if (callback) callback();
                });
            }

            $('#todo-form').submit(function(e) {
                e.preventDefault();
                let taskText = $('#todo-input-text').val().trim();
                if (!taskText) return;
                let tasks = getLocalTasks();
                tasks.push({
                    text: taskText,
                    completed: false
                });
                setLocalTasks(tasks);
                $('#todo-input-text').val('');
                loadTasks();
            });

            $(document).on('click', '.todo-done', function() {
                let $li = $(this).closest('li');
                let source = $li.data('source');
                let key = $li.data('key');

                if (source === 'server') {
                    $.post("{{ route('todo.toggle') }}", {
                        id: key,
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        let task = fixedTasks.find(function(t) {
                            return String(t.id) === String(key);
                        });
                        if (task) task.completed = res.data.completed;
                        loadTasks();
                    }).fail(function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Could not update task'
                        });
                    });
                } else {
                    let tasks = getLocalTasks();
                    tasks[key].completed = !tasks[key].completed;
                    setLocalTasks(tasks);
                    loadTasks();
                }
            });

            $(document).on('click', '.delete-btn', function() {
                let $li = $(this).closest('li');
                let source = $li.data('source');
                let key = $li.data('key');

                if (source === 'server') {
                    $.post("{{ route('todo.destroy') }}", {
                        id: key,
                        _token: '{{ csrf_token() }}'
                    }, function() {
                        fixedTasks = fixedTasks.filter(function(t) {
                            return String(t.id) !== String(key);
                        });
                        loadTasks();
                    }).fail(function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Could not delete task'
                        });
                    });
                } else {
                    let tasks = getLocalTasks();
                    tasks.splice(key, 1);
                    setLocalTasks(tasks);
                    loadTasks();
                }
            });

            fetchFixedTasks(loadTasks);
        });
    </script>
@endpush
