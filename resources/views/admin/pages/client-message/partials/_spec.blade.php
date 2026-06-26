@if ($type)
    <p class="text-muted small mb-3">
        Guidelines for a <strong>{{ $type->name }}</strong> message. Review all three before sending — the
        Leader/Co-Leader will check your submission against these when approving it.
    </p>

    <div class="alert alert-primary">
        <div class="d-flex align-items-center mb-1">
            <i class="ri-shape-line me-2 fs-5"></i>
            <strong>Format</strong>
        </div>
        <div class="small text-muted mb-2">How this message should be written or structured.</div>
        <div>{!! $type->format ?: 'No format guidance provided.' !!}</div>
    </div>

    <div class="alert alert-danger">
        <div class="d-flex align-items-center mb-1">
            <i class="ri-forbid-line me-2 fs-5"></i>
            <strong>Restriction</strong>
        </div>
        <div class="small text-muted mb-2">Things you must NOT say or include in this message.</div>
        <div>{!! $type->restriction ?: 'No restrictions provided.' !!}</div>
    </div>

    <div class="alert alert-warning">
        <div class="d-flex align-items-center mb-1">
            <i class="ri-checkbox-circle-line me-2 fs-5"></i>
            <strong>Mandatory</strong>
        </div>
        <div class="small text-muted mb-2">Things that MUST be included before this message can be approved.</div>
        <div>{!! $type->mandatory ?: 'Nothing mandatory specified.' !!}</div>
    </div>
@endif
