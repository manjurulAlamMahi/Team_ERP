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
                                <img src="{{ asset('admin') }}/assets/images/svg/mail_sent.svg" alt="mail sent image"
                                    height="64">
                                <h4 class="text-dark-50 text-center mt-4">Please check your email</h4>
                                <p class="text-muted mb-4 mt-3">
                                    An email has been sent to <span
                                        class="fw-medium text-decoration-underline">{{ session('email') }}</span>.
                                    Please check for an email from our company and click on the included link to reset
                                    your password.
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

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-white-50">Back to <a href="{{ route('login') }}"
                                    class="text-white ms-1 link-offset-3 text-decoration-underline"><b>Log In</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->
@endsection
