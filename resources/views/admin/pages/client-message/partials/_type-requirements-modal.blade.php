@php
    $defaultAlertMessage = 'Please review the Format, Restriction and Mandatory requirements before submitting this message.';
    $typeSpecs = $types->keyBy('id')->map(fn ($t) => [
        'name' => $t->name,
        'alertMessage' => $t->alert_message ?: $defaultAlertMessage,
        'format' => $t->format ?: '<span class="text-muted">No format guidance provided.</span>',
        'restriction' => $t->restriction ?: '<span class="text-muted">No restrictions provided.</span>',
        'mandatory' => $t->mandatory ?: '<span class="text-muted">Nothing mandatory specified.</span>',
    ]);
@endphp

<div id="typeRequirementsAlert" class="alert alert-info d-none mb-3">
    <div class="row align-items-center g-2">
        <div class="col-md-6">
            <i class="ri-information-line me-1"></i>
            <span id="typeRequirementsAlertMessage"></span>
        </div>
        <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-sm btn-soft-success" onclick="openTypeRequirementsModal('format')">
                <i class="ri-shape-line me-1"></i> Format
            </button>
        </div>
        <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-sm btn-soft-danger" onclick="openTypeRequirementsModal('restriction')">
                <i class="ri-forbid-line me-1"></i> Restriction
            </button>
        </div>
        <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-sm btn-soft-primary" onclick="openTypeRequirementsModal('mandatory')">
                <i class="ri-checkbox-circle-line me-1"></i> Mandatory
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="typeRequirementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" id="typeRequirementsModalHeader">
                <h5 class="modal-title" id="typeRequirementsModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-14" id="typeRequirementsModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.TYPE_SPECS = @json($typeSpecs);

    const TYPE_REQUIREMENT_META = {
        format: { title: 'Format Requirements', label: 'Format — How this message should be written', headerClass: 'bg-success text-white' },
        restriction: { title: 'Restriction Requirements', label: 'Restriction — Things that must NOT be included', headerClass: 'bg-danger text-white' },
        mandatory: { title: 'Mandatory Requirements', label: 'Mandatory — Must be present before approval', headerClass: 'bg-primary text-white' },
    };

    function updateTypeRequirementsAlert() {
        const select = document.getElementById('typeSelect');
        const alertBox = document.getElementById('typeRequirementsAlert');
        if (!select || !alertBox) return;

        const spec = select.value && window.TYPE_SPECS[select.value] ? window.TYPE_SPECS[select.value] : null;
        alertBox.classList.toggle('d-none', !spec);
        document.getElementById('typeRequirementsAlertMessage').textContent = spec ? spec.alertMessage : '';
    }

    function openTypeRequirementsModal(field) {
        const select = document.getElementById('typeSelect');
        const typeId = select ? select.value : null;
        const spec = typeId && window.TYPE_SPECS[typeId] ? window.TYPE_SPECS[typeId] : null;
        const meta = TYPE_REQUIREMENT_META[field];

        document.getElementById('typeRequirementsModalTitle').textContent = meta.title;
        document.getElementById('typeRequirementsModalBody').innerHTML = spec ? spec[field] : 'No message type selected.';
        document.getElementById('typeRequirementsModalHeader').className = 'modal-header ' + meta.headerClass;

        new bootstrap.Modal(document.getElementById('typeRequirementsModal')).show();
    }
</script>
