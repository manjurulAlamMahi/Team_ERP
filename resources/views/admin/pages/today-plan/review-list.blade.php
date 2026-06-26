@extends('admin.master')

@section('title', 'Pending Plan Review')
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
                    <i class="ri-calendar-todo-line"></i> Pending Plan Review - {{ $team->name }}
                </h5>
                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Client</th>
                            <th>Profile</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->user->name ?? 'N/A' }}</td>
                                <td>{{ $task->client_name }}</td>
                                <td>{{ $task->profile_name }}</td>
                                <td>{{ $task->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('today.plan.review.show', $task->id) }}" class="text-reset fs-16 px-1">
                                        <i class="ri-eye-line"></i>
                                    </a>
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
@endpush
