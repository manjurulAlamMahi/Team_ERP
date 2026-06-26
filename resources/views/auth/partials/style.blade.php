<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('admin') }}/assets/images/favicon.ico">
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<!-- Theme Config Js -->
<script src="{{ asset('admin') }}/assets/js/config.js"></script>

<!-- App css -->
<link href="{{ asset('admin') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

<!-- Icons css -->
<link href="{{ asset('admin') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />

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
