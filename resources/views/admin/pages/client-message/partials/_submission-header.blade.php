@php
    $statusColor = match ($clientMessage->status) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning',
    };
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="badge bg-{{ $statusColor }} text-uppercase fs-6">{{ $clientMessage->status }}</span>
    <span class="text-muted small" title="{{ $clientMessage->created_at->format('Y-m-d H:i') }}">
        Submitted {{ $clientMessage->created_at->diffForHumans() }}
    </span>
</div>

@if ($clientMessage->status === 'rejected' && $clientMessage->rejection_reason)
    <div class="alert alert-danger d-flex align-items-start mb-3">
        <i class="ri-error-warning-line fs-3 me-2"></i>
        <div>
            <strong class="d-block">Rejected</strong>
            {{ $clientMessage->rejection_reason }}
        </div>
    </div>
@endif

@if ($clientMessage->reviewer)
    <div class="text-muted small mb-3">
        Reviewed by {{ $clientMessage->reviewer->name }} {{ $clientMessage->reviewed_at?->diffForHumans() }}
    </div>
@endif
