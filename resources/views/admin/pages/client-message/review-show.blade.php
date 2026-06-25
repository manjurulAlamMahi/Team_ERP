@extends('admin.master')

@section('title', 'Review Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-customer-service-2-line"></i> Submission
                </h5>
                @include('admin.pages.client-message.partials._submission')

                @if ($clientMessage->status === 'pending')
                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="approveMessage({{ $clientMessage->id }})">
                            <i class="ri-check-line"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="ri-close-line"></i> Reject
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-information-line"></i> Type Requirements
                </h5>
                @include('admin.pages.client-message.partials._spec', ['type' => $clientMessage->type])
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('client.message.review.list') }}" class="btn btn-light">
            <i class="ri-arrow-left-line"></i> Back to Pending Review
        </a>
    </div>

    @if ($clientMessage->status === 'pending')
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="rejectForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Message</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="{{ $clientMessage->id }}">
                            <div class="mb-3">
                                <label class="form-label">Reason for rejection</label>
                                <textarea name="reason" class="form-control" rows="4" required></textarea>
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
        function approveMessage(id) {
            $.ajax({
                url: "{{ route('client.message.approve') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong',
                    });
                }
            });
        }

        $(document).on('submit', '#rejectForm', function(e) {
            e.preventDefault();
            const $form = $(this);

            $.ajax({
                url: "{{ route('client.message.reject') }}",
                type: 'POST',
                data: $form.serialize() + '&_token={{ csrf_token() }}',
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                        : 'Unable to reject message.';
                    Toast.fire({
                        icon: 'error',
                        title: message,
                    });
                }
            });
        });
    </script>
@endpush
