@php
    $clientMessage = $clientMessage ?? null;
    $lastMessageType = old('last_message_type', $clientMessage->last_message_type ?? 'none');
    $existingLastMessageFiles = $clientMessage?->attachments->where('type', 'last_message') ?? collect();
    $existingAttachmentFiles = $clientMessage?->attachments->where('type', 'attachment') ?? collect();
@endphp

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-user-search-line"></i> Client Info</h6>

        <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="form-label">Client Name</label>
                <input type="text" class="form-control @error('client_name') is-invalid @enderror"
                    name="client_name" value="{{ old('client_name', $clientMessage->client_name ?? '') }}">
                @error('client_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Profile Name</label>
                <input type="text" class="form-control @error('profile_name') is-invalid @enderror"
                    name="profile_name" value="{{ old('profile_name', $clientMessage->profile_name ?? '') }}">
                @error('profile_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Client's Last Message</label>
                <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small">Screenshot type:</span>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="last_message_type" id="lastMessageTypeNone"
                            value="none" {{ $lastMessageType === 'none' || !$lastMessageType ? 'checked' : '' }} onchange="toggleLastMessageMode()">
                        <label class="btn btn-outline-secondary" for="lastMessageTypeNone">
                            <i class="ri-close-line me-1"></i> None
                        </label>
                        <input type="radio" class="btn-check" name="last_message_type" id="lastMessageTypeImage"
                            value="image" {{ $lastMessageType === 'image' ? 'checked' : '' }} onchange="toggleLastMessageMode()">
                        <label class="btn btn-outline-secondary" for="lastMessageTypeImage">
                            <i class="ri-image-line me-1"></i> Image
                        </label>
                        <input type="radio" class="btn-check" name="last_message_type" id="lastMessageTypeMultiple"
                            value="multiple" {{ $lastMessageType === 'multiple' ? 'checked' : '' }} onchange="toggleLastMessageMode()">
                        <label class="btn btn-outline-secondary" for="lastMessageTypeMultiple">
                            <i class="ri-gallery-line me-1"></i> Multiple
                        </label>
                    </div>
                </div>

                <div id="lastMessageSingleWrapper">
                    <input type="file" id="lastMessageFilesInput" name="last_message_files[]"
                        class="dropify @error('last_message_files') is-invalid @enderror" accept="image/*"
                        @if ($existingLastMessageFiles->isNotEmpty()) data-default-file="{{ asset($existingLastMessageFiles->first()->path) }}" @endif>
                </div>

                <div id="lastMessageMultiWrapper" style="display: none;">
                    <input type="file" id="lastMessageFilesInputMulti" name="last_message_files[]" multiple accept="image/*"
                        class="form-control @error('last_message_files') is-invalid @enderror">
                    <div id="lastMessageMultiPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>

                @error('last_message_files')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror

                @if ($existingLastMessageFiles->isNotEmpty())
                    <div class="mt-2">
                        <div class="small text-muted mb-1">Existing (leave empty to keep these):</div>
                        @foreach ($existingLastMessageFiles as $file)
                            <a href="javascript:void(0)" class="preview-trigger d-inline-block me-2 mb-2"
                                data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                                <img src="{{ asset($file->path) }}" alt="{{ $file->original_name }}" class="rounded border" style="max-height: 70px;">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-message-3-line"></i> Client Message</h6>

        <div class="mb-3">
            <label class="form-label">Your Message</label>
            <textarea id="their_message" name="their_message" class="form-control @error('their_message') is-invalid @enderror"
                rows="5">{{ old('their_message', $clientMessage->their_message ?? '') }}</textarea>
            @error('their_message')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="form-label">Attachment</label>
            <input type="file" id="attachmentFilesInput" name="attachment_files[]" multiple
                class="form-control @error('attachment_files') is-invalid @enderror">
            <div id="attachmentFilesPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
            @error('attachment_files')
                <div class="text-danger small">{{ $message }}</div>
            @enderror

            @if ($existingAttachmentFiles->isNotEmpty())
                <div class="mt-2">
                    <div class="small text-muted mb-1">Existing (leave empty to keep these):</div>
                    @foreach ($existingAttachmentFiles as $file)
                        <a href="javascript:void(0)" class="preview-trigger d-block"
                            data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                            <i class="ri-file-line"></i> {{ $file->original_name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@include('admin.pages.client-message.partials._file-preview-modal')

<script>
    function toggleLastMessageMode() {
        const checked = document.querySelector('input[name="last_message_type"]:checked');
        const single = document.getElementById('lastMessageSingleWrapper');
        const multi = document.getElementById('lastMessageMultiWrapper');
        const val = checked ? checked.value : 'none';
        if (val === 'multiple') {
            single.style.display = 'none';
            multi.style.display = 'block';
        } else if (val === 'image') {
            single.style.display = 'block';
            multi.style.display = 'none';
        } else {
            single.style.display = 'none';
            multi.style.display = 'none';
        }
    }
    toggleLastMessageMode();

    function bindSelectedFilesPreview(inputId, listId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        if (!input || !list) {
            return;
        }
        input.addEventListener('change', function() {
            list.innerHTML = '';
            Array.from(input.files).forEach(function(file) {
                const item = document.createElement('div');
                item.className = 'border rounded p-1 text-center';
                item.style.width = '90px';

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.className = 'd-block mb-1';
                    img.style.maxWidth = '80px';
                    img.style.maxHeight = '80px';
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    item.appendChild(img);
                } else {
                    item.innerHTML = '<i class="ri-file-3-line fs-2"></i>';
                }

                const name = document.createElement('div');
                name.className = 'small text-truncate';
                name.style.maxWidth = '80px';
                name.title = file.name;
                name.textContent = file.name;
                item.appendChild(name);

                list.appendChild(item);
            });
        });
    }

    bindSelectedFilesPreview('lastMessageFilesInputMulti', 'lastMessageMultiPreview');
    bindSelectedFilesPreview('attachmentFilesInput', 'attachmentFilesPreview');
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
