@extends('admin.master')

@section('title', 'Announcements')
@section('quickAccessicon', 'ri-megaphone-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-megaphone-line"></i> Announcements</span>
                    @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                        <a href="{{ route('announcement.create') }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> New Announcement
                        </a>
                    @endif
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Posted By</th>
                            <th>Ends On</th>
                            <th>Status</th>
                            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($announcements as $key => $announcement)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $announcement->title }}</td>
                                <td><span class="badge {{ $announcement->priorityBadgeClass() }}">{{ ucfirst($announcement->priority) }}</span></td>
                                <td>{{ $announcement->creator->name ?? '-' }}</td>
                                <td>{{ $announcement->ends_at->format('d M Y') }}</td>
                                <td>
                                    @if ($announcement->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Expired</span>
                                    @endif
                                </td>
                                @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                                    <td>
                                        <a href="{{ route('announcement.edit', $announcement->id) }}" class="text-reset fs-16 px-1">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteAnnouncement({{ $announcement->id }})"
                                            class="text-reset fs-16 px-1">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        async function deleteAnnouncement(id) {
            const result = await Swal.fire({
                icon: 'warning',
                title: "Are you sure you want to delete this announcement?",
                confirmButtonText: "Yes, delete it",
                showCancelButton: true,
                cancelButtonText: "No"
            });

            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('id', id);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('announcement.destroy') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Something Went Wrong',
                        });
                    }
                });
            }
        }
    </script>
@endpush
