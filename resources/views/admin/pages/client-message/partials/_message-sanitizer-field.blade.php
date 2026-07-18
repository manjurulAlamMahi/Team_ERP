@php
    $clientMessage = $clientMessage ?? null;
    $existingMessage = old('their_message', $clientMessage->their_message ?? '');
@endphp

<div class="card border mb-3">
    <div class="card-body">
        <h6 class="text-uppercase bg-light p-2 mb-3"><i class="ri-shield-check-line"></i> Message Sanitizer</h6>
        <p class="text-muted small mb-3">
            Write your message on the left — it's sanitized automatically as you type or paste, stripping
            Fiverr-restricted words (contact info, payment terms, etc.). Only the sanitized version on the right is sent for approval.
        </p>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>My Message</span>
                </label>
                <textarea id="their_message_raw" class="form-control sanitizer-pane" rows="10"
                    placeholder="Paste or type text here…">{{ $existingMessage }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label d-flex justify-content-between align-items-center">
                    <span>Sanitized Message</span>
                </label>
                <textarea id="their_message" name="their_message" readonly
                    class="form-control sanitizer-pane sanitizer-pane-output @error('their_message') is-invalid @enderror"
                    rows="10" placeholder="Sanitized output will appear here…">{{ $existingMessage }}</textarea>
                @error('their_message')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="text-muted small mt-2">
            <span id="sanitizerWordCount">0</span> Words &nbsp;|&nbsp;
            <span id="sanitizerCharCount">0</span> Characters &nbsp;|&nbsp;
            <span id="sanitizerFlaggedCount" class="fw-semibold text-danger">0</span> Flagged
        </div>
    </div>
</div>

<style>
    .sanitizer-pane {
        font-family: 'Courier New', Consolas, monospace;
        font-size: 13.5px;
        background: #fbfcfe;
    }
    .sanitizer-pane-output {
        background: #f8f9fc;
    }
</style>

<script src="{{ asset('admin') }}/assets/js/sanitizer/rules.js"></script>
<script src="{{ asset('admin') }}/assets/js/sanitizer/sanitize.js"></script>
<script>
    function runFiverrSanitize() {
        var raw = document.getElementById('their_message_raw').value;
        var result = window.sanitizeFiverrText(raw);
        document.getElementById('their_message').value = result.output;
        document.getElementById('sanitizerWordCount').textContent = window.countFiverrWords(raw);
        document.getElementById('sanitizerCharCount').textContent = window.countFiverrChars(raw);
        document.getElementById('sanitizerFlaggedCount').textContent = result.matches.length;
        return result;
    }

    // Auto-sanitize as the member types or pastes - no button needed.
    var sanitizeDebounceTimer = null;
    document.getElementById('their_message_raw').addEventListener('input', function () {
        clearTimeout(sanitizeDebounceTimer);
        sanitizeDebounceTimer = setTimeout(runFiverrSanitize, 150);
    });

    if (document.getElementById('their_message_raw').value) {
        runFiverrSanitize();
    }
</script>
