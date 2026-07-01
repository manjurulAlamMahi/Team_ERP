@extends('admin.master')

@section('title', 'Completed Issues')
@section('quickAccessicon', 'ri-checkbox-circle-line')

@push('style')
    <style>
        .issue-card { border-radius: 10px; transition: box-shadow 0.2s; }
        .issue-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.10) !important; }

        .issue-card-critical  { background: #fff5f5; border-color: #dc3545 !important; }
        .issue-card-urgent    { background: #fff8f8; border-color: #e07b80 !important; }
        .issue-card-high      { background: #f0f5ff; border-color: #0d6efd !important; }
        .issue-card-normal    { background: #f0fff4; border-color: #198754 !important; }

        .issue-card-critical .issue-title  { color: #dc3545; }
        .issue-card-urgent   .issue-title  { color: #c0434a; }
        .issue-card-high     .issue-title  { color: #0d6efd; }
        .issue-card-normal   .issue-title  { color: #198754; }

        .issue-type-strip { width: 5px; border-radius: 9px 0 0 9px; flex-shrink: 0; }
        .strip-critical { background: #dc3545; }
        .strip-urgent   { background: #e07b80; }
        .strip-high     { background: #0d6efd; }
        .strip-normal   { background: #198754; }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="ri-checkbox-circle-line me-1 text-success"></i> Completed Issues
            <span class="text-muted fw-normal fs-14">— {{ $date->format('d F Y') }}</span>
        </h5>

        @if ($isLead)
            <form method="GET" action="{{ route('daily.issue.completed') }}" class="d-flex align-items-center gap-2">
                <label class="text-muted fs-13 mb-0">Filter by date:</label>
                <input type="date" name="date" class="form-control form-control-sm"
                    value="{{ $date->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-sm btn-primary">Go</button>
                <a href="{{ route('daily.issue.completed') }}" class="btn btn-sm btn-light">Today</a>
            </form>
        @endif
    </div>

    @php $authUser = Auth::user(); @endphp

    @if ($issues->isEmpty())
        <div class="card card-body text-center text-muted py-5">
            <i class="ri-inbox-line fs-1 d-block mb-2"></i>
            <p class="mb-0 fs-15">No completed issues for {{ $date->format('d F Y') }}.</p>
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
                <div class="col-12">
                    <div class="issue-card issue-card-{{ $typeKey }} card border d-flex flex-row p-0">
                        <div class="issue-type-strip strip-{{ $typeKey }}"></div>

                        <div class="card-body p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="issue-title fw-semibold fs-15 mb-1">{{ $issue->issue }}</div>

                                    <div class="d-flex flex-wrap gap-3 text-muted small mb-2">
                                        <span><i class="ri-user-3-line me-1"></i>{{ $issue->client_name }}</span>
                                        <span><i class="ri-profile-line me-1"></i>{{ $issue->profile_name }}</span>
                                        <span><i class="ri-calendar-line me-1"></i>{{ $issue->issue_date->format('d M Y') }}</span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge bg-{{ $badgeColor }}" style="{{ $badgeStyle }}">{{ $issue->type }}</span>

                                        @if ($issue->responsibles->isNotEmpty())
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="ri-user-received-2-line me-1"></i>Assigned: {{ $issue->responsibles->pluck('name')->join(', ') }}
                                            </span>
                                        @endif
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            <i class="ri-user-add-line me-1"></i>By: {{ $issue->creator->name ?? 'N/A' }}
                                        </span>

                                        @if ($issue->completer)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="ri-check-line me-1"></i>{{ $issue->completer->name }}
                                                @if ($issue->completed_at)
                                                    · {{ $issue->completed_at->format('h:i A') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Three-dot dropdown --}}
                                <div class="dropdown ms-2 flex-shrink-0">
                                    <button class="btn btn-sm btn-light px-2 py-1" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})">
                                                <i class="ri-chat-3-line me-2 text-primary"></i> Comments
                                            </a>
                                        </li>
                                        @if ($issue->isReversibleBy($authUser))
                                            <li>
                                                <a class="dropdown-item text-warning" href="javascript:void(0);"
                                                    onclick="reverseIssue({{ $issue->id }})">
                                                    <i class="ri-arrow-go-back-line me-2"></i> Reverse
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
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
