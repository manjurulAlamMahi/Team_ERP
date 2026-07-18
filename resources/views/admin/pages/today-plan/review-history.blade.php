@extends('admin.master')

@section('title', 'Plan Review History')
@section('quickAccessicon', 'ri-calendar-todo-line')

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
                    <i class="ri-calendar-todo-line"></i> Plan Review History - {{ $team->name }}
                </h5>
                <table id="fixed-header-datatable" class="table table-striped table-centered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Client</th>
                            <th>Profile</th>
                            <th>Status</th>
                            <th>Reviewer</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->user->name ?? 'N/A' }}</td>
                                <td>{{ $task->client_name }}</td>
                                <td>{{ $task->profile_name }}</td>
                                <td><span
                                        class="badge {{ $task->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill text-uppercase">{{ $task->status }}</span>
                                </td>
                                <td>{{ $task->reviewer->name ?? 'N/A' }}</td>
                                <td>{{ $task->reviewed_at?->format('Y-m-d H:i') }}</td>
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
@endpush
