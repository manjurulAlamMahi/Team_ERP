@extends('admin.master')

@section('title', 'User-Profile-' . $username)
@section('quickAccessicon', 'ri-user-line')

@push('style')
    <style>
        .avatar {
            margin-top: -30px;
            margin-left: 40px;
            display: flex;
            align-items: flex-end
        }

        .avatar .name {
            margin: 0 15px
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="m-auto col-lg-5">
            <div class="card">
                <div class="w-100">
                    <img height="150" class="w-100 image-fluid" src="{{ asset($user->cover) }}" alt="">
                </div>
                <div class="avatar">
                    <div class="img">
                        <img src="{{ asset($user->avatar) }}" class="rounded-circle avatar-lg img-thumbnail"
                            alt="profile-image">
                    </div>
                    <div class="name">
                        <h4 class="mb-1 mt-2">{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->role }}</p>
                    </div>
                </div>
                <div class="card-body ">
                    <div class="text-start mt-3">
                        <label for="" class="form-label">User Information :</label>
                        <table>
                            <tr>
                                <td>
                                    <p class="text-muted mb-2"><strong class="me-1">Name</strong></p>
                                </td>
                                <td>
                                    <p class="text-muted mb-2"><strong>:</strong></p>
                                </td>
                                <td>
                                    <p class="text-muted mb-2"><span class="ms-2">{{ $user->name }}</span>
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
                                    <p class="text-muted mb-2"><span class="ms-2">{{ $user->phone ?? 'N/A' }}</span></p>
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
                                    <p class="text-muted mb-2"><span class="ms-2">{{ $user->email }}</span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <label for="" class="form-label">User Social :</label>
                        <ul class="text-center social-list list-inline mt-3 mb-0">
                            <li class="list-inline-item">
                                <a @if ($user->facebook) href="{{ $user->facebook }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                    class="social-list-item border-primary text-primary">
                                    <i class="ri-facebook-circle-fill"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a @if ($user->whatsapp) href="https://wa.me/{{ $user->whatsapp }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                    class="social-list-item border-success text-success">
                                    <i class="ri-whatsapp-line"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a @if ($user->official_email) href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $user->official_email }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                    class="social-list-item border-danger text-danger">
                                    <i class="ri-google-line"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a @if ($user->linkedin) href="{{ $user->linkedin }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                    class="social-list-item border-info text-info">
                                    <i class="ri-linkedin-line"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a @if ($user->github) href="{{ $user->github }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                    class="social-list-item border-secondary text-secondary">
                                    <i class="ri-github-fill"></i>
                                </a>
                            </li>
                        </ul>

                        <div class="send_message">
                            <form action="{{ route('user.message.store') }}" method="POST">@csrf
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <button type="submit" class="btn btn-sm rounded-pill btn-primary"><i
                                        class="ri-send-plane-line"></i> Send Message</button>
                            </form>
                        </div>
                    </div>


                </div> <!-- end card-body -->
            </div> <!-- end card -->

        </div> <!-- end col-->
    </div>
@endsection
