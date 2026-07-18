@extends('admin.master')

@section('title', 'Client Lists')
@section('quickAccessicon', 'ri-team-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css"
        rel="stylesheet" type="text/css" />
@endpush

@section('content')
    @php $authUser = Auth::user(); $isLead = $authUser->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']); @endphp

    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0 text-uppercase bg-light p-2 flex-grow-1">
                        <i class="ri-team-line"></i> Client Lists
                    </h5>
                    @if ($isLead)
                        <div class="d-flex gap-2">
                            <a href="{{ route('client.create') }}" class="btn btn-sm btn-success">
                                <i class="ri-add-line me-1"></i> Add New
                            </a>
                            <a href="{{ route('client.bulk.create') }}" class="btn btn-sm btn-outline-success">
                                <i class="ri-file-add-line me-1"></i> Add Multiple
                            </a>
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter by Profile</label>
                        <select id="profileFilter" class="form-select">
                            <option value="">All Profiles</option>
                            @foreach ($profiles as $profile)
                                <option value="{{ $profile->name }}">{{ $profile->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Client Username</th>
                            <th>Profile</th>
                            <th>Client Name</th>
                            <th>Sales Person Name</th>
                            <th>Sales Person WhatsApp</th>
                            <th>Country</th>
                            @if ($isLead)
                                <th class="text-end">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($clients as $client)
                            <tr id="client-row-{{ $client->id }}">
                                <td>{{ $client->username }}</td>
                                <td>{{ $client->profile->name ?? 'N/A' }}</td>
                                <td>{{ $client->client_name ?? 'N/A' }}</td>
                                <td>{{ $client->sales_man_name ?? 'N/A' }}</td>
                                <td>{{ $client->sales_man_whatsapp ?? 'N/A' }}</td>
                                <td>{{ $client->country ?? 'N/A' }}</td>
                                @if ($isLead)
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('client.edit', $client->id) }}" class="btn btn-sm btn-soft-secondary" title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="deleteClient({{ $client->id }})"
                                                class="btn btn-sm btn-soft-danger" title="Delete">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </a>
                                        </div>
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
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js">
    </script>
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        $('#profileFilter').on('change', function() {
            var table = $('#fixed-header-datatable').DataTable();
            var value = this.value;
            table.column(1).search(value ? '^' + $.fn.dataTable.util.escapeRegex(value) + '$' : '', true, false).draw();
        });

        async function deleteClient(id) {
            const {
                value: password
            } = await Swal.fire({
                icon: 'info',
                title: "Are you sure you want to delete this client?",
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
                    url: "{{ route('client.destroy') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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
                        if (xhr.status === 401) {
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Incorrect Password'
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Something went wrong.'
                            });
                        }
                    }
                });
            }
        }
    </script>
@endpush
