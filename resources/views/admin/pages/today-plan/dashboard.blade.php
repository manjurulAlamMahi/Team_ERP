@extends('admin.master')

@section('title', "Today's Plan - Team Dashboard")
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-calendar-todo-line"></i> Team Dashboard - {{ $team->name }} -
                        {{ today()->format('Y-m-d') }}</span>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#assignTaskModal">
                        <i class="ri-add-line"></i> Assign Task
                    </button>
                </h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Approved Plans</th>
                            <th>Pending Review</th>
                            <th>Leader Assigned</th>
                            <th>Personal</th>
                            <th>Completed</th>
                            <th>Not Done</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summaries as $summary)
                            <tr>
                                <td>{{ $summary['user']->name }}</td>
                                <td>{{ $summary['approved_planned'] }}</td>
                                <td>{{ $summary['pending_planned'] }}</td>
                                <td>{{ $summary['leader_assigned'] }}</td>
                                <td>{{ $summary['personal'] }}</td>
                                <td>{{ $summary['completed'] }}</td>
                                <td>{{ $summary['pending_completion'] }}</td>
                                <td>
                                    <a href="{{ route('today.plan.member.detail', $summary['user']->id) }}"
                                        class="text-reset fs-16 px-1">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.pages.today-plan.partials._assign-task-modal')
@endsection

@push('script')
    <script>
        const assignPlanClientField = initClientSelectField('assignPlanClient', '#assignTaskModal');

        $(document).on('change', '#assignTaskMemberSelect', function () {
            var userId = this.value;

            if (!userId) {
                setClientSelectOptions(assignPlanClientField, [], 'Select a team member first.');
                return;
            }

            var url = "{{ route('client.assigned.to.member', ['userId' => '__ID__']) }}".replace('__ID__', userId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    setClientSelectOptions(assignPlanClientField, response.data || [], 'No clients assigned to this member.');
                },
                error: function () {
                    setClientSelectOptions(assignPlanClientField, [], 'Unable to load clients for this member.');
                }
            });
        });

        $(document).on('submit', '#assignTaskForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('today.plan.assign.store') }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.errors ?
                        Object.values(xhr.responseJSON.errors).flat().join(' ') :
                        'Unable to assign task.';
                    Toast.fire({
                        icon: 'error',
                        title: message
                    });
                }
            });
        });
    </script>
@endpush
