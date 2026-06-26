@php
    $lastMessageFiles = $clientMessage->attachments->where('type', 'last_message');
@endphp

<div class="card border mb-3">
    <div class="card-body d-flex align-items-center">
        @if ($clientMessage->type?->icon)
            <img src="{{ asset($clientMessage->type->icon) }}" alt="" class="me-3" style="height: 40px; width: 40px; object-fit: contain;">
        @else
            <i class="ri-file-list-3-line fs-2 text-primary me-3"></i>
        @endif
        <div>
            <div class="text-muted small">Message Type</div>
            <h5 class="mb-0">{{ $clientMessage->type->name ?? 'N/A' }}</h5>
        </div>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-user-line"></i> Submitted By</h6>
        <div class="row">
            <div class="col-4">
                <div class="text-muted small">Employee Name</div>
                <div>{{ $clientMessage->submitter->name ?? 'N/A' }}</div>
            </div>
            <div class="col-4">
                <div class="text-muted small">ID</div>
                <div>{{ $clientMessage->submitter->employee_id ?? 'N/A' }}</div>
            </div>
            <div class="col-4">
                <div class="text-muted small">Stack</div>
                <div>{{ $clientMessage->submitter->stack->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-user-search-line"></i> Client Info</h6>
        <div class="row mb-3">
            <div class="col-6">
                <div class="text-muted small">Client Name</div>
                <div>{{ $clientMessage->client_name }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Profile Name</div>
                <div>{{ $clientMessage->profile_name }}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="text-muted small mb-1">Client's Last Message ({{ ucfirst($clientMessage->last_message_type) }})</div>
                @forelse ($lastMessageFiles as $file)
                    <a href="javascript:void(0)" class="preview-trigger d-inline-block me-2 mb-2"
                        data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                        <img src="{{ asset($file->path) }}" alt="{{ $file->original_name }}" class="rounded border" style="max-height: 120px;">
                    </a>
                @empty
                    <span class="text-muted">No file attached.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
