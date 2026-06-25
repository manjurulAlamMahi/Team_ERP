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
                            @php $canManage = $user->canBeManagedBy($actor); @endphp
                            <tr>
                                <td>{{ $user->employee_id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    @if ($canManage)
                                        <select class="form-select form-select-sm role-select" data-id="{{ $user->id }}" data-previous="{{ $user->getRoleNames()->first() }}" style="width: 130px;">
                                            @foreach (['Co Leader', 'Stack Lead', 'Member', 'Probation'] as $allowedRole)
                                                <option value="{{ $allowedRole }}" {{ $user->getRoleNames()->first() === $allowedRole ? 'selected' : '' }}>
                                                    {{ $allowedRole }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $user->getRoleNames()->first() ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ $user->stack->name ?? 'N/A' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->joining_date }}</td>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td>
                                    @if ($canManage)
                                        <button type="button" class="btn btn-sm btn-outline-secondary open-edit-modal"
                                                data-id="{{ $user->id }}"
                                                data-employee_id="{{ $user->employee_id }}"
                                                data-username="{{ $user->username }}"
                                                data-name="{{ $user->name }}"
                                                data-email="{{ $user->email }}"
                                                data-phone="{{ $user->phone }}"
                                                data-whatsapp="{{ $user->whatsapp }}"
                                                data-joining_date="{{ $user->joining_date }}"
                                                data-probation_end_date="{{ $user->probation_end_date }}">Edit</button>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleStatus({{ $user->id }})">Change Status</button>
                                    @endif
                                    @if ($actor->hasRole('Leader'))
                                        <button type="button" class="btn btn-sm btn-outline-warning open-password-modal" data-id="{{ $user->id }}" data-name="{{ $user->name }}">Change Password</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($actor->hasRole('Leader'))
        <div class="modal fade" id="memberPasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="memberPasswordForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Password for <span id="memberPasswordName"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="memberPasswordId">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="memberEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="memberEditForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit <span id="memberEditName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="memberEditId">
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" id="memberEditEmployeeId" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">User Name</label>
                            <input type="text" name="username" id="memberEditUsername" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Employee Name</label>
                            <input type="text" name="name" id="memberEditFullName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="memberEditEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="memberEditPhone" class="form-control" placeholder="Optional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Whatsapp Number</label>
                            <input type="text" name="whatsapp" id="memberEditWhatsapp" class="form-control" placeholder="Optional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" id="memberEditJoiningDate" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Of Probation Date</label>
                            <input type="date" name="probation_end_date" id="memberEditProbationEndDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
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

        $(document).on('click', '.open-password-modal', function() {
            $('#memberPasswordForm')[0].reset();
            $('#memberPasswordId').val($(this).data('id'));
            $('#memberPasswordName').text($(this).data('name'));
            new bootstrap.Modal(document.getElementById('memberPasswordModal')).show();
        });

        $(document).on('submit', '#memberPasswordForm', function(e) {
            e.preventDefault();
            const $form = $(this);

            $.ajax({
                url: "{{ route('leader.member.password') }}",
                type: 'POST',
                data: $form.serialize() + '&_token={{ csrf_token() }}',
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        bootstrap.Modal.getInstance(document.getElementById('memberPasswordModal')).hide();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                        : 'Unable to update password.';
                    Toast.fire({
                        icon: 'error',
                        title: message,
                    });
                }
            });
        });

        $(document).on('click', '.open-edit-modal', function() {
            const $btn = $(this);
            $('#memberEditForm')[0].reset();
            $('#memberEditId').val($btn.data('id'));
            $('#memberEditName').text($btn.data('name'));
            $('#memberEditEmployeeId').val($btn.data('employee_id'));
            $('#memberEditUsername').val($btn.data('username'));
            $('#memberEditFullName').val($btn.data('name'));
            $('#memberEditEmail').val($btn.data('email'));
            $('#memberEditPhone').val($btn.data('phone'));
            $('#memberEditWhatsapp').val($btn.data('whatsapp'));
            $('#memberEditJoiningDate').val($btn.data('joining_date'));
            $('#memberEditProbationEndDate').val($btn.data('probation_end_date'));
            new bootstrap.Modal(document.getElementById('memberEditModal')).show();
        });

        $(document).on('submit', '#memberEditForm', function(e) {
            e.preventDefault();
            const $form = $(this);

            $.ajax({
                url: "{{ route('leader.member.info') }}",
                type: 'POST',
                data: $form.serialize() + '&_token={{ csrf_token() }}',
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
                error: function(xhr) {
                    const message = xhr.responseJSON && xhr.responseJSON.errors
                        ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                        : 'Unable to update member info.';
                    Toast.fire({
                        icon: 'error',
                        title: message,
                    });
                }
            });
        });

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
                error: function(xhr) {
                    $select.val(previousRole);
                    Toast.fire({
                        icon: 'error',
                        title: (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to update role.',
                    });
                }
            });
        });
    </script>
@endpush
