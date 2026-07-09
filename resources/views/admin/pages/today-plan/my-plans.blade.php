@extends('admin.master')

@section('title', 'My Plans')
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body mb-3">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-calendar-todo-line"></i> Today's Tasks - {{ today()->format('Y-m-d') }}</span>
                    <div>
                        @if (Auth::user()->hasAnyRole(['Co Leader', 'Stack Lead', 'Member', 'Probation']))
                            <a href="{{ route('today.plan.create') }}" class="btn btn-sm btn-success">
                                <i class="ri-add-line"></i> Submit Plan
                            </a>
                        @endif
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                            data-bs-target="#personalTaskModal">
                            <i class="ri-sticky-note-add-line"></i> Add Personal Task
                        </button>
                    </div>
                </h5>

                @if ($checklist->isEmpty())
                    <p class="text-muted mb-0">No tasks yet for today.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Client</th>
                                <th>Profile</th>
                                <th>Details</th>
                                <th>Source</th>
                                <th>Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($checklist as $task)
                                @include('admin.pages.today-plan.partials._task-row', ['task' => $task, 'canVerify' => false])
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-body">
                <h6 class="text-uppercase bg-light p-2 mb-3">Approved Plans</h6>
                @forelse ($approvedPlanned as $task)
                    <div class="border-bottom pb-2 mb-2">
                        <strong>{{ $task->client_name }}</strong> - {{ $task->profile_name }}
                        <div class="text-muted small">{{ $task->details }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">None yet.</p>
                @endforelse
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-body">
                <h6 class="text-uppercase bg-light p-2 mb-3">Leader Assigned Plans</h6>
                @forelse ($leaderAssigned as $task)
                    <div class="border-bottom pb-2 mb-2">
                        <span class="badge bg-info mb-1">Assigned by Leader</span>
                        <div><strong>{{ $task->client_name }}</strong> - {{ $task->profile_name }}</div>
                        <div class="text-muted small">{{ $task->details }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">None yet.</p>
                @endforelse
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-body">
                <h6 class="text-uppercase bg-light p-2 mb-3">Personal Tasks</h6>
                @forelse ($personal as $task)
                    <div class="border-bottom pb-2 mb-2 d-flex justify-content-between">
                        <div>
                            <strong>{{ $task->client_name }}</strong> - {{ $task->profile_name }}
                            <div class="text-muted small">{{ $task->details }}</div>
                        </div>
                        <a href="javascript:void(0);" onclick="deletePersonalTask({{ $task->id }})" class="text-reset">
                            <i class="ri-delete-bin-2-line"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">None yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($pendingPlanned->isNotEmpty() || $rejectedPlanned->isNotEmpty())
        <div class="card card-body mt-3">
            <h6 class="text-uppercase bg-light p-2 mb-3">Awaiting / Past Review</h6>
            @foreach ($pendingPlanned->merge($rejectedPlanned) as $task)
                @php
                    $statusColor = $task->status === 'rejected' ? 'danger' : 'warning';
                @endphp
                <div class="border-bottom pb-2 mb-2">
                    <span class="badge bg-{{ $statusColor }} text-uppercase">{{ $task->status }}</span>
                    <strong>{{ $task->client_name }}</strong> - {{ $task->profile_name }}
                    <div class="text-muted small">{{ $task->details }}</div>
                    @if ($task->review_comment)
                        <div class="text-muted small">Leader comment: {{ $task->review_comment }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @include('admin.pages.today-plan.partials._personal-task-modal')
@endsection

@push('script')
    <script>
        const personalTaskClientField = initClientSelectField('personalTaskClient', '#personalTaskModal');

        function toggleComplete(id) {
            $.ajax({
                url: "{{ route('today.plan.toggle.complete') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        location.reload();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong'
                    });
                }
            });
        }

        function deletePersonalTask(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete this task?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('today.plan.personal.destroy') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
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
                        }
                    });
                }
            });
        }

        $(document).on('submit', '#personalTaskForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('today.plan.personal.store') }}",
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
                        'Unable to add task.';
                    Toast.fire({
                        icon: 'error',
                        title: message
                    });
                }
            });
        });
    </script>
@endpush
