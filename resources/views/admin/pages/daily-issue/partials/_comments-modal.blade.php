<div class="modal fade" id="issueCommentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Issue Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCommentForm" class="mb-3 d-none">
                    <input type="hidden" name="id" id="commentIssueId">
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" rows="2" placeholder="Add a comment..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-sm btn-success">Post Comment</button>
                    </div>
                </form>
                <div id="commentsList">
                    <p class="text-muted mb-0">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(str) {
        return $('<div>').text(str === null || str === undefined ? '' : str).html();
    }

    function openComments(issueId, canComment) {
        document.getElementById('commentIssueId').value = issueId;
        document.getElementById('addCommentForm').classList.toggle('d-none', !canComment);
        document.getElementById('commentsList').innerHTML = '<p class="text-muted mb-0">Loading...</p>';

        new bootstrap.Modal(document.getElementById('issueCommentsModal')).show();

        const url = "{{ route('daily.issue.comments', ['id' => '__ID__']) }}".replace('__ID__', issueId);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                renderComments(response.data || []);
            },
            error: function() {
                document.getElementById('commentsList').innerHTML = '<p class="text-danger mb-0">Unable to load comments.</p>';
            }
        });
    }

    function renderComments(comments) {
        const container = document.getElementById('commentsList');

        if (!comments.length) {
            container.innerHTML = '<p class="text-muted mb-0">No comments yet.</p>';
            return;
        }

        container.innerHTML = comments.map(function(c) {
            const badge = c.type === 'reopen' ?
                '<span class="badge bg-danger me-1">Reopened</span>' :
                '';
            const author = c.user ? c.user.name : 'N/A';
            return '<div class="border-bottom pb-2 mb-2">' +
                badge +
                '<strong>' + escapeHtml(author) + '</strong>' +
                '<span class="text-muted small ms-1">' + escapeHtml(c.created_at) + '</span>' +
                '<div>' + escapeHtml(c.comment) + '</div>' +
                '</div>';
        }).join('');
    }

    $(document).on('submit', '#addCommentForm', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('daily.issue.comment.store') }}",
            type: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(response) {
                if (response.status) {
                    document.querySelector('#addCommentForm textarea').value = '';
                    openComments(document.getElementById('commentIssueId').value, true);
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.message
                    });
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.errors ?
                    Object.values(xhr.responseJSON.errors).flat().join(' ') :
                    'Unable to add comment.';
                Toast.fire({
                    icon: 'error',
                    title: message
                });
            }
        });
    });
</script>
