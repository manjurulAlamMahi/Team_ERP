@extends('admin.master')

@section('title', 'Client Message Types')
@section('quickAccessicon', 'ri-file-list-3-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2 d-flex justify-content-between align-items-center">
                    <span><i class="ri-file-list-3-line"></i> Client Message Types</span>
                    @can('client_message_type_create')
                        <a href="{{ route('client.message.type.create') }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> Add New
                        </a>
                    @endcan
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Format</th>
                            <th>Restriction</th>
                            <th>Mandatory</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($types as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($item->icon)
                                        <img src="{{ asset($item->icon) }}" alt="" style="max-height: 32px;">
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->format), 40) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->restriction), 40) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->mandatory), 40) }}</td>
                                <td>{{ $item->messages_count }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input"
                                            onchange="updateStatus({{ $item->id }})"
                                            @if ($item->status == 'active') checked @endif>
                                        <label class="form-check-label">
                                            @if ($item->status == 'active')
                                                <span class="badge bg-success">{{ $item->status }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                            @endif
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    @can('client_message_type_edit')
                                        <a href="{{ route('client.message.type.edit', $item->id) }}" class="text-reset fs-16 px-1">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                    @endcan
                                    @can('client_message_type_delete')
                                        <a href="javascript: void(0);" onclick="deleteType({{ $item->id }})"
                                            class="text-reset fs-16 px-1">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                    @endcan
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
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        function updateStatus(id) {
            $.ajax({
                url: "{{ route('client.message.type.status') }}",
                type: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
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
                        title: 'Something Went Wrong',
                    });
                }
            });
        }
    </script>

    <script>
        async function deleteType(id) {
            const {
                value: password
            } = await Swal.fire({
                icon: 'info',
                title: "Are you sure you want to delete this message type?",
                input: "password",
                inputLabel: "Enter your password",
                inputPlaceholder: "Enter your password",
                inputAttributes: {
                    maxlength: "100",
                    autocapitalize: "off",
                    autocorrect: "off"
                },
                confirmButtonText: "Yes",
                showCancelButton: true,
                cancelButtonText: "No"
            });

            if (password) {
                let formData = new FormData();
                formData.append('id', id);
                formData.append('password', password);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('client.message.type.destroy') }}",
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
                            }, 2000);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Incorrect Password',
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Something Went Wrong',
                            });
                        }
                    }
                });
            }
        }
    </script>
@endpush
