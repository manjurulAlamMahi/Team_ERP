@extends('admin.master')

@section('title', 'My Client Messages')
@section('quickAccessicon', 'ri-customer-service-2-line')

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
                    <span><i class="ri-customer-service-2-line"></i> My Client Messages</span>
                    <a href="{{ route('client.message.create') }}" class="btn btn-sm btn-success">
                        <i class="ri-add-line"></i> Send Message
                    </a>
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Client Name</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $item)
                            @php
                                $statusColor = match ($item->status) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <tr>
                                <td>{{ $item->type->name ?? 'N/A' }}</td>
                                <td>{{ $item->client_name }}</td>
                                <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                <td><span class="badge bg-{{ $statusColor }} text-uppercase">{{ $item->status }}</span></td>
                                <td>
                                    <a href="{{ route('client.message.my.show', $item->id) }}" class="text-reset fs-16 px-1">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    @if ($item->status === 'pending')
                                        <a href="{{ route('client.message.edit', $item->id) }}" class="text-reset fs-16 px-1">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteMessage({{ $item->id }})"
                                            class="text-reset fs-16 px-1">
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
@endsection

@push('script')
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        function deleteMessage(id) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete this message?',
                text: 'This pending message draft will be permanently deleted.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('client.message.destroy') }}",
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
            });
        }
    </script>
@endpush
