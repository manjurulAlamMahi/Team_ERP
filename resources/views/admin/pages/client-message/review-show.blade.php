@extends('admin.master')

@section('title', 'Review Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')
    @include('admin.pages.client-message.partials._submission-header')

    <div class="row">
        <div class="col-lg-6">
            @include('admin.pages.client-message.partials._submission-info')
            @include('admin.pages.client-message.partials._submission-message')

            @if ($clientMessage->status === 'pending')
                <div class="mb-3 d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="approveMessage({{ $clientMessage->id }})">
                        <i class="ri-check-line"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="ri-close-line"></i> Reject
                    </button>
                </div>
            @endif
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

    @include('admin.pages.client-message.partials._file-preview-modal')

    <div class="mt-3">
        <a href="{{ route('client.message.review.list') }}" class="btn btn-light">
            <i class="ri-arrow-left-line"></i> Back to Pending Review
        </a>
    </div>

    @if ($clientMessage->status === 'pending')
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="rejectForm">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="ri-close-circle-line me-1"></i> Reject Message</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <input type="hidden" name="id" value="{{ $clientMessage->id }}">
                            <div class="row g-0" style="min-height: 340px;">
                                {{-- Left: user's submitted message --}}
                                <div class="col-md-6 border-end p-4 bg-light-subtle">
                                    <h6 class="text-uppercase text-muted fw-bold mb-3 fs-12">
                                        <i class="ri-message-3-line me-1"></i> Employee's Message
                                    </h6>
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">Client / Profile</div>
                                        <strong>{{ $clientMessage->client_name }}</strong>
                                        <span class="text-muted mx-1">/</span>
                                        {{ $clientMessage->profile_name }}
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">Message</div>
                                        <div class="p-3 border rounded bg-white fs-14" style="white-space: pre-wrap; max-height: 260px; overflow-y: auto;">{{ strip_tags($clientMessage->their_message) }}</div>
                                    </div>
                                    @php $lastFiles = $clientMessage->attachments->where('type', 'last_message'); @endphp
                                    @if ($lastFiles->isNotEmpty())
                                        <div>
                                            <div class="text-muted small mb-1">Last Message Screenshot(s)</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($lastFiles as $f)
                                                    <a href="javascript:void(0)" class="preview-trigger"
                                                        data-url="{{ asset($f->path) }}" data-name="{{ $f->original_name }}">
                                                        <img src="{{ asset($f->path) }}" alt="{{ $f->original_name }}"
                                                            class="rounded border" style="max-height:80px;">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Right: rejection reason --}}
                                <div class="col-md-6 p-4">
                                    <h6 class="text-uppercase text-muted fw-bold mb-3 fs-12">
                                        <i class="ri-feedback-line me-1"></i> Rejection Reason
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Explain clearly what the member needs to fix before resubmitting.
                                        This message will be sent as a notification to the member.
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" class="form-control" rows="9"
                                            placeholder="e.g. The message format is incorrect. Please follow the required structure and resubmit."
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="ri-close-line me-1"></i> Confirm Rejection</button>
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
