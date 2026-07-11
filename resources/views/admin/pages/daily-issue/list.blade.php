@extends('admin.master')

@section('title', 'View Issues')
@section('quickAccessicon', 'ri-alert-line')

@push('style')
    <style>
        .issue-card {
            border-radius: 10px;
            transition: box-shadow 0.2s;
            position: relative;
        }
        .issue-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.10) !important; }

        /* type colours */
        .issue-card-critical  { background: #fff5f5; border-color: #dc3545 !important; }
        .issue-card-urgent    { background: #fff8f8; border-color: #e07b80 !important; }
        .issue-card-high      { background: #f0f5ff; border-color: #0d6efd !important; }
        .issue-card-normal    { background: #f0fff4; border-color: #198754 !important; }

        .issue-card-critical .issue-title  { color: #dc3545; }
        .issue-card-urgent   .issue-title  { color: #c0434a; }
        .issue-card-high     .issue-title  { color: #0d6efd; }
        .issue-card-normal   .issue-title  { color: #198754; }

        /* Strip — explicit border-radius so we don't need overflow-hidden on card */
        .issue-type-strip {
            width: 5px;
            flex-shrink: 0;
            border-radius: 9px 0 0 9px;
        }
        .strip-critical { background: #dc3545; }
        .strip-urgent   { background: #e07b80; }
        .strip-high     { background: #0d6efd; }
        .strip-normal   { background: #198754; }

        /* Done checkbox — more prominent */
        .issue-checkbox {
            width: 22px;
            height: 22px;
            cursor: pointer;
            border: 2px solid #adb5bd;
            border-radius: 6px;
        }
        .issue-checkbox:checked {
            background-color: #198754;
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25,135,84,.25);
        }
        .issue-checkbox:not(:disabled):hover {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25,135,84,.15);
        }
        .issue-checkbox:disabled { opacity: 0.35; cursor: not-allowed; }

        .issue-serial {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0"><i class="ri-alert-line me-1"></i> Issues</h5>
        @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']))
            <a href="{{ route('daily.issue.create') }}" class="btn btn-sm btn-success">
                <i class="ri-add-line me-1"></i> Add Issue
            </a>
        @endif
    </div>

    @php $authUser = Auth::user(); @endphp

    {{-- Filter bar --}}
    <div class="card card-body mb-3">
        <form method="GET" action="{{ route('daily.issue.list') }}" class="row gy-2 gx-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1 small text-muted">Filter</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Issue</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed Issue</option>
                </select>
            </div>

            @if ($status === 'pending')
                <div class="col-auto">
                    <label class="form-label mb-1 small text-muted">Responsible Person</label>
                    <select name="responsible_id" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ (string) request('responsible_id') === (string) $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1 small text-muted">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    <a href="{{ route('daily.issue.list') }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            @else
                <div class="col-auto">
                    <label class="form-label mb-1 small text-muted">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm"
                        value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                </div>
                <input type="hidden" name="status" value="completed">
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    <a href="{{ route('daily.issue.list', ['status' => 'completed']) }}" class="btn btn-sm btn-light">Today</a>
                </div>
            @endif
        </form>
    </div>

    @if ($status === 'completed')
        <p class="text-muted small mb-2">Showing completed issues for <strong>{{ $date->format('d F Y') }}</strong>.</p>
    @endif

    @if ($issues->isEmpty())
        <div class="card card-body text-center text-muted py-5">
            <i class="ri-checkbox-circle-line fs-1 d-block mb-2 text-success"></i>
            <p class="mb-0 fs-15">
                @if ($status === 'completed')
                    No completed issues for {{ $date->format('d F Y') }}.
                @else
                    No open issues. Everything looks good!
                @endif
            </p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($issues as $issue)
                @php
                    $typeKey = match ($issue->type) {
                        'Critical' => 'critical',
                        'Urgent'   => 'urgent',
                        'High'     => 'high',
                        default    => 'normal',
                    };
                    $badgeColor = match ($issue->type) {
                        'Critical' => 'danger',
                        'Urgent'   => 'danger',
                        'High'     => 'primary',
                        default    => 'success',
                    };
                    $badgeStyle = $issue->type === 'Urgent' ? 'opacity:.75;' : '';
                @endphp
                <div class="col-12" id="issue-card-wrap-{{ $issue->id }}">
                    <div class="issue-card issue-card-{{ $typeKey }} card border d-flex flex-row p-0">
                        {{-- colour strip --}}
                        <div class="issue-type-strip strip-{{ $typeKey }}"></div>

                        <div class="card-body p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                {{-- Left: content --}}
                                <div class="flex-grow-1">
                                    {{-- Serial + Issue (formerly category) --}}
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="issue-serial d-inline-flex align-items-center justify-content-center bg-light text-muted border">
                                            {{ $loop->iteration }}
                                        </span>
                                        <span class="issue-title fw-semibold fs-15">{{ $issue->category ?: 'Issue' }}</span>
                                        <span class="badge bg-{{ $badgeColor }}" style="{{ $badgeStyle }}" title="Issue Type">{{ $issue->type }}</span>
                                    </div>

                                    {{-- Remarks (formerly the free-text "issue") --}}
                                    @if ($issue->issue)
                                        <div class="text-muted small mb-2">
                                            <span class="fw-semibold text-body">Remarks:</span> {{ $issue->issue }}
                                        </div>
                                    @endif

                                    {{-- Date / Client / Profile --}}
                                    <div class="d-flex flex-wrap gap-3 text-muted small mb-2">
                                        <span><i class="ri-calendar-line me-1"></i>{{ $issue->issue_date->format('d M Y') }}</span>
                                        <span><i class="ri-user-3-line me-1"></i>{{ $issue->client_name }}</span>
                                        <span><i class="ri-profile-line me-1"></i>{{ $issue->profile_name }}</span>
                                    </div>

                                    {{-- Badges row --}}
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        @if ($issue->responsibles->isNotEmpty())
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="ri-user-received-2-line me-1"></i>Responsible Person(s): {{ $issue->responsibles->pluck('name')->join(', ') }}
                                            </span>
                                        @endif
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            <i class="ri-user-add-line me-1"></i>By: {{ $issue->creator->name ?? 'N/A' }}
                                        </span>

                                        @if ($status === 'completed' && $issue->completer)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="ri-check-line me-1"></i>{{ $issue->completer->name }}
                                                @if ($issue->completed_at)
                                                    · {{ $issue->completed_at->format('h:i A') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Right: checkbox + actions --}}
                                <div class="d-flex flex-column align-items-end gap-2 ms-2 flex-shrink-0">
                                    {{-- Three-dot dropdown --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light px-2 py-1" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="javascript:void(0);"
                                                    onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})">
                                                    <i class="ri-chat-3-line me-2 text-primary"></i> Comments
                                                </a>
                                            </li>
                                            @if ($status === 'pending' && $issue->isEditableBy($authUser))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('daily.issue.edit', $issue->id) }}">
                                                        <i class="ri-edit-line me-2 text-secondary"></i> Edit
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($status === 'completed' && $issue->isReversibleBy($authUser))
                                                <li>
                                                    <a class="dropdown-item text-warning" href="javascript:void(0);"
                                                        onclick="reverseIssue({{ $issue->id }})">
                                                        <i class="ri-arrow-go-back-line me-2"></i> Reverse
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($issue->isDeletableBy($authUser))
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="javascript:void(0);" onclick="deleteIssue({{ $issue->id }})">
                                                        <i class="ri-delete-bin-2-line me-2"></i> Delete
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                    {{-- Mark complete checkbox --}}
                                    @if ($status === 'pending')
                                        <div class="form-check mb-0" title="{{ $issue->isCompletableBy($authUser) ? 'Mark as done' : 'Not your issue to close' }}">
                                            <input type="checkbox"
                                                class="issue-checkbox form-check-input"
                                                id="chk-{{ $issue->id }}"
                                                {{ $issue->isCompletableBy($authUser) ? '' : 'disabled' }}
                                                onclick="markComplete({{ $issue->id }})">
                                            <label class="form-check-label small text-muted" for="chk-{{ $issue->id }}">Done</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @include('admin.pages.daily-issue.partials._comments-modal')
@endsection

@push('script')
    <script>
        function markComplete(id) {
            $.ajax({
                url: "{{ route('daily.issue.complete') }}",
                type: 'POST',
                data: { id: id, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    if (response.status) {
                        $('#issue-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
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

        function deleteIssue(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete this issue?',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.destroy') }}",
                        type: 'POST',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status) {
                                $('#issue-card-wrap-' + id).fadeOut(300, function () { $(this).remove(); });
                                Toast.fire({ icon: 'success', title: response.message });
                            } else {
                                Toast.fire({ icon: 'error', title: response.message });
                            }
                        }
                    });
                }
            });
        }

        function reverseIssue(id) {
            Swal.fire({
                title: 'Reverse to Not Completed',
                input: 'textarea',
                inputPlaceholder: 'Explain why this is not actually completed...',
                showCancelButton: true,
                confirmButtonText: 'Reverse',
                inputValidator: function (value) {
                    if (!value) return 'A comment is required to reverse this issue.';
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.reverse') }}",
                        type: 'POST',
                        data: { id: id, comment: result.value, _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status) {
                                Toast.fire({ icon: 'success', title: response.message });
                                setTimeout(() => location.reload(), 900);
                            } else {
                                Toast.fire({ icon: 'error', title: response.message });
                            }
                        },
                        error: function (xhr) {
                            const msg = xhr.responseJSON?.errors
                                ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                                : 'Unable to reverse issue.';
                            Toast.fire({ icon: 'error', title: msg });
                        }
                    });
                }
            });
        }
    </script>
@endpush
