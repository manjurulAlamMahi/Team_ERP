@extends('admin.master')

@section('title', 'Profile')
@section('quickAccessicon', 'ri-user-star-line')

@push('style')
    <style>
        .avatar_list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 8px;
        }

        .cover_banner_list {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px 0;
        }

        .cover_label {
            display: inline-block;
            cursor: pointer;
            border: 2px solid transparent;
            /* Default border */
            border-radius: 5px;
            padding: 3px;
            transition: border 0.3s ease-in-out;
        }

        .cover_radio {
            display: none;
        }

        .avatar-lg.img-thumbnail.active {
            background: #4254ba !important;
        }

        .cover_radio:checked+.cover_image {
            border: 3px solid #4254ba;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(66, 84, 186, 0.7);
        }

        .flexy-flex {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .avatar-col {
            position: relative;
            display: inline-block;
        }

        .removeAvatar {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            /* Semi-transparent black background */
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .avatar-col:hover .removeAvatar {
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{ asset(Auth::user()->avatar) }}" class="rounded-circle avatar-lg img-thumbnail"
                        alt="profile-image">

                    <h4 class="mb-1 mt-2">{{ Auth::user()->name }}</h4>
                    <p class="text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}</p>

                    <ul class="social-list list-inline mt-3 mb-0">
                        <li class="list-inline-item">
                            <a @if (Auth::user()->facebook) href="{{ Auth::user()->facebook }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                class="social-list-item border-primary text-primary">
                                <i class="ri-facebook-circle-fill"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a @if (Auth::user()->whatsapp) href="https://wa.me/{{ Auth::user()->whatsapp }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                class="social-list-item border-success text-success">
                                <i class="ri-whatsapp-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a @if (Auth::user()->gmail) href="https://mail.google.com/mail/?view=cm&fs=1&to={{ Auth::user()->gmail }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                class="social-list-item border-danger text-danger">
                                <i class="ri-google-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a @if (Auth::user()->linkedin) href="{{ Auth::user()->linkedin }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                class="social-list-item border-info text-info">
                                <i class="ri-linkedin-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a @if (Auth::user()->github) href="{{ Auth::user()->github }}" target="_blank"
                                @else href="javascript: void(0);" @endif
                                class="social-list-item border-secondary text-secondary">
                                <i class="ri-github-fill"></i>
                            </a>
                        </li>
                    </ul>


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


                    <div class="row my-2">
                        <div class="col-lg-6">
                            @if (Auth::user()->email_verified_at == null)
                                <div class="text-start">
                                    <a href="{{ route('email.verify') }}" class="btn btn-warning btn-sm mb-2">Verify
                                        Email</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            @if (Auth::user()->getRoleNames()->first() != 'Super Admin' )
                                <div class="text-end">
                                    <button type="button" onclick="deleteAccount()"
                                        class="btn btn-danger btn-sm mb-2">Delete Account</button>
                                </div>
                            @endif
                        </div>
                    </div>

                </div> <!-- end card-body -->
            </div> <!-- end card -->

        </div> <!-- end col-->

        <div class="col-xl-8 col-lg-7">

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#aboutme" data-bs-toggle="tab" aria-expanded="false"
                                class="nav-link rounded-start rounded-0 {{ session()->has('profile_update') ? '' : 'active' }}">
                                Avatar & Cover
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#timeline" data-bs-toggle="tab" aria-expanded="true"
                                class="nav-link rounded-0 {{ session()->has('profile_update') ? 'active' : '' }}">
                                Personal Info
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane {{ session()->has('profile_update') ? '' : 'show active' }}" id="aboutme">
                            <label for="" class="form-label">Profile Avatar</label>

                            <div class="avatar_list">
                                @foreach ($avatar as $key => $img)
                                    <div class="avatar-col" style="cursor: pointer">
                                        <img src="{{ asset($img->image) }}"
                                            onclick="updateAvatar({{ $img->id }} , {{ Auth::id() }})"
                                            class="rounded-circle avatar-lg img-thumbnail {{ $img->image == Auth::user()->avatar ? 'active' : '' }}"
                                            alt="profile-image">
                                        @if ($key > 10 && $img->image != Auth::user()->avatar)
                                            <button onclick="deleteData('{{ route('avatar.destroy', $img->id) }}')"
                                                type="button" class="removeAvatar"><i class="ri-close-line"></i></button>
                                        @endif

                                    </div>
                                @endforeach
                                @if ($avatar->count() != 18)
                                    <div class="avatar-col" style="cursor: pointer">
                                        <label style="cursor: pointer" for="add_avatar"
                                            class="rounded-circle avatar-lg flexy-flex">
                                            <input type="file" id="add_avatar" onchange="addAvatar()" hidden>
                                            <img width="80%" src="{{ asset('user/avatar/add_icon.png') }}"
                                                class="" alt="profile-image">
                                        </label>
                                    </div>
                                @endif

                            </div>

                            <hr class="my-2">
                            <label for="" class="form-label">Profile Cover Banner</label>

                            <div class="cover_banner_list">
                                @foreach ($cover as $img)
                                    <label class="cover_label">
                                        <input type="radio" name="cover_image" class="cover_radio"
                                            value="{{ $img->id }}"
                                            onclick="updateCoverBanner({{ $img->id }} , {{ Auth::id() }})"
                                            {{ $img->image == Auth::user()->cover ? 'checked' : '' }}>
                                        <div class="cover_image"
                                            style="width: 260px; height: 80px; background: url('{{ asset($img->image) }}'); background-position: center; background-repeat: no-repeat;">
                                        </div>
                                    </label>
                                @endforeach
                            </div>


                        </div> <!-- end tab-pane -->
                        <!-- end about me section content -->

                        <div class="tab-pane {{ session()->has('profile_update') ? 'show active' : '' }}" id="timeline">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label"><i>User Name</i></label>
                                            <input type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                name="username" id="username"
                                                value="{{ old('username', Auth::user()->username) }}" autocomplete="off">
                                            <small id="username-status" class="form-text"></small>
                                            @error('username')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', Auth::user()->name) }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" value="{{ old('email', Auth::user()->email) }}">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="official_email" class="form-label">Official Email</label>
                                            <input type="email"
                                                class="form-control @error('official_email') is-invalid @enderror" id="official_email"
                                                name="official_email" value="{{ old('official_email', Auth::user()->official_email) }}">
                                            @error('official_email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="whatsapp" class="form-label">Whatsapp Number</label>
                                            <input type="text"
                                                class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp"
                                                name="whatsapp" value="{{ old('whatsapp', Auth::user()->whatsapp) }}" placeholder="01XXXXXXXXX">
                                            @error('whatsapp')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text"
                                                class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                name="phone" value="{{ old('phone', Auth::user()->phone) }}" placeholder="Optional">
                                            @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="designation" class="form-label">Designation</label>
                                            <input type="text"
                                                class="form-control @error('designation') is-invalid @enderror" id="designation"
                                                name="designation" value="{{ old('designation', Auth::user()->designation) }}" placeholder="e.g. UI/UX Designer">
                                            @error('designation')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Date of Birth</label>
                                            <input type="date"
                                                class="form-control @error('dob') is-invalid @enderror" id="dob"
                                                name="dob" value="{{ old('dob', Auth::user()->dob) }}">
                                            @error('dob')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea id="address" name="address" rows="2"
                                                class="form-control @error('address') is-invalid @enderror">{{ old('address', Auth::user()->address) }}</textarea>
                                            @error('address')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="dob" class="form-label">Date Of Birth</label>
                                            <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                                id="dob" name="dob"
                                                value="{{ old('dob', Auth::user()->dob) }}">
                                            @error('dob')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> <!-- end row -->

                                <h5 class="mb-3 text-uppercase bg-light p-2">
                                    <i class="ri-global-line me-1"></i> Social
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Facebook</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-facebook-fill"></i></span>
                                                <input type="text" class="form-control" name="facebook"
                                                    placeholder="Url" value="{{ old('facebook', Auth::user()->facebook) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Telegram</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-telegram-fill"></i></span>
                                                <input type="text" class="form-control" name="telegram"
                                                    placeholder="@username" value="{{ old('telegram', Auth::user()->telegram) }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Gmail</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-google-line"></i></span>
                                                <input type="text" class="form-control" name="gmail"
                                                    placeholder="MailTo:" value="{{ old('gmail', Auth::user()->gmail) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Linkedin</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-linkedin-fill"></i></span>
                                                <input type="text" class="form-control" name="linkedin"
                                                    placeholder="Url" value="{{ old('linkedin', Auth::user()->linkedin) }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Github</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-github-line"></i></span>
                                                <input type="text" class="form-control" name="github"
                                                    placeholder="Url" value="{{ old('github', Auth::user()->github) }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Discord</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-discord-fill"></i></span>
                                                <input type="text" class="form-control" name="discord"
                                                    placeholder="Discord username" value="{{ old('discord', Auth::user()->discord) }}">
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->

                                <div class="row" style="align-items: end">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="userpassword" class="form-label">Password</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="userpassword" name="password"
                                                placeholder="Enter password to make the change">
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
                            </form>
                        </div>
                        <!-- end timeline content-->

                    </div> <!-- end tab-content -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
    <!-- end row-->
@endsection

@push('script')
    <script>
        function updateCoverBanner(cover_id, user_id) {
            $.ajax({
                url: "{{ route('profile.cover') }}",
                type: "POST",
                data: {
                    user_id: user_id,
                    cover_id: cover_id,
                },
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        })
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        })
                    }
                },
                error: function() {
                    alert('Error updating cover image.');
                }
            });
        }
    </script>

    <script>
        function updateAvatar(avatar_id, user_id) {
            $.ajax({
                url: "{{ route('profile.avatar') }}",
                type: "POST",
                data: {
                    user_id: user_id,
                    avatar_id: avatar_id,
                },
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        })
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        })
                    }
                },
                error: function() {
                    alert('Error updating cover image.');
                }
            });
        }
    </script>

    <script>
        function addAvatar() {
            let formData = new FormData();
            formData.append('avatar', $('#add_avatar')[0].files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('avatar.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        })
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message,
                        })
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function() {
                    new Notyf().error('An error occurred.');
                }
            });
        }
    </script>

    <script>
        function deleteData(link) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            })
        }
    </script>

    <script>
        async function deleteAccount() {
            const {
                value: password
            } = await Swal.fire({
                icon: 'info',
                title: "Are you sure you want to delete your account?",
                input: "password",
                inputLabel: "Enter your password",
                inputPlaceholder: "Enter your password",
                inputAttributes: {
                    maxlength: "100",
                    autocapitalize: "off",
                    autocorrect: "off"
                },
                confirmButtonText: "Yes",
                showCancelButton: true,
                cancelButtonText: "No"
            });

            if (password) {
                let formData = new FormData();
                formData.append('password', password);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('profile.destroy') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) { // Handle incorrect password case
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Incorrect Password',
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Something Went Wrong',
                            });
                        }
                    }
                });
            }
        }
    </script>

    <script>
        let profileUsernameTimeout;

        function checkProfileUsernameAvailability() {
            const username = document.getElementById('username').value.trim();
            const status = document.getElementById('username-status');

            if (!username) {
                status.textContent = '';
                status.className = 'form-text';
                return;
            }

            clearTimeout(profileUsernameTimeout);
            profileUsernameTimeout = setTimeout(function () {
                $.ajax({
                    url: "{{ route('username.check') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        username: username,
                        exclude_id: '{{ Auth::id() }}',
                    },
                    success: function (response) {
                        status.textContent = response.message;
                        status.className = response.available ? 'form-text text-success' : 'form-text text-danger';
                    },
                    error: function () {
                        status.textContent = 'Unable to verify username at this time.';
                        status.className = 'form-text text-danger';
                    }
                });
            }, 500);
        }

        document.addEventListener('DOMContentLoaded', function () {
            var usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.addEventListener('input', checkProfileUsernameAvailability);
                checkProfileUsernameAvailability();
            }
        });
    </script>
@endpush
