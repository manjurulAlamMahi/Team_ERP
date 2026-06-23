@extends('admin.master')

@section('title', 'Profile-Password')
@section('quickAccessicon', 'ri-key-fill')

@push('style')
@endpush

@section('content')
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="{{ asset(Auth::user()->avatar) }}" class="rounded-circle avatar-lg img-thumbnail"
                                alt="profile-image">

                            <h4 class="mb-1 mt-2">{{ Auth::user()->name }}</h4>
                            <p class="text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}</p>

                            <div class="text-start mt-3">
                                <table>
                                    <tr>
                                        <td>
                                            <p class="text-muted mb-2"><strong class="me-1">Name</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><strong>:</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><span class="ms-2">{{ Auth::user()->name }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="text-muted mb-2"><strong class="me-1">Mobile</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><strong>:</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><span
                                                    class="ms-2">{{ Auth::user()->phone ?? 'N/A' }}</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="text-muted mb-2"><strong class="me-1">Email</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><strong>:</strong></p>
                                        </td>
                                        <td>
                                            <p class="text-muted mb-2"><span class="ms-2">{{ Auth::user()->email }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->

                </div> <!-- end col-->

                <div class="col-lg-6">
                    <div class="card card-body">
                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="" class="form-label">Old Password</label>
                                <input type="password" class="form-control" name="old_password">
                                <a href="{{ route('password.request') }}" class="text-muted float-end fs-12">Forgot your password?</a>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" placeholder="Enter new password"
                                        name="new_password">
                                    <div class="input-group-text" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Confirm New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" placeholder="Confirm your password"
                                        name="new_password_confirmation">
                                    <div class="input-group-text" data-password="false">
                                        <span class="password-eye"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="ri-save-line"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div> <!-- end col -->
                <div class="col-lg-1"></div>
            </div>
            <!-- end row-->

        </div>
        <!-- container -->

    </div>
    <!-- content -->
@endsection

@push('script')
@endpush
