<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-centered align-middle mb-0">
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
                                <span class="badge rounded-pill {{ $task->task_date->isToday() ? 'bg-primary-subtle text-primary' : ($task->task_date->isYesterday() ? 'bg-secondary-subtle text-secondary' : 'bg-light text-dark border') }}">
                                    {{ $task->formatted_date }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $task->client_name }}</div>
                                <small class="text-muted">{{ $task->profile_name }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $task->source === 'self' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning' }}">
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
                                {{ $emptyMessage }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
