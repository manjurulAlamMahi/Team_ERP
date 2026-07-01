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
                    <span> Inbox </span>
                    @if ($msg > 0)
                        <span class="badge bg-purple ms-auto">{{ $msg }}</span>
                    @endif
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('todo.list') }}" class="side-nav-link {{ Route::is('todo.list') ? 'active' : '' }}">
                    <i class="ri-checkbox-multiple-line"></i>
                    <span> To Do List </span>
                </a>
            </li>

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

            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead', 'Member', 'Probation']))
                <li class="side-nav-title mt-2">Teams</li>

                {{-- 1. Daily Issue --}}
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarDailyIssue" aria-expanded="false" aria-controls="sidebarDailyIssue"
                        class="side-nav-link {{ Route::is('daily.issue.*') ? 'active' : '' }}">
                        <i class="ri-alert-line"></i>
                        <span> Daily Issue </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Route::is('daily.issue.*') ? 'show' : '' }}" id="sidebarDailyIssue">
                        <ul class="side-nav-second-level">
                            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']))
                                <li>
                                    <a href="{{ route('daily.issue.create') }}">Add Issue</a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('daily.issue.my') }}">My Issues</a>
                            </li>
                            @php
                                $pendingDailyIssueCount = \App\Models\DailyIssue::where('team_id', Auth::user()->team_id)
                                    ->where('status', 'pending')
                                    ->count();
                            @endphp
                            <li>
                                <a href="{{ route('daily.issue.list') }}">All Issues
                                    @if ($pendingDailyIssueCount > 0)
                                        <span class="badge bg-danger float-end">{{ $pendingDailyIssueCount > 9 ? '9+' : $pendingDailyIssueCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('daily.issue.completed') }}">Completed Issues</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- 2. Daily Task --}}
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarDailyTask" aria-expanded="false" aria-controls="sidebarDailyTask"
                        class="side-nav-link {{ Route::is('daily.task.*') ? 'active' : '' }}">
                        <i class="ri-task-line"></i>
                        <span> Daily Task </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Route::is('daily.task.*') ? 'show' : '' }}" id="sidebarDailyTask">
                        <ul class="side-nav-second-level">
                            <li>
                                <a href="{{ route('daily.task.add') }}" class="{{ Route::is('daily.task.add') ? 'active' : '' }}">Add Task</a>
                            </li>
                            <li>
                                <a href="{{ route('daily.task.my') }}" class="{{ Route::is('daily.task.my') ? 'active' : '' }}">My Tasks</a>
                            </li>
                            @if (Auth::user()->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']))
                                <li>
                                    <a href="{{ route('daily.task.assign') }}" class="{{ Route::is('daily.task.assign') ? 'active' : '' }}">Assign Task</a>
                                </li>
                                <li>
                                    <a href="{{ route('daily.task.all') }}" class="{{ Route::is('daily.task.all') ? 'active' : '' }}">All Tasks</a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('daily.task.completed') }}" class="{{ Route::is('daily.task.completed') ? 'active' : '' }}">Completed Tasks</a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- 3. Client Messages --}}
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarClientMessages" aria-expanded="false" aria-controls="sidebarClientMessages"
                        class="side-nav-link {{ Route::is('client.message.*') ? 'active' : '' }}">
                        <i class="ri-customer-service-2-line"></i>
                        <span> Client Messages </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Route::is('client.message.*') ? 'show' : '' }}" id="sidebarClientMessages">
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

                {{-- 4. Manage Team --}}
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarManageTeam" aria-expanded="false" aria-controls="sidebarManageTeam"
                        class="side-nav-link {{ Route::is('leader.*') ? 'active' : '' }}">
                        <i class="ri-team-line"></i>
                        <span> Manage Team </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Route::is('leader.*') ? 'show' : '' }}" id="sidebarManageTeam">
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

            {{--
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
            --}}

             <li class="side-nav-title mt-2">Setting</li>

            {{-- User Management --}}
            @canany(['user_request', 'user_list', 'user_create'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarUserManagement" aria-expanded="false" aria-controls="sidebarUserManagement"
                        class="side-nav-link">
                        <i class="ri-user-settings-line"></i>
                        <span> User Management </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarUserManagement">
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
            @canany(['setting_events', 'setting_admin', 'setting_mail', 'setting_roleManagement'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarSiteSetting" aria-expanded="false" aria-controls="sidebarSiteSetting"
                        class="side-nav-link">
                        <i class="ri-settings-4-line"></i>
                        <span> Site Setting </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarSiteSetting">
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

{{-- Sidebar: badge-on-parent + active-state fix --}}
<style>
/* Active sidebar item uses the same visual as hover (not full background fill) */
.leftside-menu .side-nav-link.active:not([data-bs-toggle="collapse"]) {
    background-color: rgba(255, 255, 255, 0.1) !important;
    border-radius: 5px;
}

/* Parent link text turns to theme primary color when a child is active */
.leftside-menu .side-nav-item > a[data-bs-toggle="collapse"].active > span:first-of-type {
    color: #0ab39c !important;
}

/* Badge spacing fix — ensure badge and arrow don't overlap */
.leftside-menu .side-nav-link .sidebar-parent-badge {
    margin-right: 6px;
}
.leftside-menu .side-nav-link .menu-arrow {
    flex-shrink: 0;
}
</style>
<script>
(function () {
    'use strict';

    /* ── 1. Active-state: open the collapse whose child matches current URL ── */
    var currentPath = window.location.pathname;

    document.querySelectorAll('.side-nav-item').forEach(function (item) {
        var trigger  = item.querySelector(':scope > a[data-bs-toggle="collapse"]');
        var collapse = item.querySelector(':scope > .collapse');
        if (!trigger || !collapse) return;

        // Check every child link
        var matched = Array.from(collapse.querySelectorAll('a[href]')).some(function (a) {
            var href = a.getAttribute('href');
            return href && href !== '#' && href !== 'javascript:void(0);'
                && currentPath.startsWith(href.replace(window.location.origin, ''));
        });

        if (matched) {
            // Ensure parent link has active class
            trigger.classList.add('active');
            // Ensure collapse is open (Bootstrap 5)
            if (!collapse.classList.contains('show')) {
                var bs = bootstrap.Collapse.getOrCreateInstance(collapse, { toggle: false });
                bs.show();
            }
        }
    });

    /* ── 2. Badge-on-parent: copy child badges to parent when collapsed ───── */
    document.querySelectorAll('.side-nav-item').forEach(function (item) {
        var trigger  = item.querySelector(':scope > a[data-bs-toggle="collapse"]');
        var collapse = item.querySelector(':scope > .collapse');
        if (!trigger || !collapse) return;

        // Sum up any danger badges inside child items
        var total = 0;
        collapse.querySelectorAll('.badge.bg-danger').forEach(function (b) {
            var txt = b.textContent.trim();
            total += txt.includes('+') ? 10 : (parseInt(txt) || 0);
        });
        if (total === 0) return;

        // Create parent badge
        var pBadge = document.createElement('span');
        pBadge.className = 'badge bg-danger ms-1 sidebar-parent-badge';
        pBadge.style.fontSize = '10px';
        pBadge.textContent    = total > 9 ? '9+' : total;

        // Insert before menu-arrow
        var arrow = trigger.querySelector('.menu-arrow');
        arrow ? trigger.insertBefore(pBadge, arrow) : trigger.appendChild(pBadge);

        // Hide if already open
        if (collapse.classList.contains('show')) pBadge.style.display = 'none';

        // Toggle visibility with collapse state
        collapse.addEventListener('show.bs.collapse',  function () { pBadge.style.display = 'none'; });
        collapse.addEventListener('hide.bs.collapse',  function () { pBadge.style.display = ''; });
    });
})();
</script>
