<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignTaskForm">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Team Member</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select Member</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client Name</label>
                        <input type="text" name="client_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile</label>
                        <input type="text" name="profile_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="details" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
