@extends('admin.master')

@section('title', 'My Tasks')
@section('quickAccessicon', 'ri-task-line')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">My Daily Tasks</h5>
        <a href="{{ route('daily.task.add') }}" class="btn btn-primary btn-sm">
            <i class="ri-add-line me-1"></i> Add Task
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px"></th>
                            <th>Date</th>
                            <th>Client / Profile</th>
                            <th>Task By</th>
                            <th>Plan Details</th>
                            <th>Remarks</th>
                            <th>Expected Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr class="{{ $task->status === 'completed' ? 'table-success' : '' }}" id="task-row-{{ $task->id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                        class="form-check-input task-checkbox"
                                        data-id="{{ $task->id }}"
                                        {{ $task->status === 'completed' ? 'checked' : '' }}>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge {{ $task->task_date->isToday() ? 'bg-primary' : ($task->task_date->isYesterday() ? 'bg-secondary' : 'bg-light text-dark border') }}">
                                        {{ $task->formatted_date }}
                                    </span>
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
                                <td style="max-width:220px;">
                                    <div class="text-truncate" style="max-width:220px;" title="{{ $task->plan_details }}">
                                        {{ $task->plan_details }}
                                    </div>
                                </td>
                                <td style="max-width:180px;">
                                    @if ($task->remarks)
                                        <div class="text-truncate" style="max-width:180px;" title="{{ $task->remarks }}">
                                            {{ $task->remarks }}
                                        </div>
                                        @if ($task->remarksByUser)
                                            <small class="text-muted">by {{ $task->remarksByUser->name }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{ $task->expected_complete_date ? $task->expected_complete_date->format('d M Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ri-inbox-line fs-2 d-block mb-1"></i>
                                    No tasks yet. <a href="{{ route('daily.task.add') }}">Add your first task</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('change', '.task-checkbox', function () {
            const $cb  = $(this);
            const id   = $cb.data('id');
            const $row = $('#task-row-' + id);

            $.ajax({
                url: "{{ route('daily.task.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.status) {
                        const done = res.data.status === 'completed';
                        $cb.prop('checked', done);
                        $row.toggleClass('table-success', done);
                        Toast.fire({ icon: 'success', title: done ? 'Task marked as completed!' : 'Task reopened.' });
                    }
                },
                error: function () {
                    $cb.prop('checked', !$cb.prop('checked'));
                    Toast.fire({ icon: 'error', title: 'Something went wrong.' });
                }
            });
        });
    </script>
@endpush
