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

@stack('style')
