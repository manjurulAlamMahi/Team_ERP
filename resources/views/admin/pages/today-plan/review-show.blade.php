@extends('admin.master')

@section('title', 'Review Plan')
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-calendar-todo-line"></i> Plan from {{ $task->user->name ?? 'N/A' }}
                </h5>

                <table class="table table-borderless mb-3">
                    <tr>
                        <th width="150">Client Name</th>
                        <td>{{ $task->client_name }}</td>
                    </tr>
                    <tr>
                        <th>Profile</th>
                        <td>{{ $task->profile_name }}</td>
                    </tr>
                    <tr>
                        <th>Details</th>
                        <td>{{ $task->details }}</td>
                    </tr>
                    <tr>
                        <th>Submitted</th>
                        <td>{{ $task->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span
                                class="badge bg-{{ $task->status === 'approved' ? 'success' : ($task->status === 'rejected' ? 'danger' : 'warning') }} text-uppercase">{{ $task->status }}</span>
                        </td>
                    </tr>
                    @if ($task->review_comment)
                        <tr>
                            <th>Leader Comment</th>
                            <td>{{ $task->review_comment }}</td>
                        </tr>
                    @endif
                </table>

                @if ($task->status === 'pending')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#approveModal">
                            <i class="ri-check-line"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ri-close-line"></i> Reject
                        </button>
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('today.plan.review.list') }}" class="btn btn-light">
                        <i class="ri-arrow-left-line"></i> Back to Pending Review
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($task->status === 'pending')
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="approveForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Approve Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="{{ $task->id }}">
                            <div class="mb-3">
                                <label class="form-label">Comment (optional)</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="rejectForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="{{ $task->id }}">
                            <div class="mb-3">
                                <label class="form-label">Comment (optional)</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        $(document).on('submit', '#approveForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('today.plan.approve') }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong'
                    });
                }
            });
        });

        $(document).on('submit', '#rejectForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('today.plan.reject') }}",
                type: 'POST',
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong'
                    });
                }
            });
        });
    </script>
@endpush
