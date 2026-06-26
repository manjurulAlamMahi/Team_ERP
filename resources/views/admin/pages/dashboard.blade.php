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

    @if (isset($team))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="header-title mb-0">
                            <i class="ri-team-line"></i> {{ $team->name }}
                            <span class="badge bg-primary ms-1">{{ $totalMembers }} Members</span>
                        </h4>
                        <div>
                            @foreach ($stackBreakdown as $stackName => $count)
                                <span class="badge bg-secondary me-1">{{ $stackName }}: {{ $count }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Stack</th>
                                        <th>Today's Plan</th>
                                        <th>Issue Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teamOverview as $row)
                                        @php
                                            $planBadge = match ($row['plan_status']) {
                                                'approved' => ['success', 'Approved'],
                                                'pending' => ['warning', 'Pending Review'],
                                                'rejected' => ['danger', 'Rejected'],
                                                default => ['secondary', 'Not Submitted'],
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $row['user']->name }}</td>
                                            <td>{{ $row['user']->stack->name ?? 'Unassigned' }}</td>
                                            <td><span class="badge bg-{{ $planBadge[0] }}">{{ $planBadge[1] }}</span>
                                            </td>
                                            <td>
                                                @if ($row['has_open_issue'])
                                                    <span class="badge bg-danger">Open Issue</span>
                                                @else
                                                    <span class="badge bg-success">Clear</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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

                @can('dashboard_quickAccess')
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
                @endcan

                @can('dashboard_todolist')
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
                @endcan

                @can('dashboard_unreadMessages')
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
                @endcan

            </div>
        </div>
        <!-- Welcome Ends -->
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

    </div>
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
