@extends('admin.master')

@section('title', 'Profile — ' . $user->name)
@section('quickAccessicon', 'ri-user-line')

@push('style')
    <style>
        .profile-cover {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .profile-avatar-wrap {
            margin-top: -55px;
            padding: 0 24px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .profile-avatar-wrap img {
            border: 4px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        .info-row td { padding: 5px 8px 5px 0; vertical-align: top; }
        .info-row td:first-child { white-space: nowrap; color: #6c757d; font-size: .82rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
        .info-row td:last-child { color: #344050; font-size: .9rem; }
        .social-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 50px; font-size: .83rem; border: 1px solid; text-decoration: none; margin: 3px; }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">

            {{-- Cover + Avatar --}}
            <div class="card mb-3 overflow-hidden">
                <img class="profile-cover" src="{{ asset($user->cover) }}" alt="cover">

                <div class="profile-avatar-wrap mb-2">
                    <img src="{{ asset($user->avatar) }}" class="rounded-circle avatar-xl img-thumbnail" alt="{{ $user->name }}">
                    <div class="pt-2">
                        @if ($user->canMessage(Auth::user()))
                            <form action="{{ route('user.message.store') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill">
                                    <i class="ri-send-plane-line me-1"></i> Send Message
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="card-body pt-1 pb-3 px-4">
                    <h4 class="mb-0">{{ $user->name }}</h4>
                    <p class="text-muted mb-1">
                        {{ $user->designation ?? $user->getRoleNames()->first() ?? 'No Role' }}
                        @if ($user->employee_id)
                            &nbsp;·&nbsp; <span class="badge bg-light text-dark border">{{ $user->employee_id }}</span>
                        @endif
                    </p>

                    {{-- Online indicator --}}
                    @if ($user->is_online)
                        <span class="badge bg-success"><i class="ri-checkbox-blank-circle-fill me-1" style="font-size:.6rem"></i> Online</span>
                    @else
                        <span class="badge bg-secondary"><i class="ri-checkbox-blank-circle-fill me-1" style="font-size:.6rem"></i> Offline</span>
                    @endif
                </div>
            </div>

            <div class="row">
                {{-- Left column --}}
                <div class="col-md-5">

                    {{-- Organisation --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-uppercase fw-bold mb-3 text-muted fs-12"><i class="ri-building-line me-1"></i> Organisation</h6>
                            <table class="info-row w-100">
                                @if ($user->team)
                                    <tr>
                                        <td>Team</td>
                                        <td>{{ $user->team->name }}</td>
                                    </tr>
                                @endif
                                @if ($user->community)
                                    <tr>
                                        <td>Community</td>
                                        <td>{{ $user->community->name }}</td>
                                    </tr>
                                @endif
                                @if ($user->stack)
                                    <tr>
                                        <td>Stack</td>
                                        <td>{{ $user->stack->name }}</td>
                                    </tr>
                                @endif
                                @if ($user->reportingTo)
                                    <tr>
                                        <td>Reports To</td>
                                        <td>
                                            <a href="{{ route('user.profile', $user->reportingTo->username) }}">{{ $user->reportingTo->name }}</a>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>Role</td>
                                    <td>{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Social Links --}}
                    @if ($user->facebook || $user->whatsapp || $user->telegram || $user->discord || $user->linkedin || $user->github || $user->official_email)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="text-uppercase fw-bold mb-3 text-muted fs-12"><i class="ri-global-line me-1"></i> Social & Contact</h6>
                                <div>
                                    @if ($user->facebook)
                                        <a href="{{ $user->facebook }}" target="_blank" class="social-pill border-primary text-primary">
                                            <i class="ri-facebook-circle-fill"></i> Facebook
                                        </a>
                                    @endif
                                    @if ($user->whatsapp)
                                        <a href="https://wa.me/{{ $user->whatsapp }}" target="_blank" class="social-pill border-success text-success">
                                            <i class="ri-whatsapp-line"></i> WhatsApp
                                        </a>
                                    @endif
                                    @if ($user->telegram)
                                        <a href="https://t.me/{{ ltrim($user->telegram, '@') }}" target="_blank" class="social-pill border-info text-info">
                                            <i class="ri-telegram-line"></i> Telegram
                                        </a>
                                    @endif
                                    @if ($user->discord)
                                        <a href="javascript:void(0)" onclick="navigator.clipboard.writeText('{{ $user->discord }}').then(()=>Toast.fire({icon:'success',title:'Discord username copied!'}))"
                                            class="social-pill border-secondary text-secondary" title="{{ $user->discord }}">
                                            <i class="ri-discord-line"></i> Discord
                                        </a>
                                    @endif
                                    @if ($user->linkedin)
                                        <a href="{{ $user->linkedin }}" target="_blank" class="social-pill border-primary text-primary">
                                            <i class="ri-linkedin-fill"></i> LinkedIn
                                        </a>
                                    @endif
                                    @if ($user->github)
                                        <a href="{{ $user->github }}" target="_blank" class="social-pill border-dark text-dark">
                                            <i class="ri-github-fill"></i> GitHub
                                        </a>
                                    @endif
                                    @if ($user->official_email)
                                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $user->official_email }}" target="_blank"
                                            class="social-pill border-danger text-danger">
                                            <i class="ri-google-line"></i> Official Mail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Right column --}}
                <div class="col-md-7">

                    {{-- Personal Info --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-uppercase fw-bold mb-3 text-muted fs-12"><i class="ri-user-line me-1"></i> Personal Information</h6>
                            <table class="info-row w-100">
                                <tr>
                                    <td>Full Name</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>@{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                @if ($user->official_email)
                                    <tr>
                                        <td>Official Email</td>
                                        <td>{{ $user->official_email }}</td>
                                    </tr>
                                @endif
                                @if ($user->phone)
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                @endif
                                @if ($user->whatsapp)
                                    <tr>
                                        <td>WhatsApp</td>
                                        <td>{{ $user->whatsapp }}</td>
                                    </tr>
                                @endif
                                @if ($user->dob)
                                    <tr>
                                        <td>Date of Birth</td>
                                        <td>{{ \Carbon\Carbon::parse($user->dob)->format('d M Y') }}</td>
                                    </tr>
                                @endif
                                @if ($user->designation)
                                    <tr>
                                        <td>Designation</td>
                                        <td>{{ $user->designation }}</td>
                                    </tr>
                                @endif
                                @if ($user->address)
                                    <tr>
                                        <td>Address</td>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Employment Info --}}
                    @if ($user->joining_date || $user->probation_end_date || $user->telegram || $user->discord)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h6 class="text-uppercase fw-bold mb-3 text-muted fs-12"><i class="ri-briefcase-line me-1"></i> Employment</h6>
                                <table class="info-row w-100">
                                    @if ($user->joining_date)
                                        <tr>
                                            <td>Joining Date</td>
                                            <td>{{ \Carbon\Carbon::parse($user->joining_date)->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                    @if ($user->probation_end_date)
                                        <tr>
                                            <td>Probation End</td>
                                            <td>{{ \Carbon\Carbon::parse($user->probation_end_date)->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                    @if ($user->telegram)
                                        <tr>
                                            <td>Telegram</td>
                                            <td>{{ $user->telegram }}</td>
                                        </tr>
                                    @endif
                                    @if ($user->discord)
                                        <tr>
                                            <td>Discord</td>
                                            <td>{{ $user->discord }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection
