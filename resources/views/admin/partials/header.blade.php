<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="index.html" class="logo-light">
                    <span class="logo-lg">
                        <img src="{{ asset($admin->logo) }}" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset($admin->logo_sm) }}" alt="small logo">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="index.html" class="logo-dark">
                    <span class="logo-lg">
                        <img src="{{ asset($admin->logo_dark) }}" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="{{ asset($admin->logo_sm) }}" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="ri-menu-2-fill"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <!-- Topbar Search Form -->
            <div class="app-search dropdown d-none d-lg-block">
                {{-- @php
                    $menuItems = [
                        ['icon' => 'ri-dashboard-2-fill', 'name' => 'Dashboard', 'route' => route('dashboard')],
                        ['icon' => 'ri-layout-3-fill', 'name' => 'Product Entry', 'route' => route('dashboard')],
                        ['icon' => 'ri-bubble-chart-fill', 'name' => 'Orders', 'route' => route('dashboard')],
                        ['icon' => 'ri-group-2-fill', 'name' => 'Customers', 'route' => route('dashboard')],
                        ['icon' => 'ri-settings-4-fill', 'name' => 'Settings', 'route' => route('dashboard')],
                    ];
                @endphp --}}

                <form>
                    <div class="input-group">
                        <input type="search" class="form-control dropdown-toggle" placeholder="Search..."
                            id="top-search" autocomplete="off">
                        <span class="ri-search-line search-icon"></span>
                    </div>
                </form>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                    @foreach ($menuItems as $item)
                        <a href="{{ $item['url'] }}" class="dropdown-item notify-item search-item"
                            style="display: none;">
                            <i class="{{ $item['icon'] }} fs-16 me-1"></i>
                            <span>{{ $item['name'] }}</span>
                        </a>
                    @endforeach
                    <a href="javascript:void(0);" class="dropdown-item notify-item" id="search-access">
                        <i class="ri-search-2-line fs-16 me-1"></i>
                        <span>Search For Quick Access</span>
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item notify-item" id="no-links"
                        style="display: none;">
                        <i class="ri-emotion-sad-line fs-16 me-1"></i>
                        <span>No Links Found</span>
                    </a>
                </div>
            </div>


        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line fs-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search For Quick Access ..."
                            id="mobile-search">
                    </form>
                    <div class="mobile-quick-links-content p-2" style="display: none">
                        @foreach ($menuItems as $item)
                            <a href="{{ $item['url'] }}" class="dropdown-item notify-item search-mobile-item"
                                style="display: none;">
                                <i class="{{ $item['icon'] }} fs-16 me-1"></i>
                                <span>{{ $item['name'] }}</span>
                            </a>
                        @endforeach
                        <a href="javascript:void(0);" class="dropdown-item notify-item" id="no-mov-links"
                            style="display: none;">
                            <i class="ri-emotion-sad-line fs-16 me-1"></i>
                            <span>No Links Found</span>
                        </a>
                    </div>
                </div>
            </li>

            {{-- <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('admin') }}/assets/images/flags/us.jpg" alt="user-image" class="me-0 me-sm-1"
                        height="12">
                    <span class="align-middle d-none d-lg-inline-block">English</span> <i
                        class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{ asset('admin') }}/assets/images/flags/germany.jpg" alt="user-image"
                            class="me-1" height="12">
                        <span class="align-middle">German</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{ asset('admin') }}/assets/images/flags/italy.jpg" alt="user-image" class="me-1"
                            height="12">
                        <span class="align-middle">Italian</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{ asset('admin') }}/assets/images/flags/spain.jpg" alt="user-image" class="me-1"
                            height="12">
                        <span class="align-middle">Spanish</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{ asset('admin') }}/assets/images/flags/russia.jpg" alt="user-image"
                            class="me-1" height="12">
                        <span class="align-middle">Russian</span>
                    </a>

                </div>
            </li> --}}

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <i class="ri-notification-3-fill fs-22"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="noti-icon-badge"></span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fs-16 fw-medium"> Notification</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('clearAllNotifications') }}"
                                    class="text-dark text-decoration-underline">
                                    <small>Clear All</small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div style="max-height: 300px;" data-simplebar>
                        @php
                            use Carbon\Carbon;
                         
                            $notifications = auth()
                                ->user()
                                // ->unreadNotifications
                                ->notifications->groupBy(function ($notification) {
                                    $createdAt = Carbon::parse($notification->created_at);
                                    if ($createdAt->isToday()) {
                                        return 'Today';
                                    } elseif ($createdAt->isYesterday()) {
                                        return 'Yesterday';
                                    } else {
                                        return $createdAt->format('d M Y'); // Example: "31 Jan 2023"
                                    }
                                });
                        @endphp


                        @forelse ($notifications as $date => $group)
                            <h5 class="text-muted fs-12 fw-bold p-2 text-uppercase mb-0">{{ $date }}</h5>

                            @foreach ($group as $notification)
                                <a href="javascript:void(0);" onclick="markAsRead('{{ $notification->id }}')"
                                    class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="notify-icon bg-{{ $notification->data['type'] ?? 'primary' }}">
                                                    <i class="{{ $notification->data['icon'] ?? 'ri-notification-3-line' }} fs-18"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 text-truncate ms-2">
                                                <h5 class="noti-item-title fw-medium fs-14">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                    <small class="fw-normal text-muted float-end ms-1">
                                                        {{ Carbon::parse($notification->created_at)->diffForHumans() }}
                                                    </small>
                                                </h5>
                                                <small class="noti-item-subtitle text-muted">
                                                    {{ $notification->data['message'] ?? 'No message' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @empty
                                <div class="text-center">
                                    No Notification Found
                                </div>
                        @endforelse
                    </div>

                    <!-- All-->
                    <a href="{{ route('markAllNotificationsRead') }}"
                        class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                        Mark All Read
                    </a>

                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-fill fs-22"></i>
                </div>
            </li>


            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="#" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown me-md-2">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="user-image"
                            width="32" class="rounded-circle">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">{{ Auth::user()->name }}</h5>
                        <h6 class="my-0 fw-normal" style="text-transform: capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned'  }}</h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    <!-- item-->
                    <a href="{{ route('profile.index') }}" class="dropdown-item">
                        <i class="ri-account-circle-fill align-middle me-1"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="{{ route('lock.screen') }}" class="dropdown-item">
                        <i class="ri-lock-password-fill align-middle me-1"></i>
                        <span>Lock Screen</span>
                    </a>

                    <!-- item-->
                    <a href="{{ route('profile.password') }}" class="dropdown-item">
                        <i class="ri-key-fill align-middle me-1"></i>
                        <span>Change Password</span>
                    </a>

                    <!-- item-->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ri-logout-box-fill align-middle me-1"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>
