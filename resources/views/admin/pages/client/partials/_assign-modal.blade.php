<div class="modal fade" id="clientAssignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Client <span class="badge bg-warning text-dark">Beta</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clientAssignForm">
                <div class="modal-body">
                    <input type="hidden" name="client_id" id="assignClientId">
                    <div id="assignMembersList">
                        <p class="text-muted mb-0">Loading...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAssignModal(clientId) {
        document.getElementById('assignClientId').value = clientId;
        document.getElementById('assignMembersList').innerHTML = '<p class="text-muted mb-0">Loading...</p>';

        new bootstrap.Modal(document.getElementById('clientAssignModal')).show();

        var url = "{{ route('client.assignees', ['id' => '__ID__']) }}".replace('__ID__', clientId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                renderAssignMembers(response.data.members || [], response.data.assigned_ids || []);
            },
            error: function() {
                document.getElementById('assignMembersList').innerHTML =
                    '<p class="text-danger mb-0">Unable to load team members.</p>';
            }
        });
    }

    function renderAssignMembers(members, assignedIds) {
        var container = document.getElementById('assignMembersList');

        if (!members.length) {
            container.innerHTML = '<p class="text-muted mb-0">No team members found.</p>';
            return;
        }

        container.innerHTML = '<div class="row">' + members.map(function(member) {
            var checked = assignedIds.indexOf(member.id) !== -1 ? 'checked' : '';
            return '<div class="col-md-6">' +
                '<div class="form-check">' +
                '<input type="checkbox" class="form-check-input" name="assignee_ids[]" value="' + member.id + '" ' +
                'id="assignee-' + member.id + '" ' + checked + '>' +
                '<label class="form-check-label" for="assignee-' + member.id + '">' + $('<div>').text(member
                    .name).html() + '</label>' +
                '</div>' +
                '</div>';
        }).join('') + '</div>';
    }

    $(document).on('submit', '#clientAssignForm', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('client.assign.store') }}",
            type: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.status) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    bootstrap.Modal.getInstance(document.getElementById('clientAssignModal')).hide();
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.message
                    });
                }
            },
            error: function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.errors ?
                    Object.values(xhr.responseJSON.errors).flat().join(' ') :
                    'Unable to update assignment.';
                Toast.fire({
                    icon: 'error',
                    title: message
                });
            }
        });
    });
</script>
