@php
    $clientMessage = $clientMessage ?? null;
    $lastMessageType = old('last_message_type', $clientMessage->last_message_type ?? 'image');
    $existingLastMessageFile = $clientMessage?->attachments->where('type', 'last_message')->first();
@endphp

<div class="row mb-3">
    <label class="col-3 col-form-label">Client Name</label>
    <div class="col-9">
        <input type="text" class="form-control form-control-sm @error('client_name') is-invalid @enderror"
            name="client_name" value="{{ old('client_name', $clientMessage->client_name ?? '') }}">
        @error('client_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label class="col-3 col-form-label">Profile Name</label>
    <div class="col-9">
        <input type="text" class="form-control form-control-sm @error('profile_name') is-invalid @enderror"
            name="profile_name" value="{{ old('profile_name', $clientMessage->profile_name ?? '') }}">
        @error('profile_name')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label class="col-3 col-form-label">Client's Last Message</label>
    <div class="col-9">
        <div class="mb-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="last_message_type" id="lastMessageTypeImage"
                    value="image" {{ $lastMessageType === 'image' ? 'checked' : '' }} onchange="toggleLastMessageMultiple()">
                <label class="form-check-label" for="lastMessageTypeImage">Image</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="last_message_type" id="lastMessageTypeMultiple"
                    value="multiple" {{ $lastMessageType === 'multiple' ? 'checked' : '' }} onchange="toggleLastMessageMultiple()">
                <label class="form-check-label" for="lastMessageTypeMultiple">Multiple</label>
            </div>
        </div>
        <input type="file" id="lastMessageFilesInput" name="last_message_files[]"
            class="dropify @error('last_message_files') is-invalid @enderror" accept="image/*"
            @if ($existingLastMessageFile) data-default-file="{{ asset($existingLastMessageFile->path) }}" @endif>
        @error('last_message_files')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @if ($clientMessage && $clientMessage->attachments->where('type', 'last_message')->count())
            <div class="small text-muted mt-1">
                Existing: {{ $clientMessage->attachments->where('type', 'last_message')->pluck('original_name')->join(', ') }}
                (leave empty to keep these)
            </div>
        @endif
    </div>
</div>

<div class="row mb-3">
    <label class="col-3 col-form-label">Their Message</label>
    <div class="col-9">
        <textarea id="their_message" name="their_message" class="form-control @error('their_message') is-invalid @enderror"
            rows="5">{{ old('their_message', $clientMessage->their_message ?? '') }}</textarea>
        @error('their_message')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <label class="col-3 col-form-label">Attachment</label>
    <div class="col-9">
        <input type="file" name="attachment_files[]" multiple
            class="form-control form-control-sm @error('attachment_files') is-invalid @enderror">
        @error('attachment_files')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @if ($clientMessage && $clientMessage->attachments->where('type', 'attachment')->count())
            <div class="small text-muted mt-1">
                Existing: {{ $clientMessage->attachments->where('type', 'attachment')->pluck('original_name')->join(', ') }}
                (leave empty to keep these)
            </div>
        @endif
    </div>
</div>

<script>
    function toggleLastMessageMultiple() {
        const checked = document.querySelector('input[name="last_message_type"]:checked');
        const input = document.getElementById('lastMessageFilesInput');
        if (checked && checked.value === 'multiple') {
            input.setAttribute('multiple', 'multiple');
        } else {
            input.removeAttribute('multiple');
        }
    }
    toggleLastMessageMultiple();
</script>

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/css/dropify.min.css">
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/dropify@0.2.2/dist/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#lastMessageFilesInput').dropify();
        });
    </script>
@endpush
