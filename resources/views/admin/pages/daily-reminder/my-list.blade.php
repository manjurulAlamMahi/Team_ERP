@extends('admin.master')

@section('title', 'Daily Reminder')
@section('quickAccessicon', 'ri-alarm-line')

@push('style')
    <style>
        .reminder-card { border-radius: 10px; transition: box-shadow 0.2s; }
        .reminder-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.10) !important; }
        .reminder-card-overdue { background: #fff5f5; border-color: #dc3545 !important; }
        .reminder-card-today { background: #fff8e1; border-color: #ffc107 !important; }
        .reminder-card-upcoming { background: #f0fff4; border-color: #198754 !important; }
        .reminder-checkbox {
            width: 22px; height: 22px; cursor: pointer;
            border: 2px solid #adb5bd; border-radius: 6px;
        }
        .reminder-checkbox:checked { background-color: #198754; border-color: #198754; }
    </style>
@endpush

@section('content')
    @php $authUser = Auth::user(); $isLead = $authUser->hasAnyRole(['Leader', 'Co Leader']); @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0"><i class="ri-alarm-line me-1"></i> Daily Reminder</h5>
        <div class="d-flex gap-2">
            @if ($isLead)
                <a href="{{ route('daily.reminder.assign') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-user-add-line me-1"></i> Assign to Member
                </a>
            @endif
            <a href="{{ route('daily.reminder.create') }}" class="btn btn-sm btn-success">
                <i class="ri-add-line me-1"></i> Create Daily Reminder
            </a>
        </div>
    </div>

    @if ($reminders->isEmpty())
        <div class="card card-body text-center text-muted py-5">
            <i class="ri-checkbox-circle-line fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0 fs-15">No reminders. You're all caught up!</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($reminders as $reminder)
                @php
                    $daysLeft = today()->diffInDays($reminder->due_date, false);
                    $stateKey = $daysLeft < 0 ? 'overdue' : ($daysLeft === 0 ? 'today' : 'upcoming');
                @endphp
                <div class="col-12" id="reminder-card-wrap-{{ $reminder->id }}">
                    <div class="reminder-card reminder-card-{{ $stateKey }} card border p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1">
                                <div class="fw-semibold fs-15 mb-1">{{ $reminder->daysLeftLabel() }}</div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge bg-light text-dark border">
                                        <i class="ri-time-line me-1"></i>Due at {{ \Carbon\Carbon::parse($reminder->due_time)->format('h:i A') }}
                                    </span>
                                    @if ($reminder->isAssigned())
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                            <i class="ri-user-add-line me-1"></i>Assigned by: {{ $reminder->creator->name ?? 'N/A' }}
                                        </span>
                                    @endif
                                    @if ($reminder->user_id !== $authUser->id)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            <i class="ri-user-line me-1"></i>Assigned to: {{ $reminder->user->name ?? 'N/A' }}
                                        </span>
                                    @endif
                                    @if ($reminder->isAssigned() && $reminder->user_id === $authUser->id && !$reminder->isCompletableBy($authUser))
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill" title="Only your Leader/Co Leader can mark this as completed">
                                            <i class="ri-eye-line me-1"></i>View only
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-end gap-2 ms-2 flex-shrink-0">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light px-2 py-1" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        @if ($reminder->isEditableBy($authUser))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('daily.reminder.edit', $reminder->id) }}">
                                                    <i class="ri-edit-line me-2 text-secondary"></i> Edit
                                                </a>
                                            </li>
                                        @endif
                                        @if ($reminder->isCancelableBy($authUser))
                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="cancelReminder({{ $reminder->id }})">
                                                    <i class="ri-close-circle-line me-2"></i> Cancel
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                @if ($reminder->isCompletableBy($authUser))
                                    <div class="form-check mb-0" title="Mark as completed">
                                        <input type="checkbox" class="reminder-checkbox form-check-input"
                                            id="chk-{{ $reminder->id }}" onclick="markComplete({{ $reminder->id }})">
                                        <label class="form-check-label small text-muted" for="chk-{{ $reminder->id }}">Completed</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('script')
    <script>
        function markComplete(id) {
            $.ajax({
                url: "{{ route('daily.reminder.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status) {
                        $('#reminder-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
                        Toast.fire({ icon: 'success', title: response.message });
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                        location.reload();
                    }
                },
                error: function () {
                    Toast.fire({ icon: 'error', title: 'Something went wrong.' });
                    location.reload();
                }
            });
        }

        function cancelReminder(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Cancel this assigned reminder?',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.reminder.assign.destroy') }}",
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status) {
                                $('#reminder-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Toast.fire({ icon: 'error', title: response.message });
                            }
                        }
                    });
                }
            });
        }
    </script>
@endpush
