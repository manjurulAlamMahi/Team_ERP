@if ($type)
    <p class="text-muted small mb-3">
        Requirements for a <strong>{{ $type->name }}</strong> message. All three sections are expanded by default —
        review them carefully before approving or rejecting.
    </p>

    <div class="accordion" id="specAccordion">

        <div class="accordion-item border-primary mb-2">
            <h2 class="accordion-header" id="headingFormat">
                <button class="accordion-button fw-medium" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseFormat"
                    aria-expanded="true" aria-controls="collapseFormat">
                    <i class="ri-shape-line text-primary me-2"></i> Format
                    <small class="ms-2 text-muted fw-normal">How this message should be written or structured</small>
                </button>
            </h2>
            <div id="collapseFormat" class="accordion-collapse collapse show" aria-labelledby="headingFormat" data-bs-parent="">
                <div class="accordion-body pt-2 fs-14">
                    {!! $type->format ?: '<span class="text-muted">No format guidance provided.</span>' !!}
                </div>
            </div>
        </div>

        <div class="accordion-item border-danger mb-2">
            <h2 class="accordion-header" id="headingRestriction">
                <button class="accordion-button fw-medium" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseRestriction"
                    aria-expanded="true" aria-controls="collapseRestriction">
                    <i class="ri-forbid-line text-danger me-2"></i> Restriction
                    <small class="ms-2 text-muted fw-normal">Things that must NOT be included</small>
                </button>
            </h2>
            <div id="collapseRestriction" class="accordion-collapse collapse show" aria-labelledby="headingRestriction" data-bs-parent="">
                <div class="accordion-body pt-2 fs-14">
                    {!! $type->restriction ?: '<span class="text-muted">No restrictions provided.</span>' !!}
                </div>
            </div>
        </div>

        <div class="accordion-item border-warning mb-2">
            <h2 class="accordion-header" id="headingMandatory">
                <button class="accordion-button fw-medium" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseMandatory"
                    aria-expanded="true" aria-controls="collapseMandatory">
                    <i class="ri-checkbox-circle-line text-warning me-2"></i> Mandatory
                    <small class="ms-2 text-muted fw-normal">Must be present before approval</small>
                </button>
            </h2>
            <div id="collapseMandatory" class="accordion-collapse collapse show" aria-labelledby="headingMandatory" data-bs-parent="">
                <div class="accordion-body pt-2 fs-14">
                    {!! $type->mandatory ?: '<span class="text-muted">Nothing mandatory specified.</span>' !!}
                </div>
            </div>
        </div>

    </div>
@endif
