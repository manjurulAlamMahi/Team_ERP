@extends('admin.master')

@section('title', 'Admin-Setting')
@section('quickAccessicon', 'ri-settings-5-line')

@section('content')
    <form action="{{ route('setting.admin.update') }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-body">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-file-image-line"></i> Logo & Icon
                    </h5>
                    <div class="row mb-3">
                        <label for="logo" class="col-3 col-form-label">Logo-light</label>
                        <div class="col-9">
                            <input type="file" oninput="logo_img.src=window.URL.createObjectURL(this.files[0])"
                                class="form-control form-control-sm" id="logo" name="logo">
                            <br>
                            <div class="bg" style="background: rgba(30, 30, 30, 0.4); width:270px">
                                <img width="270" height="72" id="logo_img"
                                    src="{{ asset($admin->logo) }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="logo-dark" class="col-3 col-form-label">Logo-dark</label>
                        <div class="col-9">
                            <input type="file" oninput="logo_dark_img.src=window.URL.createObjectURL(this.files[0])"
                                class="form-control form-control-sm" id="logo-dark" name="logo_dark">
                            <br>
                            <div class="bg" style="background: rgba(30, 30, 30, 0.4); width:270px">
                                <img width="270" height="72" id="logo_dark_img"
                                    src="{{ asset($admin->logo_dark) }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="logo-sm" class="col-3 col-form-label">Logo-small</label>
                        <div class="col-9">
                            <input type="file" oninput="logo_sm_img.src=window.URL.createObjectURL(this.files[0])"
                                class="form-control form-control-sm" id="logo-sm" name="logo_sm">
                            <br>
                            <div class="bg" style="background: rgba(30, 30, 30, 0.4); width:70px">
                                <img width="70" height="70" id="logo_sm_img"
                                    src="{{ asset($admin->logo_sm) }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card card-body mb-3">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-information-line"></i> Admin Panel Information
                    </h5>
                    <div class="row mb-3">
                        <label for="logo" class="col-3 col-form-label">Admin Panel Name</label>
                        <div class="col-9">
                            <input type="text" class="form-control form-control-sm" name="name" value="{{ $admin->name }}">
                        </div>
                    </div>
                </div>
                <div class="card card-body">
                    <div class="row" style="align-items: end">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userpassword" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="userpassword" name="password" placeholder="Enter password to make the change">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span><br>
                                @enderror
                                <span class="form-text text-muted"><small>If you want to change
                                        password please <a href="{{ route('profile.password') }}">click</a>
                                        here.</small></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success mt-2">
                                        <i class="ri-save-line"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row -->
                </div>
            </div>
        </div>
    </form>
@endsection
