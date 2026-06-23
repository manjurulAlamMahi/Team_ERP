@extends('auth.master')

@section('title', 'Forgot Password')

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
                                    <span><img src="{{ asset('admin') }}/assets/images/logo-dark.png" alt="dark logo"
                                            height="28"></span>
                                </a>
                                <a href="index.html" class="logo-light">
                                    <span><img src="{{ asset('admin') }}/assets/images/logo.png" alt="logo"
                                            height="28"></span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0">Reset Password</h4>
                                <p class="text-muted mb-4">Enter your email address and we'll send you an email with
                                    instructions to reset your password.</p>
                            </div>

                            <form action="{{ route('password.email') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="emailaddress" class="form-label">Email address</label>
                                    <input class="form-control" name="email" type="email" id="emailaddress"
                                        required="" placeholder="Enter your email">
                                </div>
                                @error('email')
                                    <strong class="text-danger">{{ $message }}</strong>
                                @enderror

                                <div class="mb-0 text-center">
                                    <button class="btn btn-primary" type="submit">Reset Password</button>
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
@endsection
