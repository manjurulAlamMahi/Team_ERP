<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset($admin->logo_sm) }}">

<!-- Daterangepicker css -->
<link rel="stylesheet" href="{{ asset('admin') }}/assets/vendor/daterangepicker/daterangepicker.css">

<!-- Vector Map css -->
<link rel="stylesheet"
    href="{{ asset('admin') }}/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">

<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">

<!-- Theme Config Js -->
<script src="{{ asset('admin') }}/assets/js/config.js"></script>

<!-- App css -->
<link href="{{ asset('admin') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

<!-- Icons css -->
<link href="{{ asset('admin') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

<style>
    .drp-buttons .applyBtn {
        display: none;
    }
</style>

<style>
    .dashboard-date {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .dashboard-date i {
        color: #007bff;
        font-size: 18px;
        padding-top: 2px;
    }

    #timer {
        font-weight: bold;
        color: #d9534f;
    }

    @media (max-width: 768px) {
        .dashboard-date {
            gap: 3px;
            font-size: 14px;
            font-weight: 400;
            padding: 8px 10px;
        }
        .page-title-box .page-title{
            text-align: center
        }
    }
</style>

<style>
    /* The sidebar's menu.js adds class "active" to whichever <a> matches the current URL, and
       class "menuitem-active" to that link's parent <li> (and to every ancestor toggle <li>
       for nested dropdowns). So ".menuitem-active>a" alone covers the active leaf link inside
       a dropdown AND the toggle row of any dropdown that contains it - same full brand-colored
       background + white text as a top-level active link, so it's unmistakable what's selected. */
    .side-nav .menuitem-active>a,
    .side-nav .menuitem-active>a:active,
    .side-nav .menuitem-active>a:focus,
    .side-nav .menuitem-active>a:hover {
        background: var(--ct-menu-item-active-bg) !important;
        color: #fff !important;
        border-radius: 4px;
    }

    /* Soft hover highlight on dropdown links so they feel interactive even before they're active */
    .side-nav-second-level a:not(.active):hover,
    .side-nav-third-level a:not(.active):hover,
    .side-nav-forth-level a:not(.active):hover {
        background: rgba(0, 171, 12, 0.08);
    }

    /* Dropdown (submenu) items: add breathing room so consecutive links don't feel glued together */
    .side-nav-second-level li,
    .side-nav-second-level .side-nav-item,
    .side-nav-third-level li,
    .side-nav-third-level .side-nav-item,
    .side-nav-forth-level li,
    .side-nav-forth-level .side-nav-item {
        margin-bottom: 4px;
    }

    .side-nav-second-level li:last-child,
    .side-nav-second-level .side-nav-item:last-child,
    .side-nav-third-level li:last-child,
    .side-nav-third-level .side-nav-item:last-child,
    .side-nav-forth-level li:last-child,
    .side-nav-forth-level .side-nav-item:last-child {
        margin-bottom: 0;
    }

    .side-nav-second-level li a,
    .side-nav-second-level li .side-nav-link,
    .side-nav-second-level .side-nav-item a,
    .side-nav-second-level .side-nav-item .side-nav-link,
    .side-nav-third-level li a,
    .side-nav-third-level li .side-nav-link,
    .side-nav-third-level .side-nav-item a,
    .side-nav-third-level .side-nav-item .side-nav-link,
    .side-nav-forth-level li a,
    .side-nav-forth-level li .side-nav-link,
    .side-nav-forth-level .side-nav-item a,
    .side-nav-forth-level .side-nav-item .side-nav-link {
        padding-top: calc(var(--ct-menu-item-padding-y) * .85);
        padding-bottom: calc(var(--ct-menu-item-padding-y) * .85);
        border-radius: 4px;
    }
</style>

<style>
    /* Brand color override: #00AB0C replacing the default theme blue */
    :root,
    [data-bs-theme=light] {
        --ct-primary: #00AB0C;
        --ct-blue: #00AB0C;
        --ct-indigo: #00AB0C;
        --ct-primary-rgb: 0, 171, 12;
        --ct-primary-text-emphasis: #00780a;
        --ct-primary-bg-subtle: #e6f7e6;
        --ct-primary-border-subtle: #b3e0b6;
        --ct-menu-item-active-bg: #00AB0C;
    }

    html[data-menu-color=light] {
        --ct-menu-item-hover-color: #00AB0C;
        --ct-menu-item-active-color: #00AB0C;
        --ct-help-box-bg: #00AB0C;
    }

    html[data-menu-color=brand] {
        --ct-menu-bg: linear-gradient(135deg, #00AB0C 0%, #00780a 60%);
    }

    html[data-topbar-color=light] {
        --ct-topbar-item-hover-color: #00AB0C;
    }

    html[data-topbar-color=brand] {
        --ct-topbar-bg: #00AB0C;
    }
</style>

@stack('style')
