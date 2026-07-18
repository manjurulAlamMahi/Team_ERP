@php
    $sourceLabel = match ($task->source) {
        'leader_assigned' => 'Assigned by Leader',
        'personal' => 'Personal',
        default => 'Planned',
    };
    $sourceColor = match ($task->source) {
        'leader_assigned' => 'bg-info-subtle text-info',
        'personal' => 'bg-secondary-subtle text-secondary',
        default => 'bg-success-subtle text-success',
    };
@endphp
<tr>
    <td>
        @if ($task->status !== 'approved')
            <span class="badge {{ $task->status === 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} rounded-pill text-uppercase">{{ $task->status }}</span>
        @elseif ($canVerify ?? false)
            <i class="ri-checkbox{{ $task->is_completed ? '-line text-success' : '-blank-line text-muted' }} fs-18"></i>
        @else
            <input type="checkbox" class="form-check-input" {{ $task->is_completed ? 'checked' : '' }}
                onclick="toggleComplete({{ $task->id }})">
        @endif
    </td>
    <td>{{ $task->client_name }}</td>
    <td>{{ $task->profile_name }}</td>
    <td>{{ $task->details }}</td>
    <td><span class="badge {{ $sourceColor }} rounded-pill">{{ $sourceLabel }}</span></td>
    <td>
        @if ($task->status === 'approved')
            @if ($task->is_completed)
                @if ($task->leader_verified === true)
                    <span class="badge bg-success-subtle text-success rounded-pill">Verified</span>
                @elseif ($task->leader_verified === false)
                    <span class="badge bg-danger-subtle text-danger rounded-pill">Reopened</span>
                @else
                    <span class="badge bg-warning-subtle text-warning rounded-pill">Awaiting Verification</span>
                @endif
                @if ($task->completion_comment)
                    <div class="text-muted small">{{ $task->completion_comment }}</div>
                @endif
            @else
                <span class="badge bg-light text-dark rounded-pill">Not done</span>
            @endif
        @elseif ($task->review_comment)
            <div class="text-muted small">{{ $task->review_comment }}</div>
        @endif

        @if (($canVerify ?? false) && $task->status === 'approved' && $task->is_completed)
            <div class="mt-1 d-flex gap-1">
                @if ($task->leader_verified !== true)
                    <button type="button" class="btn btn-sm btn-success" onclick="verifyComplete({{ $task->id }})">Verify</button>
                @endif
                <button type="button" class="btn btn-sm btn-warning" onclick="reopenTaskPrompt({{ $task->id }})">Reopen</button>
            </div>
        @endif
    </td>
</tr>
