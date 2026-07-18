@extends('admin.master')

@section('title', 'Fiverr Profiles')
@section('quickAccessicon', 'ri-user-star-line')

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
                    <span><i class="ri-user-star-line"></i> Fiverr Profiles</span>
                    @can('fiverr_profile_create')
                        <a href="{{ route('fiverr.profile.create') }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> Add New
                        </a>
                    @endcan
                </h5>
                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Clients</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($profiles as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->clients_count }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input"
                                            onchange="updateStatus({{ $item->id }})"
                                            @if ($item->status == 'active') checked @endif>
                                        <label class="form-check-label">
                                            @if ($item->status == 'active')
                                                <span class="badge bg-success-subtle text-success rounded-pill">{{ $item->status }}</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $item->status }}</span>
                                            @endif
                                        </label>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @can('fiverr_profile_edit')
                                            <a href="{{ route('fiverr.profile.edit', $item->id) }}" class="btn btn-sm btn-soft-secondary" title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                        @endcan
                                        @can('fiverr_profile_delete')
                                            <a href="javascript: void(0);" onclick="deleteProfile({{ $item->id }})"
                                                class="btn btn-sm btn-soft-danger" title="Delete">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </a>
                                        @endcan
                                    </div>
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
                url: "{{ route('fiverr.profile.status') }}",
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
        async function deleteProfile(id) {
            const {
                value: password
            } = await Swal.fire({
                icon: 'info',
                title: "Are you sure you want to delete this profile?",
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
                    url: "{{ route('fiverr.profile.destroy') }}",
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
                                title: xhr.responseJSON?.message || 'Something Went Wrong',
                            });
                        }
                    }
                });
            }
        }
    </script>
@endpush
