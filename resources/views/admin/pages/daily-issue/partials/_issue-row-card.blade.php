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
<div class="col-12" id="issue-card-wrap-{{ $issue->id }}">
    <div id="issue-card-{{ $issue->id }}" class="issue-card issue-card-{{ $typeKey }} {{ $commentCount ? 'has-comments' : '' }} card border d-flex flex-row p-0">
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
                        <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} rounded-pill" style="{{ $badgeStyle }}" title="Issue Type">{{ $issue->type }}</span>
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
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                <i class="ri-user-received-2-line me-1"></i>Responsible Person(s): {{ $issue->responsibles->pluck('name')->join(', ') }}
                            </span>
                        @endif
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                            <i class="ri-user-add-line me-1"></i>By: {{ $issue->creator->name ?? 'N/A' }}
                        </span>

                        @if ($status === 'completed' && $issue->completer)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                <i class="ri-check-line me-1"></i>{{ $issue->completer->name }}
                                @if ($issue->completed_at)
                                    · {{ $issue->completed_at->format('h:i A') }}
                                @endif
                            </span>
                        @endif

                        <span id="comment-badge-slot-{{ $issue->id }}">
                            @if ($commentCount)
                                <span class="badge comment-badge" role="button"
                                    onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})"
                                    title="{{ $commentCount }} comment{{ $commentCount > 1 ? 's' : '' }}">
                                    <i class="ri-chat-3-fill"></i> {{ $commentCount }}
                                </span>
                            @endif
                        </span>
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
                                    <span id="comment-menu-count-{{ $issue->id }}">
                                        @if ($commentCount)
                                            <span class="badge bg-warning text-dark ms-1">{{ $commentCount }}</span>
                                        @endif
                                    </span>
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
