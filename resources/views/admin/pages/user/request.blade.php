@extends('admin.master')

@section('title', 'User-request')
@section('quickAccessicon', 'ri-group-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('admin') }}/assets/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ asset('admin') }}/assets/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ asset('admin') }}/assets/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" /> --}}
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-group-line"></i> User's Request List
                </h5>
                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Added By</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->role }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->phone ?? 'N/A' }}</td>
                                <td>{{ $item->addBy->name }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('user.accept', $item->id) }}" class="btn btn-sm btn-soft-success" title="Accept">
                                            <i class="ri-check-line"></i>
                                        </a>
                                        <a href="javascript: void(0);" onclick="deleteAccount({{ $item->id }})"
                                            class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                        <a href="{{ route('user.profile', $item->username) }}" class="btn btn-sm btn-soft-primary" title="View Profile">
                                            <i class="ri-user-line"></i>
                                        </a>
                                        <a href="{{ route('user.edit', $item->username) }}" class="btn btn-sm btn-soft-info" title="Edit">
                                            <i class="ri-settings-3-line"></i>
                                        </a>
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
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js"></script> --}}
    {{-- <script src="{{ asset('admin') }}/assets/vendor/datatables.net-select/js/dataTables.select.min.js"></script> --}}
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>


    <script>
        function updateStatus(id) {
            $.ajax({
                url: "{{ route('user.status') }}",
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
                    if (xhr.status === 401) { // Handle incorrect password case
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
    </script>

    <script>
        async function deleteAccount(id) {
            const {
                value: password
            } = await Swal.fire({
                icon: 'info',
                title: "Are you sure you want to delete this account?",
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
                    url: "{{ route('user.destroy') }}",
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
                        if (xhr.status === 401) { // Handle incorrect password case
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
