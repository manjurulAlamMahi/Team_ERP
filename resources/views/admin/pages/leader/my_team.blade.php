@extends('admin.master')

@section('title', 'My Team')
@section('quickAccessicon', 'ri-team-line')

@push('style')
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 m-auto">
            <div class="card card-body">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ri-team-line"></i> {{ $team->name }} Team Members
                </h5>

                <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Role</th>
                            <th>Stack</th>
                            <th>Email</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->employee_id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <select class="form-select form-select-sm role-select" data-id="{{ $user->id }}" data-previous="{{ $user->getRoleNames()->first() }}" style="width: 130px;">
                                        @foreach (['Co Leader', 'Stack Lead', 'Member', 'Probation'] as $allowedRole)
                                            <option value="{{ $allowedRole }}" {{ $user->getRoleNames()->first() === $allowedRole ? 'selected' : '' }}>
                                                {{ $allowedRole }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $user->stack->name ?? 'N/A' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->joining_date }}</td>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="toggleStatus({{ $user->id }})">Change Status</button>
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
    <script src="{{ asset('admin') }}/assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    <script src="{{ asset('admin') }}/assets/js/pages/demo.datatable-init.js"></script>

    <script>
        function toggleStatus(id) {
            $.ajax({
                url: "{{ route('leader.member.status') }}",
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
                        title: 'Unable to update status.',
                    });
                }
            });
        }

        $(document).on('change', '.role-select', function() {
            const $select = $(this);
            const id = $select.data('id');
            const role = $select.val();
            const previousRole = $select.data('previous');

            $.ajax({
                url: "{{ route('leader.member.role') }}",
                type: 'POST',
                data: {
                    id: id,
                    role: role,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        $select.data('previous', role);
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                    } else {
                        $select.val(previousRole);
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function() {
                    $select.val(previousRole);
                    Toast.fire({
                        icon: 'error',
                        title: 'Unable to update role.',
                    });
                }
            });
        });
    </script>
@endpush
