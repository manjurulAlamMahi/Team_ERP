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
    /* Submenu (dropdown) active items: color text only, keep background transparent */
    .side-nav .side-nav-second-level .menuitem-active>a,
    .side-nav .side-nav-third-level .menuitem-active>a,
    .side-nav .side-nav-forth-level .menuitem-active>a {
        background: transparent !important;
        color: var(--ct-menu-item-active-color) !important;
    }

    .side-nav .side-nav-second-level .menuitem-active>a:active,
    .side-nav .side-nav-second-level .menuitem-active>a:focus,
    .side-nav .side-nav-second-level .menuitem-active>a:hover,
    .side-nav .side-nav-third-level .menuitem-active>a:active,
    .side-nav .side-nav-third-level .menuitem-active>a:focus,
    .side-nav .side-nav-third-level .menuitem-active>a:hover,
    .side-nav .side-nav-forth-level .menuitem-active>a:active,
    .side-nav .side-nav-forth-level .menuitem-active>a:focus,
    .side-nav .side-nav-forth-level .menuitem-active>a:hover {
        background: transparent !important;
        color: var(--ct-menu-item-active-color) !important;
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
    /* Brand color override: green palette (#6FCF97 / #1F6F5F / #ffffff) replacing the default theme blue */
    :root,
    [data-bs-theme=light] {
        --ct-primary: #6FCF97;
        --ct-blue: #6FCF97;
        --ct-indigo: #6FCF97;
        --ct-primary-rgb: 111, 207, 151;
        --ct-primary-text-emphasis: #1F6F5F;
        --ct-primary-bg-subtle: #e8f7ee;
        --ct-primary-border-subtle: #b7e4c7;
    }

    html[data-menu-color=light] {
        --ct-menu-item-hover-color: #1F6F5F;
        --ct-menu-item-active-color: #1F6F5F;
        --ct-help-box-bg: #1F6F5F;
    }

    html[data-menu-color=brand] {
        --ct-menu-bg: linear-gradient(135deg, #6FCF97 0%, #1F6F5F 60%);
    }

    html[data-topbar-color=light] {
        --ct-topbar-item-hover-color: #1F6F5F;
    }

    html[data-topbar-color=brand] {
        --ct-topbar-bg: #1F6F5F;
    }
</style>

@stack('style')
