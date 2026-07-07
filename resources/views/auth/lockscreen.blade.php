@extends('auth.master')

@section('title', 'Lock Screen')

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
                                    <span><img src="{{ asset('admin') }}/assets/images/logo-dark.png" alt="dark logo" height="28"></span>
                                </a>
                                <a href="index.html" class="logo-light">
                                    <span><img src="{{ asset('admin') }}/assets/images/logo.png" alt="logo" height="28"></span>
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-4">

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="text-center w-75 m-auto">
                                <img src="{{ asset($user->avatar) }}" height="64" alt="user-image"
                                    class="rounded-circle shadow">
                                <h4 class="text-dark-50 text-center mt-3">Hi ! {{ $user->name }}</h4>
                                <p class="text-muted mb-4">Enter your password to access the panel.</p>
                            </div>

                            <form action="{{ route('unlock.screen') }}" method="POST">@csrf
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control" type="password" name="password" required="" id="password"
                                        placeholder="Enter your password">
                                </div>

                                <div class="mb-0 text-center">
                                    <button class="btn btn-primary" type="submit">Log In</button>
                                </div>
                            </form>

                        </div> <!-- end card-body-->
                    </div>
                    <!-- end card-->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-white-50">Not you? return <a href="{{ route('login') }}" class="text-white ms-1 link-offset-3 text-decoration-underline"><b>Log In</b></a></p>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
