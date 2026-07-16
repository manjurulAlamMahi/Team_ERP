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
    $commentCount = $issue->comments_count ?? 0;
@endphp
<div class="col-xl-3 col-lg-4 col-md-6" id="issue-card-wrap-{{ $issue->id }}">
    <div id="issue-card-{{ $issue->id }}" class="issue-card issue-grid-card issue-card-{{ $typeKey }} {{ $commentCount ? 'has-comments' : '' }} card border h-100">
        <div class="issue-type-strip-top strip-{{ $typeKey }}"></div>

        <div class="card-body p-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="issue-serial d-inline-flex align-items-center justify-content-center bg-light text-muted border">
                        {{ $loop->iteration }}
                    </span>
                    <span class="badge bg-{{ $badgeColor }}" style="{{ $badgeStyle }}" title="Issue Type">{{ $issue->type }}</span>
                </div>

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
                                <span id="comment-menu-count-{{ $issue->id }}">
                                    @if ($commentCount)
                                        <span class="badge bg-warning text-dark ms-1">{{ $commentCount }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        @if ($issue->isEditableBy($authUser))
                            <li>
                                <a class="dropdown-item" href="{{ route('daily.issue.edit', $issue->id) }}">
                                    <i class="ri-edit-line me-2 text-secondary"></i> Edit
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
            </div>

            <div class="issue-title fw-semibold fs-14 mb-1">{{ $issue->category ?: 'Issue' }}</div>

            @if ($issue->issue)
                <div class="text-muted small mb-2 issue-remarks-clamp" title="{{ $issue->issue }}">
                    {{ $issue->issue }}
                </div>
            @endif

            <div class="text-muted small mb-1">
                <i class="ri-user-3-line me-1"></i>{{ $issue->client_name }}
                <span class="text-muted">/ {{ $issue->profile_name }}</span>
            </div>
            <div class="text-muted small mb-2">
                <i class="ri-calendar-line me-1"></i>{{ $issue->issue_date->format('d M Y') }}
            </div>

            @if ($issue->responsibles->isNotEmpty())
                <div class="small text-info mb-2 text-truncate" title="{{ $issue->responsibles->pluck('name')->join(', ') }}">
                    <i class="ri-user-received-2-line me-1"></i>{{ $issue->responsibles->pluck('name')->join(', ') }}
                </div>
            @endif

            <div class="mt-auto d-flex justify-content-between align-items-center pt-1">
                <span class="text-muted small text-truncate" title="Assigned by {{ $issue->creator->name ?? 'N/A' }}">
                    By {{ $issue->creator->name ?? 'N/A' }}
                </span>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span id="comment-badge-slot-{{ $issue->id }}">
                        @if ($commentCount)
                            <span class="badge comment-badge" role="button"
                                onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})"
                                title="{{ $commentCount }} comment{{ $commentCount > 1 ? 's' : '' }}">
                                <i class="ri-chat-3-fill"></i> {{ $commentCount }}
                            </span>
                        @endif
                    </span>

                    <div class="form-check mb-0" title="{{ $issue->isCompletableBy($authUser) ? 'Mark as done' : 'Not your issue to close' }}">
                        <input type="checkbox"
                            class="issue-checkbox form-check-input"
                            id="chk-{{ $issue->id }}"
                            {{ $issue->isCompletableBy($authUser) ? '' : 'disabled' }}
                            onclick="markComplete({{ $issue->id }})">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
