<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    @include('auth.partials.style')

</head>

<body class="authentication-bg pb-0">

    @if(!Route::is('login'))
    @include('auth.partials.common_bg')
    @endif

    @yield('content')


   @include('auth.partials.footer')

</body>

</html>
