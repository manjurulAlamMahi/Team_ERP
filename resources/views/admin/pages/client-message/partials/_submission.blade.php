@php
    $statusColor = match ($clientMessage->status) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning',
    };
    $lastMessageFiles = $clientMessage->attachments->where('type', 'last_message');
    $outgoingFiles = $clientMessage->attachments->where('type', 'attachment');
@endphp

<div class="mb-3 d-flex justify-content-between align-items-center">
    <span class="badge bg-{{ $statusColor }} text-uppercase">{{ $clientMessage->status }}</span>
    <span class="text-muted small">Submitted {{ $clientMessage->created_at->format('Y-m-d H:i') }}</span>
</div>

@if ($clientMessage->status === 'rejected' && $clientMessage->rejection_reason)
    <div class="alert alert-danger">
        <strong>Rejection Reason:</strong> {{ $clientMessage->rejection_reason }}
    </div>
@endif

@if ($clientMessage->reviewer)
    <div class="text-muted small mb-3">
        Reviewed by {{ $clientMessage->reviewer->name }} on {{ $clientMessage->reviewed_at?->format('Y-m-d H:i') }}
    </div>
@endif

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-user-line"></i> Submitted By</h6>
        <div class="row mb-2">
            <div class="col-4 text-muted">Submitted By</div>
            <div class="col-8">{{ $clientMessage->submitter->name ?? 'N/A' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-4 text-muted">Message Type</div>
            <div class="col-8">{{ $clientMessage->type->name ?? 'N/A' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-4 text-muted">Client Name</div>
            <div class="col-8">{{ $clientMessage->client_name }}</div>
        </div>
        <div class="row mb-0">
            <div class="col-4 text-muted">Profile Name</div>
            <div class="col-8">{{ $clientMessage->profile_name }}</div>
        </div>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-chat-history-line"></i> Client's Last Message ({{ ucfirst($clientMessage->last_message_type) }})</h6>
        @forelse ($lastMessageFiles as $file)
            <a href="{{ asset($file->path) }}" target="_blank" class="d-inline-block me-2 mb-2">
                <img src="{{ asset($file->path) }}" alt="{{ $file->original_name }}" class="rounded border" style="max-height: 120px;">
            </a>
        @empty
            <span class="text-muted">No file attached.</span>
        @endforelse
    </div>
</div>

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-message-3-line"></i> Client Message</h6>
        <div class="p-3 bg-light-subtle border-start border-primary border-4 rounded mb-3 fs-5">
            {!! $clientMessage->their_message !!}
        </div>

        <h6 class="text-muted small text-uppercase mb-2">Attachment</h6>
        @forelse ($outgoingFiles as $file)
            <a href="{{ asset($file->path) }}" target="_blank" class="d-block">{{ $file->original_name }}</a>
        @empty
            <span class="text-muted">No attachment.</span>
        @endforelse
    </div>
</div>
