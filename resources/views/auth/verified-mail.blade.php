@extends('auth.master')

@section('title', 'Confirm Email')

@section('content')
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <!-- Logo -->
                        <div class="card-header pt-4 text-center">
                            <div class="auth-brand mb-0">
                                <a href="index.html" class="logo-dark">
                                    <span><img src="{{ asset($admin->logo_dark) }}" alt="dark logo"
                                            height="28"></span>
                                </a>
                                <a href="index.html" class="logo-light">
                                    <span><img src="{{ asset($admin->logo) }}" alt="logo"
                                            height="28"></span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-center m-auto">
                                <img src="{{ asset('admin/assets/images/verfied.png') }}" alt="mail sent image"
                                    height="64">
                                <h4 class="text-dark-50 text-center mt-4">Email Verified Successfully!</h4>
                                <p class="text-muted mb-4 mt-3">
                                    Your email <span
                                        class="fw-medium text-decoration-underline">{{ $user->email }}</span>
                                    has been successfully verified. <br>
                                    <strong>Note:</strong> If you change your email, you will need to verify your account
                                    again.
                                </p>
                            </div>

                            <form action="{{ route('dashboard') }}">
                                <div class="mb-0 text-center">
                                    <button class="btn btn-primary" type="submit"><i class="ri-home-4-line me-1"></i>
                                        Back to Home</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    <!-- end card -->

                </div> <!-- end col -->
            </div>
        </div>
        <!-- end container -->
    </div>
@endsection
