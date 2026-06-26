@php
    $outgoingFiles = $clientMessage->attachments->where('type', 'attachment');
@endphp

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-message-3-line"></i> Employee Message</h6>
        <div class="p-3 bg-light-subtle border-start border-primary border-4 rounded mb-3 fs-5">
            {!! $clientMessage->their_message !!}
        </div>

        <h6 class="text-muted small text-uppercase mb-2">Attachment</h6>
        @forelse ($outgoingFiles as $file)
            <a href="javascript:void(0)" class="preview-trigger d-block"
                data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                <i class="ri-file-line"></i> {{ $file->original_name }}
            </a>
        @empty
            <span class="text-muted">No attachment.</span>
        @endforelse
    </div>
</div>
