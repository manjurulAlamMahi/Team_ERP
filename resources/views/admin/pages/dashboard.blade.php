@extends('admin.master')

@section('title', 'Dashboard')
@section('quickAccessicon', 'ri-dashboard-2-fill')

@section('content')

    @if (!($profileComplete ?? true))
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><i class="ri-error-warning-line"></i> Your profile is incomplete. Please add your phone,
                        address, designation, and date of birth.</span>
                    <a href="{{ route('profile.index') }}" class="btn btn-sm btn-warning">Complete Profile</a>
                </div>
            </div>
        </div>
    @endif

    @if (($myPendingIssueCount ?? 0) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm" role="alert">
                    <div>
                        <i class="ri-alarm-warning-fill me-2 fs-18"></i>
                        <strong>Hello, {{ Auth::user()->name }}!</strong>
                        You have <strong>{{ $myPendingIssueCount }}</strong> open
                        {{ Str::plural('issue', $myPendingIssueCount) }} assigned to you that need to be resolved <strong>ASAP</strong>.
                    </div>
                    <a href="{{ route('daily.issue.list') }}" class="btn btn-sm btn-danger">
                        <i class="ri-arrow-right-line"></i> View Issues
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Welcome -->
        <div class="col-lg-5">
            <div class="row">
                @if (Auth::user()->email_verified_at == null)
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>{{ Auth::user()->email }}</strong>Is Not Verified ! <a
                                href="{{ route('email.verify') }}">Click Here</a> To Verify Now
                        </div>
                    </div>
                @endif

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body row">
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center" style="gap: 0 10px">
                                    <div class="greet_img">
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
                                    </div>
                                    <div class="greet">
                                        <h4 class="mt-1 mb-1">
                                            <span class="text-primary">{{ $greetings }}</span>
                                        </h4>
                                        <p class="fs-13 m-0" style="text-transform:capitalize">
                                            {{ Auth::user()->name }} ||
                                            {{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}</p>
                                    </div>
                                </div>



                                {{-- <ul class="mb-0 list-inline">
                                    <li class="list-inline-item me-3">
                                        <h5 class="mb-1">$ 2658.69</h5>
                                        <p class="mb-0 fs-13">Total Test</p>
                                    </li>
                                    <li class="list-inline-item">
                                        <h5 class="mb-1">150</h5>
                                        <p class="mb-0 fs-13">Number of Test</p>
                                    </li>
                                </ul> --}}

                                @if (!empty($eventMessages))
                                    @foreach ($eventMessages as $event)
                                        <p class="fs-13 pt-3 m-0" style="text-transform:capitalize">
                                            Today is
                                            <b>{{ $event->name == 'Birthday' ? 'Your Birthday' : $event->name }}</b>
                                        </p>
                                        <h4 class="mb-1">{{ $event->message }}</h4>
                                    @endforeach
                                @endif
                                @if ($upcomingEvents->isNotEmpty())
                                    <p class="fs-13 pt-3 m-0" style="text-transform:capitalize">
                                        Upcoming Events:
                                    </p>
                                    <h4 class="mb-1">
                                        @foreach ($upcomingEvents as $event)
                                            @if ($event->name == 'Birthday')
                                                <b>{{ $event->message }}</b>
                                            @else
                                                <b>{{ $event->name }}</b> on
                                                {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }} <br>
                                            @endif
                                        @endforeach
                                    </h4>
                                @else
                                    @if (empty($eventMessages))
                                        <p class="fs-13 pt-3 m-0" style="text-transform:capitalize"> Upcoming Event</p>
                                        <h4 class="mb-1">There No Upcoming Event</h4>
                                    @endif
                                @endif

                            </div>

                            <div class="col-lg-6">
                                <img class="w-100 image-fluid" src="{{ asset('admin/assets/images/admin.png') }}"
                                    alt="">
                            </div>
                            <!-- end div-->
                        </div>
                        <!-- end card-body-->
                    </div>
                </div>

                <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">Quick Access</h4>
                            </div>

                            <div class="card-body py-0 mb-3" data-simplebar style="max-height: 315px;">

                                @forelse ($menuItems as $item)
                                    <div class="row py-1 align-items-center">
                                        <div class="col-auto">
                                            <i class="{{ $item->icon }} fs-18"></i>
                                        </div>
                                        <div class="col ps-0">
                                            <a href="{{ $item->url }}" class="text-body">{{ $item->name }}</a>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('remove.quick.access', $item->route) }}"
                                                class="text-danger fw-bold pe-2">Remove</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="row py-1 align-items-center">
                                        <div class="col-auto">
                                            <i class="ri-search-2-line  fs-18"></i>
                                        </div>
                                        <div class="col ps-0">
                                            <a href="javascript:void(0);" class="text-body">No Links Found</a>
                                        </div>
                                    </div>
                                @endforelse

                            </div> <!-- end slimscroll -->
                        </div>
                        <!-- end card-->
                    </div>

                <div class="col-lg-6">
                        <!-- Todo-->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">Todo List</h4>
                            </div>

                            <div class="todoapp">

                                <div class="card-body pt-2">
                                    <form name="todo-form" id="todo-form" class="needs-validation">
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" id="todo-input-text" name="todo-input-text"
                                                    class="form-control" placeholder="Add new todo">
                                            </div>
                                            <div class="col-auto d-grid">
                                                <button class="btn-primary btn-md btn waves-effect waves-light" type="submit"
                                                    id="todo-btn-submit">Add</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="simplebar-content">
                                        <ul class="list-group list-group-flush todo-list" id="todo-list">

                                        </ul>
                                    </div>
                                </div>
                            </div> <!-- end .todoapp-->
                        </div> <!-- end card-->
                    </div>

                @php
                    $messages = App\Models\Chat::where('receiver_id', auth()->user()->id)
                        ->where('read', false)
                        ->get();
                @endphp
                @if ($messages->count() > 0)
                        <div class="col-lg-12">
                            <!-- Messages-->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="header-title">Messages</h4>
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body pt-0">
                                    <div class="inbox-widget">
                                        @foreach ($messages as $msg)
                                            <div class="inbox-item">
                                                <div class="inbox-item-img"><img src="{{ asset($msg->sender->avatar) }}"
                                                        class="rounded-circle" alt=""></div>
                                                <p class="inbox-item-author">{{ $msg->sender->name }}</p>
                                                <p class="inbox-item-text">{{ Str::limit($msg->message, 50) }}</p>
                                                <p class="inbox-item-date">
                                                    <a href="{{ route('dashboard.inbox') }}"
                                                        class="btn btn-sm btn-link text-info fs-13"> Reply </a>
                                                </p>
                                            </div>
                                        @endforeach
                                    </div> <!-- end inbox-widget -->
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div>
                    @endif

            </div>
        </div>
        <!-- Welcome Ends -->
        <div class="col-lg-7">
                <div class="row">
                    @if (isset($team))
                        <div class="col-lg-3">
                            <div class="card widget-icon-box text-bg-purple h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-uppercase fs-13 mt-0">Total Members</h5>
                                            <h3 class="my-3">{{ $totalMembers }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-team-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        <div class="col-lg-3">
                            <div class="card widget-icon-box text-bg-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-uppercase fs-13 mt-0">Today's Plans Submitted</h5>
                                            <h3 class="my-3">{{ $teamOverview->whereIn('plan_status', ['approved', 'pending', 'rejected'])->count() }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-calendar-todo-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        <div class="col-lg-3">
                            <div class="card widget-icon-box text-bg-pink h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-uppercase fs-13 mt-0">Pending Plan Reviews</h5>
                                            <h3 class="my-3">{{ $pendingPlanCount }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-hourglass-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        <div class="col-lg-3">
                            <div class="card widget-icon-box h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-muted text-uppercase fs-13 mt-0">Open Issues</h5>
                                            <h3 class="my-3">{{ $openIssueCount }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title text-bg-danger rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-alert-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        @if (($pendingClientMessageCount ?? 0) > 0)
                        <div class="col-lg-12 mt-2">
                            <a href="{{ route('client.message.review.list') }}" class="text-decoration-none">
                                <div class="alert alert-warning d-flex align-items-center justify-content-between mb-0" role="alert">
                                    <div>
                                        <i class="ri-mail-send-line me-2 fs-18"></i>
                                        <strong>Quick Review Needed:</strong>
                                        You have <strong>{{ $pendingClientMessageCount }}</strong> client
                                        {{ Str::plural('message', $pendingClientMessageCount) }} waiting for your review.
                                    </div>
                                    <span class="badge bg-warning text-dark fs-13 ms-3">Review Now &rarr;</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="col-lg-6">
                            <div class="card widget-icon-box text-bg-purple h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-uppercase fs-13 mt-0">Total Teams</h5>
                                            <h3 class="my-3">{{ $totalTeams }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-briefcase-4-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->

                        <div class="col-lg-6">
                            <div class="card widget-icon-box text-bg-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h5 class="text-uppercase fs-13 mt-0">Total Members</h5>
                                            <h3 class="my-3">{{ $totalOrgMembers }}</h3>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                                <i class="ri-group-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->
                    @endif
                </div>
            </div>

        {{-- Kept for future design reference, not currently used:
        @can('dashboard_anylisis')
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card widget-icon-box text-bg-purple">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-uppercase fs-13 mt-0" title="Number of Customers">Customers</h5>
                                        <h3 class="my-3">54,214</h3>
                                        <p class="mb-0 text-white text-opacity-75 text-truncate">
                                            <span class="badge bg-white bg-opacity-10 me-1"><i class="ri-arrow-up-line"></i>
                                                2,541</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span
                                            class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                            <i class="ri-group-line"></i>
                                        </span>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-4">
                        <div class="card widget-icon-box text-bg-pink">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-uppercase fs-13 mt-0" title="Number of Orders">Orders</h5>
                                        <h3 class="my-3">7,543</h3>
                                        <p class="mb-0 text-white text-opacity-75 text-truncate">
                                            <span class="badge bg-white bg-opacity-25 me-1"><i class="ri-arrow-down-line"></i>
                                                1.08%</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span
                                            class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                            <i class="ri-shopping-basket-2-line"></i>
                                        </span>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-4">
                        <div class="card widget-icon-box text-bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-uppercase fs-13 mt-0" title="Average Revenue">Revenue</h5>
                                        <h3 class="my-3">$9,254</h3>
                                        <p class="mb-0 text-white text-opacity-75 text-truncate">
                                            <span class="badge bg-white bg-opacity-25 me-1"><i class="ri-arrow-down-line"></i>
                                                7.00%</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span
                                            class="avatar-title bg-white bg-opacity-25 text-white rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                            <i class="ri-money-dollar-circle-line"></i>
                                        </span>
                                    </div>
                                </div>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-4">
                        <div class="card widget-icon-box">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-muted text-uppercase fs-13 mt-0" title="Growth">Growth</h5>
                                        <h3 class="my-3">+ 20.6%</h3>
                                        <p class="mb-0 text-muted text-truncate">
                                            <span class="badge bg-success me-1"><i class="ri-arrow-up-line"></i> 4.87%</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span
                                            class="avatar-title text-bg-primary rounded rounded-3 fs-3 widget-icon-box-avatar shadow">
                                            <i class="ri-donut-chart-line"></i>
                                        </span>
                                    </div>
                                </div>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-4">
                        <div class="card widget-icon-box">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-muted text-uppercase fs-13 mt-0" title="Conversation Ration">
                                            Conversation</h5>
                                        <h3 class="my-3">9.62%</h3>
                                        <p class="mb-0 text-muted text-truncate">
                                            <span class="badge bg-success me-1"><i class="ri-arrow-up-line"></i> 3.07%</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span
                                            class="avatar-title text-bg-warning rounded rounded-3 fs-3 widget-icon-box-avatar">
                                            <i class="ri-pulse-line"></i>
                                        </span>
                                    </div>
                                </div>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-lg-4">
                        <div class="card widget-icon-box">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="text-muted text-uppercase fs-13 mt-0" title="Conversation Ration">Balance
                                        </h5>
                                        <h3 class="my-3">$168.5k</h3>
                                        <p class="mb-0 text-muted text-truncate">
                                            <span class="badge bg-success me-1"><i class="ri-arrow-up-line"></i> 18.34%</span>
                                            <span>Since last month</span>
                                        </p>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title text-bg-dark rounded rounded-3 fs-3 widget-icon-box-avatar">
                                            <i class="ri-wallet-3-line"></i>
                                        </span>
                                    </div>
                                </div>

                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
        @endcan
        --}}

    </div>

    {{-- ── Personal Dashboard Widgets (team members only) ─────────────────── --}}
    @if (isset($team))
    <div class="row g-3 mt-1">

        {{-- Today's Tasks --}}
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                    <span class="fw-semibold fs-14"><i class="ri-task-line me-1"></i> Your Tasks Today</span>
                    <span class="badge bg-white text-primary">{{ ($myTodayTasks ?? collect())->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height:260px;overflow-y:auto;">
                    @forelse ($myTodayTasks ?? [] as $task)
                        <div class="px-3 py-2 border-bottom d-flex align-items-start gap-2">
                            <i class="ri-checkbox-blank-circle-line text-primary mt-1 flex-shrink-0 fs-13"></i>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-medium fs-13 text-truncate" title="{{ $task->plan_details }}">
                                    {{ Str::limit($task->plan_details, 55) }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $task->client_name }} · {{ $task->profile_name }}
                                    @if ($task->source !== 'self')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">{{ $task->task_by_label }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="ri-checkbox-circle-line fs-2 d-block mb-1 text-success"></i>
                            <span class="fs-13">No pending tasks today</span>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-transparent py-2 text-end">
                    <a href="{{ route('daily.task.my') }}" class="text-primary fs-12 fw-medium">View All Tasks →</a>
                </div>
            </div>
        </div>

        {{-- Open Issues Assigned to Me --}}
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header {{ ($myPendingIssues ?? collect())->isNotEmpty() ? 'bg-danger' : 'bg-success' }} text-white d-flex justify-content-between align-items-center py-2">
                    <span class="fw-semibold fs-14"><i class="ri-alert-line me-1"></i> My Open Issues</span>
                    <span class="badge bg-white {{ ($myPendingIssues ?? collect())->isNotEmpty() ? 'text-danger' : 'text-success' }}">
                        {{ ($myPendingIssues ?? collect())->count() }}
                    </span>
                </div>
                <div class="card-body p-0" style="max-height:260px;overflow-y:auto;">
                    @forelse ($myPendingIssues ?? [] as $issue)
                        @php
                            $issueColor = match($issue->type) {
                                'Critical' => '#dc3545',
                                'Urgent'   => '#e07b80',
                                'High'     => '#0d6efd',
                                default    => '#198754',
                            };
                            $issueBadge = match($issue->type) {
                                'Critical' => 'danger',
                                'High'     => 'primary',
                                'Normal'   => 'success',
                                default    => 'danger',
                            };
                        @endphp
                        <div class="px-3 py-2 border-bottom d-flex align-items-start gap-2"
                            style="border-left: 3px solid {{ $issueColor }} !important;">
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-medium fs-13" style="color:{{ $issueColor }}">
                                    {{ Str::limit($issue->issue, 55) }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">
                                    {{ $issue->client_name }} · {{ $issue->profile_name }}
                                    <span class="badge bg-{{ $issueBadge }} ms-1" style="{{ $issue->type === 'Urgent' ? 'opacity:.75' : '' }}">{{ $issue->type }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="ri-shield-check-line fs-2 d-block mb-1 text-success"></i>
                            <span class="fs-13">No open issues assigned to you</span>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-transparent py-2 text-end">
                    <a href="{{ route('daily.issue.list') }}" class="text-danger fs-12 fw-medium">View All Issues →</a>
                </div>
            </div>
        </div>

        {{-- Client Message Status --}}
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center py-2">
                    <span class="fw-semibold fs-14"><i class="ri-mail-send-line me-1"></i> Client Messages</span>
                    @if (($myPendingClientMessages ?? 0) > 0)
                        <span class="badge bg-dark text-warning">{{ $myPendingClientMessages }}</span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4">
                    @if (($myPendingClientMessages ?? 0) > 0)
                        <div class="avatar-sm mb-3">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-1 text-warning">{{ $myPendingClientMessages }} Pending</h5>
                        <p class="text-muted fs-13 mb-3">
                            {{ $myPendingClientMessages === 1 ? 'Your message is' : 'Your messages are' }}
                            awaiting review by the leader.
                        </p>
                        <a href="{{ route('client.message.my.list') }}" class="btn btn-sm btn-warning">
                            View My Messages
                        </a>
                    @elseif (Auth::user()->hasAnyRole(['Leader', 'Co Leader']) && ($pendingClientMessageCount ?? 0) > 0)
                        <div class="avatar-sm mb-3">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-3">
                                <i class="ri-mail-check-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $pendingClientMessageCount }} to Review</h5>
                        <p class="text-muted fs-13 mb-3">Team messages are waiting for your approval.</p>
                        <a href="{{ route('client.message.review.list') }}" class="btn btn-sm btn-warning">
                            Review Now
                        </a>
                    @else
                        <div class="avatar-sm mb-3">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-3">
                                <i class="ri-mail-check-line"></i>
                            </span>
                        </div>
                        <p class="text-muted fs-13 mb-3">No pending client messages.</p>
                        @if (Auth::user()->hasAnyRole(['Stack Lead', 'Member', 'Probation']))
                            <a href="{{ route('client.message.create') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-add-line me-1"></i> Send Message
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>
    @endif
    {{-- ── End Personal Dashboard Widgets ─────────────────────────────────── --}}

@endsection


@push('script')
    <script>
        $(document).ready(function() {
            let userID = "{{ Auth::id() }}"; // Get user ID from Laravel Blade
            let storageKey = `tasks_${userID}`; // Unique key for each user's tasks

            function loadTasks() {
                let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
                $('#todo-list').empty();
                tasks.forEach(function(task, index) {
                    let taskHTML = `
            <li class="list-group-item border-0 ps-0 ${task.completed ? 'completed' : ''}" data-index="${index}">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input todo-done" ${task.completed ? 'checked' : ''}>
                    <label class="form-check-label">${task.completed ? `<s>${task.text}</s>` : task.text}</label>
                    <span style="cursor:pointer;" class="delete-btn text-danger float-end"><i class="ri-eraser-line"></i></span>
                </div>
            </li>
        `;
                    $('#todo-list').append(taskHTML);
                });
            }

            // Add new task
            $('#todo-form').submit(function(e) {
                e.preventDefault();
                let taskText = $('#todo-input-text').val();
                if (taskText) {
                    let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
                    tasks.push({
                        text: taskText,
                        completed: false
                    });
                    localStorage.setItem(storageKey, JSON.stringify(tasks));
                    $('#todo-input-text').val('');
                    loadTasks();
                }
            });

            // Mark task as completed or not
            $(document).on('click', '.todo-done', function() {
                let taskIndex = $(this).closest('li').data('index');
                let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
                tasks[taskIndex].completed = !tasks[taskIndex].completed;
                localStorage.setItem(storageKey, JSON.stringify(tasks));
                loadTasks();
            });

            // Delete task
            $(document).on('click', '.delete-btn', function() {
                let taskIndex = $(this).closest('li').data('index');
                let tasks = JSON.parse(localStorage.getItem(storageKey)) || [];
                tasks.splice(taskIndex, 1);
                localStorage.setItem(storageKey, JSON.stringify(tasks));
                loadTasks();
            });

            // Initial load
            loadTasks();
        });
    </script>
@endpush
