<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="{{ route('dashboard') }}" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset($admin->logo) }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset($admin->logo_sm) }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="{{ route('dashboard') }}" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset($admin->logo_dark) }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset($admin->logo_sm) }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user p-3 text-white"
            style=" background: url('{{ asset(Auth::user()->cover) }}') center center;">
            <a href="{{ route('profile.index') }}" class="d-flex align-items-center text-reset">
                <div class="flex-shrink-0">
                    <img src="{{ asset(Auth::user()->avatar) }}" alt="user-image" height="42"
                        class="rounded-circle shadow">
                </div>
                <div class="flex-grow-1 ms-2">
                    <span class="fw-semibold fs-15 d-block">{{ Auth::user()->name }}</span>
                    <span class="fs-13">{{ Auth::user()->getRoleNames()->first() ?? 'No Role Assigned' }}</span>
                </div>
                <div class="ms-auto">
                    <i class="ri-arrow-right-s-fill fs-20"></i>
                </div>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title mt-1"> Main</li>

            <li class="side-nav-item {{ Route::is('dashboard') }}">
                <a href="{{ route('dashboard') }}" class="side-nav-link active">
                    <i class="ri-dashboard-2-fill"></i>
                    <span> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-item">
                @php
                    $msg = App\Models\Chat::where('receiver_id', Auth::user()->id)
                        ->where('read', 0)
                        ->count();
                @endphp
                <a href="{{ route('dashboard.inbox') }}" class="side-nav-link">
                    <i class="ri-chat-voice-fill"></i>
                    @if ($msg > 0)
                        <span class="badge bg-purple float-end">{{ $msg }}</span>
                    @endif
                    <span> Inbox </span>
                </a>
            </li>
            @if (Auth::user()->team_id)
                <li class="side-nav-title mt-2">Manage Team</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarManageTeam" aria-expanded="false" aria-controls="sidebarManageTeam"
                        class="side-nav-link {{ Route::is('leader.*') ? 'active' : '' }}">
                        <i class="ri-team-line"></i>
                        <span> Manage Team </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarManageTeam">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('leader.team.stats') }}">Team Stats</a>
                            </li>
                            <li>
                                <a href="{{ route('leader.my.team') }}">My Team</a>
                            </li>
                            @if (Auth::user()->hasRole('Leader'))
                                <li>
                                    <a href="{{ route('leader.add.member') }}">Add Member</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            @if (Auth::user()->team_id)
                <li class="side-nav-title mt-2">Client Messages</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarClientMessages" aria-expanded="false" aria-controls="sidebarClientMessages"
                        class="side-nav-link {{ Route::is('client.message.*') ? 'active' : '' }}">
                        <i class="ri-customer-service-2-line"></i>
                        <span> Client Messages </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarClientMessages">
                        <ul class="side-nav-second-level">
                            @if (Auth::user()->hasAnyRole(['Stack Lead', 'Member', 'Probation']))
                                <li>
                                    <a href="{{ route('client.message.create') }}">Send Message</a>
                                </li>
                                <li>
                                    <a href="{{ route('client.message.my.list') }}">My Messages</a>
                                </li>
                            @endif
                            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader']))
                                @php
                                    $pendingClientMessageCount = \App\Models\ClientMessage::where('team_id', Auth::user()->team_id)->where('status', 'pending')->count();
                                @endphp
                                <li>
                                    <a href="{{ route('client.message.review.list') }}">Pending Review
                                        @if ($pendingClientMessageCount > 0)
                                            <span class="badge bg-danger float-end">{{ $pendingClientMessageCount > 9 ? '9+' : $pendingClientMessageCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('client.message.review.history') }}">Review History</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            @if (Auth::user()->team_id)
                <li class="side-nav-title mt-2">Today's Plan</li>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarTodayPlan" aria-expanded="false" aria-controls="sidebarTodayPlan"
                        class="side-nav-link {{ Route::is('today.plan.*') ? 'active' : '' }}">
                        <i class="ri-calendar-todo-line"></i>
                        <span> Today's Plan </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarTodayPlan">
                        <ul class="side-nav-second-level">
                            @if (Auth::user()->hasAnyRole(['Co Leader', 'Stack Lead', 'Member', 'Probation']))
                                <li>
                                    <a href="{{ route('today.plan.create') }}">Submit Plan</a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('today.plan.my.plans') }}">My Plans</a>
                            </li>
                            @if (Auth::user()->hasRole('Leader'))
                                @php
                                    $pendingTodayPlanCount = \App\Models\TodayPlanTask::where('team_id', Auth::user()->team_id)
                                        ->where('source', 'planned')
                                        ->where('status', 'pending')
                                        ->count();
                                @endphp
                                <li>
                                    <a href="{{ route('today.plan.review.list') }}">Pending Review
                                        @if ($pendingTodayPlanCount > 0)
                                            <span class="badge bg-danger float-end">{{ $pendingTodayPlanCount > 9 ? '9+' : $pendingTodayPlanCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('today.plan.review.history') }}">Review History</a>
                                </li>
                                <li>
                                    <a href="{{ route('today.plan.dashboard') }}">Team Dashboard</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCharts" aria-expanded="false" aria-controls="sidebarCharts"
                    class="side-nav-link">
                    <i class="ri-bubble-chart-fill"></i>
                    <span> Module </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCharts">
                    <ul class="side-nav-second-level">
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarApexCharts" aria-expanded="false"
                                aria-controls="sidebarApexCharts">
                                <span> Module 01 </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarApexCharts">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="#">Test</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarChartJSCharts" aria-expanded="false"
                                aria-controls="sidebarChartJSCharts">
                                <span> Module 02 </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarChartJSCharts">
                                <ul class="side-nav-third-level">
                                    <li>
                                        <a href="charts-chartjs-area.html">Test</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            @canany(['setting_events', 'setting_admin', 'setting_roleManagement', 'user_request', 'user_list', 'user_create', 'community_list', 'team_list', 'client_message_type_list', 'client_message_type_create'])
            <li class="side-nav-title mt-2">Administrator</li>

            {{-- Organization Control --}}
            @canany(['community_list', 'team_list', 'community_create', 'team_create'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarOrganizationControl" aria-expanded="false" aria-controls="sidebarOrganizationControl"
                        class="side-nav-link">
                        <i class="ri-briefcase-4-line"></i>
                        <span> Organization Control </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarOrganizationControl">
                        <ul class="side-nav-second-level">
                            @can('community_list')
                                <li>
                                    <a href="{{ route('community.list') }}">Communities</a>
                                </li>
                            @endcan
                            @can('team_list')
                                <li>
                                    <a href="{{ route('team.list') }}">Teams</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcanany

            {{-- Client Message Types --}}
            @canany(['client_message_type_list', 'client_message_type_create'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarClientMessageTypes" aria-expanded="false" aria-controls="sidebarClientMessageTypes"
                        class="side-nav-link">
                        <i class="ri-file-list-3-line"></i>
                        <span> Client Message Types </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarClientMessageTypes">
                        <ul class="side-nav-second-level">
                            @can('client_message_type_list')
                                <li>
                                    <a href="{{ route('client.message.type.list') }}">Message Types</a>
                                </li>
                            @endcan
                            @can('client_message_type_create')
                                <li>
                                    <a href="{{ route('client.message.type.create') }}">Add Type</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcanany

            {{-- Users Management --}}
            @canany(['user_request', 'user_list', 'user_create'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarUsersManagement" aria-expanded="false" aria-controls="sidebarUsersManagement"
                        class="side-nav-link">
                        <i class="ri-user-settings-fill"></i>
                        <span> Users Management</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarUsersManagement">
                        <ul class="side-nav-second-level">
                            @can('user_request')
                                <li>
                                    <a href="{{ route('user.request') }}">User Request <span
                                            class="badge bg-success float-end">{{ App\Models\User::where('is_request', true)->count() > 9 ? '9+' : App\Models\User::where('is_request', true)->count() }}</span></a>
                                </li>
                            @endcan
                            @can('user_list')
                                <li>
                                    <a href="{{ route('user.list') }}">Users List</a>
                                </li>
                            @endcan
                            @can('user_create')
                                <li>
                                    <a href="{{ route('user.create') }}">Create User</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcanany

            {{-- Site Setting --}}
            @canany(['setting_events', 'setting_admin', 'setting_roleManagement'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#siteSetting" aria-expanded="false" aria-controls="sidebarPages"
                        class="side-nav-link">
                        <i class="ri-settings-5-fill"></i>
                        <span> Site Setting </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="siteSetting">
                        <ul class="side-nav-second-level">
                            @can('setting_events')
                                <li>
                                    <a href="{{ route('events.index') }}">Events Entry</a>
                                </li>
                            @endcan
                            @can('setting_admin')
                                <li>
                                    <a href="{{ route('setting.admin') }}">Admin Setting</a>
                                </li>
                            @endcan
                            @can('setting_mail')
                                <li>
                                    <a href="{{ route('setting.mail') }}">Mail Setting</a>
                                </li>
                            @endcan
                            @can('setting_roleManagement')
                                <li>
                                    <a href="{{ route('role.index') }}">Roles</a>
                                </li>
                                <li>
                                    <a href="{{ route('role.index') }}">Permissions</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>

            @endcanany
            @endcanany

            <li class="side-nav-title mt-2">Account</li>

            <li class="side-nav-item">
                <a href="{{ route('profile.index') }}" class="side-nav-link">
                    <i class="ri-account-circle-fill"></i>
                    <span> My Profile </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('profile.password') }}" class="side-nav-link">
                    <i class="ri-key-fill"></i>
                    <span> Change Password </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('lock.screen') }}" class="side-nav-link">
                    <i class="ri-lock-password-fill"></i>
                    <span> Lock Screen </span>
                </a>
            </li>

            <li class="side-nav-item">
                <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                    @csrf
                </form>
                <a class="side-nav-link" href="#"
                    onclick="document.getElementById('logoutForm').submit(); return false;">
                    <i class="ri-logout-box-fill"></i>
                    <span> Log Out </span>
                </a>
            </li>


        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
