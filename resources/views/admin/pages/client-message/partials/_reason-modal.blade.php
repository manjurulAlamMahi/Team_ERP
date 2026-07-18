<div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ri-close-circle-line me-1"></i> Rejection Reason</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-14" id="reasonModalBody" style="white-space: pre-wrap;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.reason-trigger');
        if (!trigger) return;
        document.getElementById('reasonModalBody').textContent = trigger.dataset.reason || 'No reason provided.';
        new bootstrap.Modal(document.getElementById('reasonModal')).show();
    });
</script>
