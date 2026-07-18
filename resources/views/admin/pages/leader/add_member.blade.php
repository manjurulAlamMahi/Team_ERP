@extends('admin.master')

@section('title', 'Add Member')
@section('quickAccessicon', 'ri-user-add-line')

@section('content')
    <div class="row">
        <div class="col-lg-8 m-auto">
            <form action="{{ route('leader.store.member') }}" method="POST">
                @csrf
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-user-add-line"></i> Add Team Member
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
                            <input type="text" class="form-control form-control-sm @error('username') is-invalid @enderror"
                                   name="username" value="{{ old('username') }}" autocomplete="off">
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
                            <select name="role" class="form-control form-control-sm @error('role') is-invalid @enderror">
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

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Joining Date</label>
                        <div class="col-9">
                            <input type="date" class="form-control form-control-sm @error('joining_date') is-invalid @enderror"
                                   name="joining_date" value="{{ old('joining_date') }}">
                            @error('joining_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">End Of Probation Date</label>
                        <div class="col-9">
                            <input type="date" class="form-control form-control-sm @error('probation_end_date') is-invalid @enderror"
                                   name="probation_end_date" value="{{ old('probation_end_date') }}">
                            @error('probation_end_date')
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
                        <label class="col-3 col-form-label">Whatsapp Number</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('whatsapp') is-invalid @enderror"
                                   name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Optional">
                            @error('whatsapp')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

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

                    <div class="row mb-3">
                        <label class="col-3 col-form-label">Phone Number</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" placeholder="Optional">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 text-end d-flex justify-content-end gap-2">
                            <a href="{{ route('leader.my.team') }}" class="btn btn-soft-secondary mt-2">
                                <i class="ri-close-line"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success mt-2">
                                <i class="ri-save-line"></i> Add Member
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
