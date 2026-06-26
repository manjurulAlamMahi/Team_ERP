@extends('admin.master')

@section('title', 'Completed Issues')
@section('quickAccessicon', 'ri-alert-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-alert-line"></i> Completed Issues
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Profile</th>
                            <th>Issue</th>
                            <th>Type</th>
                            <th>Responsible</th>
                            <th>Completed By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $authUser = Auth::user(); @endphp
                        @foreach ($issues as $issue)
                            <tr>
                                <td>{{ $issue->issue_date->format('Y-m-d') }}</td>
                                <td>{{ $issue->client_name }}</td>
                                <td>{{ $issue->profile_name }}</td>
                                <td>{{ $issue->issue }}</td>
                                <td>
                                    @php
                                        $typeColor = match ($issue->type) {
                                            'Critical' => 'danger',
                                            'Urgent' => 'warning',
                                            'High' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $typeColor }}">{{ $issue->type }}</span>
                                </td>
                                <td>{{ $issue->responsibles->pluck('name')->join(', ') }}</td>
                                <td>
                                    {{ $issue->completer->name ?? 'N/A' }}
                                    <div class="text-muted small">{{ $issue->completed_at?->format('Y-m-d H:i') }}</div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);"
                                        onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})"
                                        class="text-reset fs-16 px-1" title="Comments">
                                        <i class="ri-chat-3-line"></i>
                                    </a>
                                    @if ($issue->isReversibleBy($authUser))
                                        <a href="javascript:void(0);" onclick="reverseIssue({{ $issue->id }})"
                                            class="text-reset fs-16 px-1" title="Reverse to Not Completed">
                                            <i class="ri-arrow-go-back-line"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.pages.daily-issue.partials._comments-modal')
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        function reverseIssue(id) {
            Swal.fire({
                title: 'Reverse to Not Completed',
                input: 'textarea',
                inputPlaceholder: 'Explain why this is not actually completed...',
                showCancelButton: true,
                confirmButtonText: 'Reverse',
                inputValidator: function(value) {
                    if (!value) {
                        return 'A comment is required to reverse this issue.';
                    }
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.reverse') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            comment: result.value,
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
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON && xhr.responseJSON.errors ?
                                Object.values(xhr.responseJSON.errors).flat().join(' ') :
                                'Unable to reverse issue.';
                            Toast.fire({
                                icon: 'error',
                                title: message
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
