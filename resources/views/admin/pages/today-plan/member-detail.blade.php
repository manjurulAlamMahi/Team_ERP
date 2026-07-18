@extends('admin.master')

@section('title', "Member's Today Plan")
@section('quickAccessicon', 'ri-calendar-todo-line')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-calendar-todo-line"></i> {{ $member->name }} - {{ today()->format('Y-m-d') }}</span>
                    <a href="{{ route('today.plan.dashboard') }}" class="btn btn-sm btn-light">
                        <i class="ri-arrow-left-line"></i> Back to Dashboard
                    </a>
                </h5>

                @if ($tasks->isEmpty())
                    <p class="text-muted mb-0">No tasks for today.</p>
                @else
                    <table class="table table-striped table-centered">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Status</th>
                                <th>Client</th>
                                <th>Profile</th>
                                <th>Details</th>
                                <th>Source</th>
                                <th>Verification</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                @include('admin.pages.today-plan.partials._task-row', ['task' => $task, 'canVerify' => true])
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="reopenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reopenForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Reopen Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="reopenTaskId">
                        <div class="mb-3">
                            <label class="form-label">What's missing? (optional)</label>
                            <textarea name="comment" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Reopen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function verifyComplete(id) {
            $.ajax({
                url: "{{ route('today.plan.verify') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
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
                }
            });
        }

        function reopenTaskPrompt(id) {
            document.getElementById('reopenTaskId').value = id;
            new bootstrap.Modal(document.getElementById('reopenModal')).show();
        }

        $(document).on('submit', '#reopenForm', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('today.plan.reopen') }}",
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
                }
            });
        });
    </script>
@endpush
