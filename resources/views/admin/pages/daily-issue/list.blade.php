@extends('admin.master')

@section('title', 'View Issues')
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
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-alert-line"></i> Issues</span>
                    @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']))
                        <a href="{{ route('daily.issue.create') }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> Add Issue
                        </a>
                    @endif
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width: 30px;">Done</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Profile</th>
                            <th>Issue</th>
                            <th>Type</th>
                            <th>Responsible</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $authUser = Auth::user(); @endphp
                        @foreach ($issues as $issue)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input"
                                        {{ $issue->isCompletableBy($authUser) ? '' : 'disabled' }}
                                        onclick="markComplete({{ $issue->id }})">
                                </td>
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
                                    {{ $issue->creator->name ?? 'N/A' }}
                                    @if ($issue->last_edited_by)
                                        <div class="text-muted small">Edited by {{ $issue->lastEditor->name ?? 'N/A' }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0);"
                                        onclick="openComments({{ $issue->id }}, {{ $issue->canCommentBy($authUser) ? 'true' : 'false' }})"
                                        class="text-reset fs-16 px-1" title="Comments">
                                        <i class="ri-chat-3-line"></i>
                                    </a>
                                    @if ($issue->isEditableBy($authUser))
                                        <a href="{{ route('daily.issue.edit', $issue->id) }}" class="text-reset fs-16 px-1"
                                            title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                    @endif
                                    @if ($issue->isDeletableBy($authUser))
                                        <a href="javascript:void(0);" onclick="deleteIssue({{ $issue->id }})"
                                            class="text-reset fs-16 px-1" title="Delete">
                                            <i class="ri-delete-bin-2-line"></i>
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
        function markComplete(id) {
            $.ajax({
                url: "{{ route('daily.issue.complete') }}",
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
                        location.reload();
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something Went Wrong'
                    });
                    location.reload();
                }
            });
        }

        function deleteIssue(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete this issue?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('daily.issue.destroy') }}",
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
            });
        }
    </script>
@endpush
