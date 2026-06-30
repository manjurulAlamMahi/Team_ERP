@extends('admin.master')

@section('title', 'Completed Tasks')
@section('quickAccessicon', 'ri-checkbox-circle-line')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">
            Completed Tasks
            <span class="text-muted fw-normal fs-14">— {{ $date->format('d F Y') }}</span>
        </h5>

        @if ($isLead)
            <form method="GET" action="{{ route('daily.task.completed') }}" class="d-flex align-items-center gap-2">
                <label class="text-muted fs-13 mb-0">Filter by date:</label>
                <input type="date" name="date" class="form-control form-control-sm"
                    value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-sm btn-primary">Go</button>
                <a href="{{ route('daily.task.completed') }}" class="btn btn-sm btn-light">Today</a>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            @if ($isLead)
                                <th>Member</th>
                            @endif
                            <th>Client / Profile</th>
                            <th>Task By</th>
                            <th>Plan Details</th>
                            <th>Remarks</th>
                            <th>Expected Date</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                @if ($isLead)
                                    <td>
                                        <div class="fw-medium">{{ $task->user->name }}</div>
                                        @if ($task->user->stack)
                                            <small class="text-muted">{{ $task->user->stack->name }}</small>
                                        @endif
                                    </td>
                                @endif
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
                                <td style="max-width:160px;">
                                    @if ($task->remarks)
                                        <div class="text-truncate" style="max-width:160px;" title="{{ $task->remarks }}">{{ $task->remarks }}</div>
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
                                <td class="text-nowrap">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ $task->completed_at?->format('h:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isLead ? 7 : 6 }}" class="text-center text-muted py-4">
                                    <i class="ri-inbox-line fs-2 d-block mb-1"></i>
                                    No completed tasks for {{ $date->format('d F Y') }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
