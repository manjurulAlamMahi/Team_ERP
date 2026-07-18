@extends('admin.master')

@section('title', 'My Team')
@section('quickAccessicon', 'ri-team-line')

@push('style')
<style>
    .member-card { transition: box-shadow 0.2s; }
    .member-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.12) !important; }
    .member-avatar-wrap { position: relative; display: inline-block; }
    .member-online-badge {
        position: absolute;
        bottom: 4px; right: 4px;
        width: 13px; height: 13px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .member-online-badge.online  { background: #198754; }
    .member-online-badge.offline { background: #adb5bd; }
</style>
@endpush

@section('content')
    <div class="mb-3">
        <h5 class="mb-0 text-uppercase bg-light p-2 rounded">
            <i class="ri-team-line me-1"></i> {{ $team->name }} Team Members
        </h5>
    </div>

    <div class="row g-3">
        @foreach ($users as $user)
            @php $canManage = $user->canBeManagedBy($actor); @endphp
            <div class="col-xl-3 col-md-4 col-sm-6">
                <div class="card member-card h-100 d-flex flex-column mb-0">
                    {{-- Avatar section --}}
                    <div class="card-body text-center pt-4 pb-2 flex-grow-1">
                        <div class="member-avatar-wrap mb-2 d-inline-block">
                            <img src="{{ asset($user->avatar) }}"
                                class="rounded-circle border border-3 border-white shadow"
                                width="64" height="64" style="object-fit:cover;" alt="{{ $user->name }}">
                            <span class="member-online-badge {{ session('online_' . $user->id) ? 'online' : 'offline' }}"></span>
                        </div>

                        {{-- Name --}}
                        <h6 class="mb-1">
                            <a href="{{ route('user.profile', $user->username) }}" class="text-body fw-semibold">{{ $user->name }}</a>
                        </h6>

                        {{-- Role badge --}}
                        <div class="mb-2">
                            @if ($canManage)
                                <select class="form-select form-select-sm role-select d-inline-block w-auto"
                                    data-id="{{ $user->id }}"
                                    data-previous="{{ $user->getRoleNames()->first() }}">
                                    @foreach (['Co Leader', 'Stack Lead', 'Member', 'Probation'] as $allowedRole)
                                        <option value="{{ $allowedRole }}" {{ $user->getRoleNames()->first() === $allowedRole ? 'selected' : '' }}>
                                            {{ $allowedRole }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <span class="badge bg-light text-dark border">{{ $user->getRoleNames()->first() ?? 'N/A' }}</span>
                            @endif
                        </div>

                        {{-- Status badge --}}
                        <span class="badge {{ $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill mb-3">
                            {{ ucfirst($user->status) }}
                        </span>

                        {{-- Info list --}}
                        <ul class="list-unstyled text-start fs-12 mb-0">
                            @if ($user->stack)
                                <li class="mb-1 d-flex align-items-center gap-1">
                                    <i class="ri-stack-line text-muted flex-shrink-0"></i>
                                    <span class="text-truncate">{{ $user->stack->name }}</span>
                                </li>
                            @endif
                            <li class="mb-1 d-flex align-items-center gap-1">
                                <i class="ri-mail-line text-muted flex-shrink-0"></i>
                                <span class="text-truncate">{{ $user->email }}</span>
                            </li>
                            @if ($user->whatsapp)
                                <li class="mb-1 d-flex align-items-center gap-1">
                                    <i class="ri-whatsapp-line text-muted flex-shrink-0"></i>
                                    <span>{{ $user->whatsapp }}</span>
                                </li>
                            @endif
                            @if ($user->official_email)
                                <li class="mb-1 d-flex align-items-center gap-1">
                                    <i class="ri-google-line text-muted flex-shrink-0"></i>
                                    <span class="text-truncate">{{ $user->official_email }}</span>
                                </li>
                            @endif
                            @if ($user->joining_date)
                                <li class="mb-1 d-flex align-items-center gap-1">
                                    <i class="ri-calendar-line text-muted flex-shrink-0"></i>
                                    <span>Joined {{ $user->joining_date }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- Footer buttons --}}
                    <div class="card-footer bg-transparent pt-2 pb-3">
                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                            <a href="{{ route('user.profile', $user->username) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-user-line me-1"></i> Profile
                            </a>
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
                                    data-probation_end_date="{{ $user->probation_end_date }}">
                                    <i class="ri-edit-line me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="toggleStatus({{ $user->id }})">
                                    <i class="ri-refresh-line me-1"></i> Status
                                </button>
                            @endif
                            @if ($actor->hasRole('Leader'))
                                <button type="button" class="btn btn-sm btn-outline-warning open-password-modal"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                    <i class="ri-lock-password-line me-1"></i> Password
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
