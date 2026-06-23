@extends('admin.master')

@section('title', 'Mail-Setting')
@section('quickAccessicon', 'ri-mail-line')

@section('content')
    <form action="{{ route('setting.mail.update') }}" method="POST">@csrf
        <div class="row">
            <div class="col-lg-8 m-auto">
                <div class="card card-body mb-3">
                    <h5 class="mb-3 text-uppercase bg-light p-2">
                        <i class="ri-mail-line"></i> Mail Setting
                    </h5>
                    <p class="card-description">Setup your system mail, please <code>provide your valid
                        data</code>.</p>
                    <div class="form-group row mb-3">
                        <div class="col">
                            <label class="form-lable">MAIL MAILER</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_mailer') is-invalid @enderror"
                                placeholder="MAIL MAILER" name="mail_mailer"
                                value="{{ env('MAIL_MAILER') }}" required>
                            @error('mail_mailer')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-lable">MAIL HOST</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_host') is-invalid @enderror"
                                placeholder="MAIL HOST" name="mail_host"
                                value="{{ env('MAIL_HOST') }}" required>
                            @error('mail_host')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col">
                            <label class="form-lable">MAIL PORT</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_port') is-invalid @enderror"
                                placeholder="MAIL PORT" name="mail_port"
                                value="{{ env('MAIL_PORT') }}" required>
                            @error('mail_port')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-lable">MAIL USERNAME</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_username') is-invalid @enderror"
                                placeholder="MAIL USERNAME" name="mail_username"
                                value="{{ env('MAIL_USERNAME') }}" required>
                            @error('mail_username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col">
                            <label class="form-lable">MAIL PASSWORD</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_password') is-invalid @enderror"
                                placeholder="MAIL PASSWORD" name="mail_password"
                                value="{{ env('MAIL_PASSWORD') }}" required>
                            @error('mail_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-lable">MAIL ENCRYPTION</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_encryption') is-invalid @enderror"
                                placeholder="MAIL ENCRYPTION" name="mail_encryption"
                                value="{{ env('MAIL_ENCRYPTION') }}" required>
                            @error('mail_encryption')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-6">
                            <label class="form-lable">MAIL FROM ADDRESS</label>
                            <input type="text"
                                class="form-control form-control-md border-left-0 @error('mail_from_address') is-invalid @enderror"
                                placeholder="MAIL FROM ADDRESS" name="mail_from_address"
                                value="{{ env('MAIL_FROM_ADDRESS') }}" required>
                            @error('mail_from_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
