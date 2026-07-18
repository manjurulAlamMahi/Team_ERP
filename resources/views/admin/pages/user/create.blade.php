@extends('admin.master')

@section('title', 'Create-User')
@section('quickAccessicon', 'ri-user-add-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-user-add-line"></i> Create User's
                    </h5>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Employee ID</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('employee_id') is-invalid @enderror"
                                   name="employee_id" value="{{ old('employee_id') }}" placeholder="EMP-0001">
                            @error('employee_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">User Name</label>
                        <div class="col-9">
                            <input id="username" type="text" class="form-control form-control-sm @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}" autocomplete="off">
                            <small id="username-status" class="form-text"></small>
                            @error('username')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Employee Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Email</label>
                        <div class="col-9">
                            <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Role</label>
                        <div class="col-9">
                            <select id="role-select" name="role" class="form-control form-control-sm @error('role') is-invalid @enderror">
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="community-field" class="row mb-3" style="display: none;">
                        <label class="col-3 col-form-label">Community</label>
                        <div class="col-9">
                            <select name="community_id" class="form-control form-control-sm @error('community_id') is-invalid @enderror">
                                <option value="">-- Select Community --</option>
                                @foreach ($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="team-field" class="row mb-3" style="display: none;">
                        <label class="col-3 col-form-label">Team</label>
                        <div class="col-9">
                            <select name="team_id" class="form-control form-control-sm @error('team_id') is-invalid @enderror">
                                <option value="">-- Select Team --</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Stack</label>
                        <div class="col-9">
                            <select name="stack_id" class="form-control form-control-sm @error('stack_id') is-invalid @enderror">
                                <option value="">-- Select Stack --</option>
                                @foreach ($stacks as $stack)
                                    <option value="{{ $stack->id }}" {{ old('stack_id') == $stack->id ? 'selected' : '' }}>
                                        {{ $stack->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stack_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Reporting Manager</label>
                        <div class="col-9">
                            <select name="reporting_to" class="form-control form-control-sm @error('reporting_to') is-invalid @enderror">
                                <option value="">-- Select Manager --</option>
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('reporting_to') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reporting_to')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Joining Date</label>
                        <div class="col-9">
                            <input type="date" name="joining_date" required class="form-control form-control-sm @error('joining_date') is-invalid @enderror"
                                value="{{ old('joining_date') }}">
                            @error('joining_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">End Of Probation Date</label>
                        <div class="col-9">
                            <input type="date" name="probation_end_date" required class="form-control form-control-sm @error('probation_end_date') is-invalid @enderror"
                                value="{{ old('probation_end_date') }}">
                            @error('probation_end_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <script>
                        let usernameCheckTimeout;

                        function checkUsernameAvailability(excludeId = null) {
                            const username = document.getElementById('username').value.trim();
                            const status = document.getElementById('username-status');

                            if (!username) {
                                status.textContent = '';
                                status.className = 'form-text';
                                return;
                            }

                            clearTimeout(usernameCheckTimeout);
                            usernameCheckTimeout = setTimeout(function () {
                                $.ajax({
                                    url: "{{ route('username.check') }}",
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        username: username,
                                        exclude_id: excludeId,
                                    },
                                    success: function (response) {
                                        status.textContent = response.message;
                                        status.className = response.available ? 'form-text text-success' : 'form-text text-danger';
                                    },
                                    error: function () {
                                        status.textContent = 'Unable to verify username at this time.';
                                        status.className = 'form-text text-danger';
                                    }
                                });
                            }, 500);
                        }

                        function toggleUserOrgFields() {
                            const role = document.getElementById('role-select').value;
                            const teamRow = document.getElementById('team-field');
                            const communityRow = document.getElementById('community-field');
                            const teamInput = document.querySelector('select[name="team_id"]');
                            const communityInput = document.querySelector('select[name="community_id"]');
                            const joiningInput = document.querySelector('input[name="joining_date"]');
                            const probationInput = document.querySelector('input[name="probation_end_date"]');

                            const teamRoles = ['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation'];
                            const noOrgRoles = ['GM', 'AGM'];

                            // Default: hide both org selectors
                            teamRow.style.display = 'none';
                            communityRow.style.display = 'none';
                            if (teamInput) teamInput.required = false;
                            if (communityInput) communityInput.required = false;

                            if (teamRoles.includes(role)) {
                                teamRow.style.display = 'flex';
                                if (teamInput) teamInput.required = true;
                                if (communityInput) communityInput.required = false;
                            } else if (role === 'Operation Manager') {
                                communityRow.style.display = 'flex';
                                if (communityInput) communityInput.required = true;
                                if (teamInput) teamInput.required = false;
                            } else if (noOrgRoles.includes(role)) {
                                // GM/AGM: no assignment required
                                if (teamInput) teamInput.required = false;
                                if (communityInput) communityInput.required = false;
                            } else {
                                // Other roles: no org assigned
                                if (teamInput) teamInput.required = false;
                                if (communityInput) communityInput.required = false;
                            }

                            // Joining and probation are required for all users per business rules
                            if (joiningInput) joiningInput.required = true;
                            if (probationInput) probationInput.required = true;
                        }

                        document.getElementById('role-select').addEventListener('change', function () {
                            toggleUserOrgFields();
                        });
                        document.getElementById('username').addEventListener('input', function () {
                            checkUsernameAvailability();
                        });
                        window.addEventListener('DOMContentLoaded', function () {
                            toggleUserOrgFields();
                            checkUsernameAvailability();
                        });
                    </script>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Password</label>
                        <div class="col-9">
                            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                                   name="password">
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Confirm Password</label>
                        <div class="col-9">
                            <input type="password" class="form-control form-control-sm" name="password_confirmation">
                        </div>
                    </div>

                    <div class="row" style="align-items: end">
                        <div class="text-end d-flex justify-content-end gap-2">
                            <a href="{{ route('user.list') }}" class="btn btn-soft-secondary mt-2">
                                <i class="ri-close-line"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Create
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection




