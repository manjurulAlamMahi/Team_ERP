@extends('auth.master')

@section('title', 'Login')

@push('style')
    <style>
        /* Keep the existing .login_card styles intact */
        .login_card {
            background: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black background */
            backdrop-filter: blur(8px);
            /* Optional: Adds a blur effect to the background */
            border-radius: 8px;
            /* Optional: Rounds the corners */
            padding: 20px;
            /* Adds padding inside the card */
            color: #fff;
            /* Ensures text remains visible */
        }

        .login_card .card-body {
            padding: 20px;
            /* Adds padding to the content inside the card */
        }

        .login_card .auth-brand {
            margin-bottom: 20px;
            /* Adds space below the logo */
        }

        .login_card h4 {
            color: #fff;
            /* Ensures the title color remains white */
            font-weight: 600;
            /* Optional: Makes the title bold */
        }

        .login_card .form-label {
            color: #fff;
            /* Ensures label text is visible */
        }

        .login_card .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            /* Transparent input field background */
            border: 1px solid rgba(255, 255, 255, 0.3);
            /* Light border around inputs */
            color: #fff;
            /* Ensures input text is visible */
        }

        .login_card .form-control:focus {
            border-color: #007bff;
            /* Highlight border color when focused */
            background-color: rgba(255, 255, 255, 0.2);
            /* Slightly change the background on focus */
        }

        .login_card .btn-primary {
            background-color: #007bff;
            /* Blue button */
            border-color: #007bff;
            /* Button border color */
        }

        .login_card .btn-primary:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
            border-color: #0056b3;
            /* Darker blue border on hover */
        }

        .login_card .text-danger {
            color: #ff6b6b;
            /* Red color for error messages */
        }

        .login_card .form-check-label {
            color: #fff;
            /* Ensure the "Remember me" label text is visible */
        }
    </style>
@endpush

@section('content')
    <div class="auth-fluid">
        <!-- Auth fluid right content -->
        <div class="auth-fluid-right">
            <div class="auth-user-testimonial">
                <div class="row">
                    <div class="col-lg-3 m-auto">
                        <div class="card login_card">
                            <div class="card-body d-flex flex-column gap-3"
                                style="justify-content: center; align-items:center">

                                <!-- Logo -->
                                <div class="auth-brand text-center text-lg-start">
                                    <a href="index.html" class="logo-light">
                                        <span><img src="{{ asset($admin->logo) }}" alt="logo"
                                                height="24"></span>
                                    </a>
                                    <a href="index.html" class="logo-dark">
                                        <span><img src="{{ asset($admin->logo_dark) }}" alt="dark logo"
                                                height="24"></span>
                                    </a>
                                </div>

                                <!-- title-->
                                <h4 class="mt-0">Sign In</h4>

                                <!-- form -->
                                <form class="w-100" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label"><i>User Name</i></label>
                                        <input value="{{ old('username') }}" class="form-control" name="username"
                                            type="text" id="emailaddress"
                                            placeholder="Username / Email / Employee ID">
                                        <strong class="text-danger">{{ $errors->first('username') }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input class="form-control" name="password" type="password" required=""
                                            id="password" placeholder="Enter your password">
                                        <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input id="remember_me" name="remember" type="checkbox"
                                                class="form-check-input">
                                            <label class="form-check-label" for="remember_me">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="d-grid mb-0 text-center">
                                        <button class="btn btn-primary" type="submit"><i class="ri-login-box-line"></i>
                                            Log In
                                        </button>
                                    </div>
                                </form>
                                <!-- end form-->
                            </div> <!-- end .card-body -->


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <p class="text-white-50">Forgot Password? <a href="{{ route('password.request') }}"
                                            class="text-white ms-1 link-offset-3 text-decoration-underline"><b>Click
                                                Here</b></a></p>
                                </div> <!-- end col-->
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>
@endsection
