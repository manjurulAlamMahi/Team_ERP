@extends('admin.master')

@section('title', 'All Tasks')
@section('quickAccessicon', 'ri-list-check-3')

@push('style')
    <style>
        .remarks-text { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
@endpush

@section('content')
    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('daily.task.all') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 fs-12 text-muted">Member</label>
                        <select name="member_id" class="form-select form-select-sm">
                            <option value="">All Members</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 fs-12 text-muted">Task By</label>
                        <select name="source" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="self" {{ request('source') === 'self' ? 'selected' : '' }}>My Self</option>
                            <option value="leader" {{ request('source') === 'leader' ? 'selected' : '' }}>Leader</option>
                            <option value="co_leader" {{ request('source') === 'co_leader' ? 'selected' : '' }}>Co Leader</option>
                            <option value="stack_lead" {{ request('source') === 'stack_lead' ? 'selected' : '' }}>Stack Leader</option>
                        </select>
                    </div>
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 fs-12 text-muted">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Tasks</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed Tasks</option>
                        </select>
                    </div>
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 fs-12 text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-select form-select-sm" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-sm-auto">
                        <label class="form-label mb-1 fs-12 text-muted">End Date</label>
                        <input type="date" name="end_date" class="form-select form-select-sm" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-sm-auto d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('daily.task.all') }}" class="btn btn-sm btn-light">Reset</a>
                    </div>
                    <div class="col-sm-auto ms-auto">
                        <a href="{{ route('daily.task.assign') }}" class="btn btn-sm btn-success">
                            <i class="ri-user-add-line me-1"></i> Assign Task
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px"></th>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Client / Profile</th>
                            <th>Task By</th>
                            <th>Plan Details</th>
                            <th>Remarks</th>
                            <th>Expected</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            @php $isOwn = $task->user_id === Auth::id(); @endphp
                            <tr class="{{ $task->status === 'completed' ? 'table-success' : '' }}" id="all-row-{{ $task->id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="form-check-input {{ $isOwn ? 'own-task-checkbox' : '' }}"
                                        data-id="{{ $task->id }}"
                                        {{ $task->status === 'completed' ? 'checked' : '' }}
                                        {{ !$isOwn ? 'disabled' : '' }}
                                        title="{{ !$isOwn ? 'Only the assigned member can mark this' : '' }}">
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge {{ $task->task_date->isToday() ? 'bg-primary' : 'bg-light text-dark border' }}">
                                        {{ $task->formatted_date }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $task->user->name }}</div>
                                    @if ($task->user->stack)
                                        <small class="text-muted">{{ $task->user->stack->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $task->client_name }}</div>
                                    <small class="text-muted">{{ $task->profile_name }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $task->source === 'self' ? 'bg-info' : 'bg-warning text-dark' }}">
                                        {{ $task->task_by_label }}
                                    </span>
                                </td>
                                <td style="max-width:200px;">
                                    <div class="text-truncate" style="max-width:200px;" title="{{ $task->plan_details }}">
                                        {{ $task->plan_details }}
                                    </div>
                                </td>
                                <td>
                                    @if ($task->remarks)
                                        <div class="remarks-text" title="{{ $task->remarks }}">{{ $task->remarks }}</div>
                                        @if ($task->remarksByUser)
                                            <small class="text-muted d-block">by {{ $task->remarksByUser->name }}</small>
                                            <small class="text-muted d-block" title="{{ $task->remarks_updated_at }}">
                                                {{ $task->remarks_updated_at?->diffForHumans() }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{ $task->expected_complete_date ? $task->expected_complete_date->format('d M Y') : '—' }}
                                </td>
                                <td class="text-nowrap">
                                    @if ($task->canBeEditedBy($actor))
                                        <button type="button" class="btn btn-xs btn-outline-secondary open-edit-modal"
                                            data-id="{{ $task->id }}" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-primary open-remarks-modal"
                                            data-id="{{ $task->id }}"
                                            data-remarks="{{ $task->remarks }}"
                                            title="Remarks">
                                            <i class="ri-chat-3-line"></i>
                                        </button>
                                    @endif
                                    @if ($task->canBeDeletedBy($actor))
                                        <button type="button" class="btn btn-xs btn-outline-danger delete-task"
                                            data-id="{{ $task->id }}" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ri-inbox-line fs-2 d-block mb-1"></i> No tasks found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($tasks->hasPages())
            <div class="card-footer">
                {{ $tasks->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTaskForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editTaskId">
                        @include('admin.partials._client-select-field', ['fieldId' => 'editTaskClient', 'autoInit' => false])
                        @include('admin.pages.daily-task.partials._plan-details-field', ['fieldId' => 'editPlanDetails', 'autoInit' => false])
                        <div class="mb-3">
                            <label class="form-label">Expected Complete Date</label>
                            <input type="date" name="expected_complete_date" id="editExpectedDate" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Remarks Modal --}}
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="remarksForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Remarks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="remarksTaskId">
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarksText" class="form-control" rows="5"
                                placeholder="Add your remarks or instructions..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Remarks</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const editPlanField = initPlanDetailsField('editPlanDetails', '#editTaskModal');
        const editClientField = initClientSelectField('editTaskClient', '#editTaskModal');

        // Own task checkbox
        $(document).on('change', '.own-task-checkbox', function () {
            const $cb  = $(this);
            const id   = $cb.data('id');
            const $row = $('#all-row-' + id);

            $.ajax({
                url: "{{ route('daily.task.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status) {
                        const done = res.data.status === 'completed';
                        $cb.prop('checked', done);
                        $row.toggleClass('table-success', done);
                        Toast.fire({ icon: 'success', title: done ? 'Completed!' : 'Reopened.' });
                    }
                },
                error: function () { $cb.prop('checked', !$cb.prop('checked')); }
            });
        });

        // Open edit modal
        $(document).on('click', '.open-edit-modal', function () {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('daily.task.edit') }}",
                type: 'GET',
                data: { id: id },
                success: function (res) {
                    if (res.status) {
                        const t = res.data;
                        $('#editTaskId').val(t.id);
                        setClientSelectOptions(editClientField, t.assignable_clients || [], 'No clients assigned to this member.');
                        editClientField.select.val(t.client_id).trigger('change');
                        setPlanDetailsValue(editPlanField, t.plan_details);
                        $('#editExpectedDate').val(t.expected_complete_date ? t.expected_complete_date.substr(0, 10) : '');
                        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
                    }
                }
            });
        });

        $(document).on('submit', '#editTaskForm', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('daily.task.update') }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function (res) {
                    if (res.status) {
                        Toast.fire({ icon: 'success', title: res.message });
                        setTimeout(() => location.reload(), 800);
                    } else {
                        Toast.fire({ icon: 'error', title: res.message });
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                        : 'Failed to update.';
                    Toast.fire({ icon: 'error', title: msg });
                }
            });
        });

        // Remarks modal
        $(document).on('click', '.open-remarks-modal', function () {
            $('#remarksTaskId').val($(this).data('id'));
            $('#remarksText').val($(this).data('remarks') || '');
            new bootstrap.Modal(document.getElementById('remarksModal')).show();
        });

        $(document).on('submit', '#remarksForm', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('daily.task.remarks') }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function (res) {
                    if (res.status) {
                        Toast.fire({ icon: 'success', title: res.message });
                        setTimeout(() => location.reload(), 800);
                    }
                }
            });
        });

        // Delete
        $(document).on('click', '.delete-task', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Task?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete',
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.task.destroy') }}",
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            if (res.status) {
                                $('#all-row-' + id).remove();
                                Toast.fire({ icon: 'success', title: res.message });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
