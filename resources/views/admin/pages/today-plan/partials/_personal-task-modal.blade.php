<div class="modal fade" id="personalTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="personalTaskForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add Personal Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.partials._client-select-field', [
                        'clients' => $clients,
                        'fieldId' => 'personalTaskClient',
                        'autoInit' => false,
                    ])

                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="details" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
