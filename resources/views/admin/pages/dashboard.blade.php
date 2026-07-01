@extends('admin.master')

@section('title', 'Dashboard')
@section('quickAccessicon', 'ri-dashboard-2-fill')

@push('style')
<style>
    .stat-card-link { text-decoration: none; display: block; }
    .stat-card-link .card {
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .stat-card-link:hover .card {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18) !important;
    }
</style>
@endpush

@section('content')

{{-- Alerts --}}
@if (!($profileComplete ?? true))
<div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <span><i class="ri-error-warning-line me-1"></i> Your profile is incomplete. Please add your phone, address, designation, and date of birth.</span>
    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-warning">Complete Profile</a>
</div>
@endif

@if (isset($team))

{{-- ── TEAM MEMBER DASHBOARD ──────────────────────────────── --}}
<div class="row g-3">

    {{-- ── COL 8 ─────────────────────────────────────────── --}}
    <div class="col-lg-9">

        {{-- Top row: Greeting + Todo + Quick Access (fixed height, scroll) --}}
        <div class="row g-3 mb-3">

            {{-- Greeting --}}
            <div class="col-6">
                <div class="card mb-0 h-100">
                    <div class="card-body row align-items-center">
                        <div class="col-lg-6">
                            @if (Auth::user()->email_verified_at == null)
                                <div class="alert alert-warning py-1 px-2 fs-12 mb-2">
                                    <strong>Email not verified.</strong>
                                    <a href="{{ route('email.verify') }}" class="text-warning-emphasis fw-medium">Verify now</a>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if ($greetings == 'Good Morning!')
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/004-sunrise.png') }}" alt="">
                                @elseif ($greetings == 'Good Afternoon!')
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/002-sunsets.png') }}" alt="">
                                @else
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/003-cloudy-night.png') }}" alt="">
                                @endif
                                <div>
                                    <h5 class="text-primary mb-0">{{ $greetings }}</h5>
                                    <p class="fs-13 mb-0">{{ Auth::user()->name }} — {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
                                </div>
                            </div>
                            @if (!empty($eventMessages))
                                @foreach ($eventMessages as $event)
                                    <div class="alert alert-info py-1 px-2 fs-12 mb-1">🎉 {{ $event->message }}</div>
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
                        <div class="col-lg-6">
                            <img class="w-100" src="{{ asset('admin/assets/images/admin.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Todo List --}}
            <div class="col-3">
                <div class="card mb-0 d-flex flex-column" style="height:200px;">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold fs-13">Todo List</span>
                        <a href="{{ route('todo.list') }}" class="text-muted fs-11"><i class="ri-external-link-line"></i></a>
                    </div>
                    <div class="card-body py-2 px-3 flex-grow-1" style="overflow-y:auto;" id="dash-todo-scroll">
                        <form id="todo-form" class="mb-2">
                            <div class="input-group input-group-sm">
                                <input type="text" id="todo-input-text" class="form-control form-control-sm" placeholder="Add todo...">
                                <button class="btn btn-sm btn-primary" type="submit">+</button>
                            </div>
                        </form>
                        <ul class="list-unstyled mb-0" id="todo-list"></ul>
                    </div>
                </div>
            </div>

            {{-- Quick Access --}}
            <div class="col-3">
                <div class="card mb-0 d-flex flex-column" style="height:200px;">
                    <div class="card-header py-2">
                        <span class="fw-semibold fs-13">Quick Access</span>
                    </div>
                    <div class="card-body py-2 px-3 flex-grow-1" style="overflow-y:auto;">
                        @forelse ($menuItems as $item)
                            <div class="d-flex align-items-center gap-2 py-1 border-bottom">
                                <i class="{{ $item->icon }} fs-14 text-muted flex-shrink-0"></i>
                                <a href="{{ $item->url }}" class="text-body fs-13 flex-grow-1 text-truncate">{{ $item->name }}</a>
                                <a href="{{ route('remove.quick.access', $item->route) }}" class="text-danger fs-11">✕</a>
                            </div>
                        @empty
                            <div class="text-muted text-center py-3 fs-13">
                                <i class="ri-search-2-line d-block mb-1"></i> No links added
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Stat Cards row --}}
        <div class="row g-3">
            {{-- Team Info --}}
            <div class="col-3">
                <div class="card widget-icon-box text-bg-purple mb-0 h-100" style="transition:transform 0.18s ease,box-shadow 0.18s ease;cursor:default;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">Team</h5>
                                <h3 class="my-2 text-white">{{ $team->name }}</h3>
                                <p class="mb-0 text-white text-opacity-75 fs-13">{{ $totalMembers }} Active Members</p>
                            </div>
                            <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3">
                                    <i class="ri-team-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tasks --}}
            <div class="col-3">
                <a href="{{ route('daily.task.my') }}" class="stat-card-link">
                    <div class="card widget-icon-box text-bg-success mb-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">Tasks Left</h5>
                                    <h3 class="my-2 text-white">{{ ($myTodayTasks ?? collect())->count() }}</h3>
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

            {{-- Issues --}}
            <div class="col-3">
                <a href="{{ route('daily.issue.my') }}" class="stat-card-link">
                    <div class="card widget-icon-box mb-0 h-100 {{ ($myPendingIssues ?? collect())->isNotEmpty() ? 'text-bg-danger' : 'text-bg-info' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-uppercase fs-13 mt-0 text-white text-opacity-75">Issues</h5>
                                    <h3 class="my-2 text-white">{{ ($myPendingIssues ?? collect())->count() }}</h3>
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

            {{-- Client Messages --}}
            <div class="col-3">
                @php
                    $msgCount = ($myPendingClientMessages ?? 0) > 0
                        ? ($myPendingClientMessages ?? 0)
                        : ($pendingClientMessageCount ?? 0);
                    $msgRoute = ($myPendingClientMessages ?? 0) > 0
                        ? route('client.message.my.list')
                        : route('client.message.review.list');
                @endphp
                <a href="{{ $msgRoute }}" class="stat-card-link">
                    <div class="card widget-icon-box mb-0 h-100 {{ $msgCount > 0 ? 'text-bg-warning' : 'text-bg-primary' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="text-uppercase fs-13 mt-0 {{ $msgCount > 0 ? 'text-dark' : 'text-white text-opacity-75' }}">Messages</h5>
                                    <h3 class="my-2 {{ $msgCount > 0 ? 'text-dark' : 'text-white' }}">{{ $msgCount }}</h3>
                                    <p class="mb-0 fs-13 {{ $msgCount > 0 ? 'text-dark opacity-75' : 'text-white text-opacity-75' }}">{{ $msgCount > 0 ? 'Pending review' : 'All clear' }}</p>
                                </div>
                                <div class="widget-icon-box-avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-white bg-opacity-25 {{ $msgCount > 0 ? 'text-dark' : 'text-white' }} rounded-3 fs-3">
                                        <i class="ri-mail-send-line"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>{{-- end col-8 --}}

    {{-- ── COL 4: Today's Reminders ─────────────────────── --}}
    <div class="col-lg-3">
        <div class="card h-100 mb-0">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold"><i class="ri-alarm-line me-1 text-primary"></i> Today's Reminders</span>
                <span class="text-muted fs-12">{{ now()->format('d M') }}</span>
            </div>
            <div class="card-body p-0" style="overflow-y:auto;max-height:460px;">

                {{-- Pending Tasks --}}
                @if (($myTodayTasks ?? collect())->isNotEmpty())
                    <div class="px-3 pt-3 pb-1">
                        <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                            <i class="ri-task-line text-primary"></i> Pending Tasks
                            <span class="badge bg-primary ms-1">{{ ($myTodayTasks ?? collect())->count() }}</span>
                        </div>
                        @foreach ($myTodayTasks ?? [] as $task)
                            <div class="d-flex align-items-start gap-2 mb-2 p-2 rounded" style="background:#f0f5ff;">
                                <i class="ri-circle-fill text-primary mt-1 flex-shrink-0" style="font-size:7px;"></i>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fs-13 fw-medium text-truncate" title="{{ $task->plan_details }}">{{ Str::limit($task->plan_details, 45) }}</div>
                                    <div class="text-muted fs-11">{{ $task->client_name }} · {{ $task->profile_name }}</div>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('daily.task.my') }}" class="fs-12 text-primary d-block text-end mb-2">View all →</a>
                    </div>
                    <hr class="my-0">
                @endif

                {{-- Open Issues --}}
                @if (($myPendingIssues ?? collect())->isNotEmpty())
                    <div class="px-3 pt-3 pb-1">
                        <div class="text-muted fs-11 text-uppercase fw-bold mb-2 d-flex align-items-center gap-1">
                            <i class="ri-alert-line text-danger"></i> Open Issues
                            <span class="badge bg-danger ms-1">{{ ($myPendingIssues ?? collect())->count() }}</span>
                        </div>
                        @foreach ($myPendingIssues ?? [] as $issue)
                            @php
                                $ic = match($issue->type){ 'Critical'=>'#dc3545','Urgent'=>'#e07b80','High'=>'#0d6efd',default=>'#198754' };
                            @endphp
                            <div class="d-flex align-items-start gap-2 mb-2 p-2 rounded border-start border-3" style="border-color:{{ $ic }} !important;background:#fff8f8;">
                                <div class="flex-grow-1 min-width-0">
                                    <div class="fs-13 fw-medium" style="color:{{ $ic }}">{{ Str::limit($issue->issue, 45) }}</div>
                                    <div class="text-muted fs-11">{{ $issue->client_name }} · {{ $issue->profile_name }}</div>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('daily.issue.my') }}" class="fs-12 text-danger d-block text-end mb-2">View all →</a>
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
                                <div class="fs-13">Your <strong>{{ $myPendingClientMessages }}</strong> message(s) pending approval.</div>
                                <a href="{{ route('client.message.my.list') }}" class="fs-12 text-warning fw-medium">View →</a>
                            </div>
                        @endif
                        @if (($pendingClientMessageCount ?? 0) > 0 && Auth::user()->hasAnyRole(['Leader','Co Leader']))
                            <div class="p-2 rounded" style="background:#fff3cd;">
                                <div class="fs-13"><strong>{{ $pendingClientMessageCount }}</strong> message(s) waiting your review.</div>
                                <a href="{{ route('client.message.review.list') }}" class="fs-12 text-warning fw-medium">Review Now →</a>
                            </div>
                        @endif
                    </div>
                @else
                    @if (($myTodayTasks ?? collect())->isEmpty() && ($myPendingIssues ?? collect())->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="ri-checkbox-circle-line fs-1 d-block mb-2 text-success"></i>
                            <div class="fs-14 fw-medium">All clear!</div>
                            <div class="fs-13">No pending tasks or issues.</div>
                        </div>
                    @else
                        <div class="px-3 py-3">
                            <div class="p-2 rounded" style="background:#f0fff4;">
                                <div class="fs-13 text-success"><i class="ri-mail-check-line me-1"></i> No pending client messages.</div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>

</div>{{-- end main row --}}

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
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/004-sunrise.png') }}" alt="">
                                @elseif ($greetings == 'Good Afternoon!')
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/002-sunsets.png') }}" alt="">
                                @else
                                    <img width="50" src="{{ asset('admin/assets/images/greetings/003-cloudy-night.png') }}" alt="">
                                @endif
                                <div>
                                    <h5 class="text-primary mb-0">{{ $greetings }}</h5>
                                    <p class="fs-13 mb-0">{{ Auth::user()->name }} — {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img class="w-100" src="{{ asset('admin/assets/images/admin.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="card h-100">
                    <div class="card-header"><h4 class="header-title">Quick Access</h4></div>
                    <div class="card-body py-0 mb-3" data-simplebar style="max-height:250px;">
                        @forelse ($menuItems as $item)
                            <div class="row py-1 align-items-center">
                                <div class="col-auto"><i class="{{ $item->icon }} fs-18"></i></div>
                                <div class="col ps-0"><a href="{{ $item->url }}" class="text-body">{{ $item->name }}</a></div>
                                <div class="col-auto"><a href="{{ route('remove.quick.access', $item->route) }}" class="text-danger fw-bold">Remove</a></div>
                            </div>
                        @empty
                            <div class="row py-1"><div class="col ps-0"><a href="#" class="text-body">No Links Found</a></div></div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="card h-100">
                    <div class="card-header"><h4 class="header-title">Todo List</h4></div>
                    <div class="todoapp">
                        <div class="card-body pt-2">
                            <form name="todo-form" id="todo-form">
                                <div class="row">
                                    <div class="col"><input type="text" id="todo-input-text" class="form-control" placeholder="Add new todo"></div>
                                    <div class="col-auto"><button class="btn btn-primary btn-md" type="submit">Add</button></div>
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
                            <div><h5 class="text-uppercase fs-13 mt-0">Total Teams</h5><h3 class="my-3">{{ $totalTeams }}</h3></div>
                            <div class="avatar-sm flex-shrink-0"><span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3"><i class="ri-briefcase-4-line"></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 mb-3">
                <div class="card widget-icon-box text-bg-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div><h5 class="text-uppercase fs-13 mt-0">Total Members</h5><h3 class="my-3">{{ $totalOrgMembers }}</h3></div>
                            <div class="avatar-sm flex-shrink-0"><span class="avatar-title bg-white bg-opacity-25 text-white rounded-3 fs-3"><i class="ri-group-line"></i></span></div>
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

    function loadTasks() {
        let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        let $list = $('#todo-list');
        $list.empty();
        tasks.forEach(function(task, index) {
            let taskHTML = '<li class="list-group-item border-0 ps-0 py-1 ' + (task.completed ? 'completed' : '') + '" data-index="' + index + '">'
                + '<div class="form-check mb-0">'
                + '<input type="checkbox" class="form-check-input todo-done" ' + (task.completed ? 'checked' : '') + '>'
                + '<label class="form-check-label fs-13">' + (task.completed ? '<s>' + task.text + '</s>' : task.text) + '</label>'
                + '<span style="cursor:pointer;" class="delete-btn text-danger float-end"><i class="ri-eraser-line"></i></span>'
                + '</div></li>';
            $list.append(taskHTML);
        });
    }

    $('#todo-form').submit(function(e) {
        e.preventDefault();
        let taskText = $('#todo-input-text').val().trim();
        if (taskText) {
            let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
            tasks.push({ text: taskText, completed: false });
            localStorage.setItem(storageKey, JSON.stringify(tasks));
            $('#todo-input-text').val('');
            loadTasks();
        }
    });

    $(document).on('click', '.todo-done', function() {
        let taskIndex = $(this).closest('li').data('index');
        let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        tasks[taskIndex].completed = !tasks[taskIndex].completed;
        localStorage.setItem(storageKey, JSON.stringify(tasks));
        loadTasks();
    });

    $(document).on('click', '.delete-btn', function() {
        let taskIndex = $(this).closest('li').data('index');
        let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
        tasks.splice(taskIndex, 1);
        localStorage.setItem(storageKey, JSON.stringify(tasks));
        loadTasks();
    });

    loadTasks();
});
</script>
@endpush
