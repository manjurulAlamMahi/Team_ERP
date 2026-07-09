@extends('admin.master')

@section('title', 'My Clients')
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
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-team-line"></i> My Clients
                </h5>

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

                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Client Username</th>
                            <th>Profile</th>
                            <th>Client Name</th>
                            <th>Sales Person Name</th>
                            <th>Sales Person WhatsApp</th>
                            <th>Country</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client->username }}</td>
                                <td>{{ $client->profile->name ?? 'N/A' }}</td>
                                <td>{{ $client->client_name ?? 'N/A' }}</td>
                                <td>{{ $client->sales_man_name ?? 'N/A' }}</td>
                                <td>{{ $client->sales_man_whatsapp ?? 'N/A' }}</td>
                                <td>{{ $client->country ?? 'N/A' }}</td>
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
    </script>
@endpush
