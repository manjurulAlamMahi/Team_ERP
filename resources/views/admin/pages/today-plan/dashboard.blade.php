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
                <table class="table table-striped table-centered">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Approved Plans</th>
                            <th>Pending Review</th>
                            <th>Leader Assigned</th>
                            <th>Personal</th>
                            <th>Completed</th>
                            <th>Not Done</th>
                            <th class="text-end">Action</th>
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
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('today.plan.member.detail', $summary['user']->id) }}"
                                            class="btn btn-sm btn-soft-primary" title="View">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </div>
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
        initClientSelectField('assignPlanClient', '#assignTaskModal');
        $('#assignTaskMemberSelect').select2({ width: '100%', dropdownParent: $('#assignTaskModal') });

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
