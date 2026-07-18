@extends('admin.master')

@section('title', 'Review Client Message')
@section('quickAccessicon', 'ri-customer-service-2-line')

@section('content')

<script>
const TYPE_REQUIREMENTS = {
    format: @json($clientMessage->type?->format ?: '<span class="text-muted">No format guidance provided.</span>'),
    restriction: @json($clientMessage->type?->restriction ?: '<span class="text-muted">No restrictions provided.</span>'),
    mandatory: @json($clientMessage->type?->mandatory ?: '<span class="text-muted">Nothing mandatory specified.</span>'),
};
</script>

@if ($clientMessage->status === 'rejected' && $clientMessage->rejection_reason)
    <div class="alert alert-danger d-flex align-items-start mb-3">
        <i class="ri-error-warning-line fs-3 me-2"></i>
        <div>
            <strong class="d-block">You rejected this message</strong>
            {{ $clientMessage->rejection_reason }}
        </div>
    </div>
@endif

<div class="row g-3">
    {{-- Col-3: Message Type, Client Info, Client's Last Message, Attachment --}}
    <div class="col-lg-3">
        <div class="card border mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($clientMessage->type?->icon)
                        <img src="{{ asset($clientMessage->type->icon) }}" alt="" style="height:36px;width:36px;object-fit:contain;">
                    @else
                        <i class="ri-file-list-3-line fs-1 text-primary"></i>
                    @endif
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Message Type</div>
                        <div class="fw-medium">{{ $clientMessage->type->name ?? 'N/A' }}</div>
                    </div>
                </div>
                <span class="badge {{ $clientMessage->status === 'pending' ? 'bg-warning-subtle text-warning' : ($clientMessage->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') }} rounded-pill mb-2">
                    {{ ucfirst($clientMessage->status) }}
                </span>
                <div class="text-muted fs-12">
                    Submitted by <strong>{{ $clientMessage->submitter->name ?? 'N/A' }}</strong>
                    ({{ $clientMessage->submitter->employee_id ?? 'N/A' }})
                    {{ $clientMessage->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <div class="card border mb-3">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2"><i class="ri-user-search-line me-1"></i> Client Info</div>
                <div class="mb-2">
                    <div class="text-muted fs-11">Client Name</div>
                    <div class="fw-medium">{{ $clientMessage->client_name }}</div>
                </div>
                <div>
                    <div class="text-muted fs-11">Profile Name</div>
                    <div class="fw-medium">{{ $clientMessage->profile_name }}</div>
                </div>
            </div>
        </div>

        <div class="card border mb-3">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2">
                    <i class="ri-image-2-line me-1"></i> Client's Last Message
                    <span class="badge bg-light text-dark border ms-1">{{ ucfirst($clientMessage->last_message_type ?? 'none') }}</span>
                </div>
                @php $lastFiles = $clientMessage->attachments->where('type', 'last_message'); @endphp
                @forelse ($lastFiles as $file)
                    <a href="javascript:void(0)" class="preview-trigger d-inline-block me-2 mb-1"
                        data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                        <img src="{{ asset($file->path) }}" alt="{{ $file->original_name }}" class="rounded border" style="max-height:90px;">
                    </a>
                @empty
                    <span class="text-muted fs-12">No screenshot attached.</span>
                @endforelse
            </div>
        </div>

        <div class="card border mb-0">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2"><i class="ri-attachment-2 me-1"></i> Attachment</div>
                @php $outgoingFiles = $clientMessage->attachments->where('type', 'attachment'); @endphp
                @forelse ($outgoingFiles as $file)
                    <a href="javascript:void(0)" class="preview-trigger d-block fs-13"
                        data-url="{{ asset($file->path) }}" data-name="{{ $file->original_name }}">
                        <i class="ri-file-line me-1"></i> {{ $file->original_name }}
                    </a>
                @empty
                    <span class="text-muted fs-12">No attachment.</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Col-9: Employee message + Check Requirements + Approve/Reject --}}
    <div class="col-lg-9">
        <div class="card border mb-3">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2"><i class="ri-message-3-line me-1"></i> Employee Message</div>
                <div class="p-3 bg-light-subtle border-start border-primary border-4 rounded fs-5" style="white-space:pre-wrap;">{{ $clientMessage->their_message }}</div>
            </div>
        </div>

        <div class="card border mb-3">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2"><i class="ri-shield-check-line me-1"></i> Check Requirements</div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-soft-success btn-sm" onclick="openCheckModal('format')">
                        <i class="ri-shape-line me-1"></i> Check Format
                    </button>
                    <button type="button" class="btn btn-soft-danger btn-sm" onclick="openCheckModal('restriction')">
                        <i class="ri-forbid-line me-1"></i> Check Restriction
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm" onclick="openCheckModal('mandatory')">
                        <i class="ri-checkbox-circle-line me-1"></i> Check Mandatory
                    </button>
                </div>
            </div>
        </div>

        @if ($clientMessage->status === 'pending')
            <div class="card border mb-0">
                <div class="card-body d-flex gap-2">
                    <button type="button" class="btn btn-success" onclick="approveMessage({{ $clientMessage->id }})">
                        <i class="ri-check-line me-1"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="ri-close-line me-1"></i> Reject
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('client.message.review.list') }}" class="btn btn-light">
        <i class="ri-arrow-left-line"></i> Back to Pending Review
    </a>
</div>

{{-- Check Modal --}}
<div class="modal fade" id="checkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" id="checkModalHeader">
                <h5 class="modal-title" id="checkModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-14" id="checkModalContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
                        <div class="row g-0" style="min-height:340px;">
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
                                    <div class="p-3 border rounded bg-white fs-14" style="white-space:pre-wrap;max-height:260px;overflow-y:auto;">{{ $clientMessage->their_message }}</div>
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
                                    This message will be sent as a notification to the member and shown on their message list.
                                </p>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="9"
                                        placeholder="e.g. The message format is incorrect. Please follow the required structure and resubmit."
                                        required maxlength="1000"></textarea>
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

@include('admin.pages.client-message.partials._file-preview-modal')
@endsection

@push('script')
    <script>
        function openCheckModal(type) {
            var titles = {
                format: 'Format Requirements',
                restriction: 'Restriction Requirements',
                mandatory: 'Mandatory Requirements'
            };
            var headerClasses = {
                format: 'bg-success text-white',
                restriction: 'bg-danger text-white',
                mandatory: 'bg-primary text-white'
            };

            document.getElementById('checkModalTitle').textContent = titles[type] || type;
            document.getElementById('checkModalContent').innerHTML = TYPE_REQUIREMENTS[type] || 'No information provided.';

            var header = document.getElementById('checkModalHeader');
            header.className = 'modal-header ' + (headerClasses[type] || '');

            new bootstrap.Modal(document.getElementById('checkModal')).show();
        }

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
