<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    @include('admin.partials.style')
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">


        <!-- ========== Topbar Start ========== -->
        @include('admin.partials.header')
        <!-- ========== Topbar End ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.partials.sidebar')
        <!-- ========== Left Sidebar End ========== -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">


                    <div class="row">
                        <div class="col-12">
                            <div
                                class="page-title-box justify-content-between d-flex align-items-lg-center flex-lg-row flex-column">
                                <h4 class="page-title">@yield('title')</h4>

                                <form class="d-flex mb-xxl-0 mb-2">
                                    <div class="input-group">
                                        <p class="dashboard-date mb-0"> <i
                                                class="mdil mdil-calendar"></i>{{ date('D d M Y') }}<i
                                                class="mdil mdil-clock"></i> <span id="timer"></span></p>
                                        <span id="dash-daterange"
                                            class="input-group-text bg-primary border-primary text-white">
                                            <i class="ri-calendar-todo-fill fs-13"></i>
                                        </span>
                                    </div>
                                    <a href="javascript:location.reload();" class="btn btn-info ms-1">
                                        <i class="ri-refresh-line"></i>
                                    </a>

                                    @if (App\Models\QuickAccessMenu::where('route', Route::currentRouteName())->where('user_id', Auth::user()->id)->exists())
                                        <a href="{{ route('remove.quick.access',Route::currentRouteName()) }}" class="btn btn-danger ms-1"
                                            title="Quick Access ">
                                            <i class="mdi mdi-star-minus-outline"></i>
                                        </a>
                                    @else
                                        <a href="javascript:void(0);" data-icon="@yield('quickAccessicon')"
                                            class="btn btn-success ms-1" data-bs-toggle="modal"
                                            data-bs-target="#bs-example-modal-sm" id="quick-access-link">
                                            <i class="ri-star-line"></i>
                                        </a>
                                    @endif

                                </form>
                            </div>
                        </div>
                    </div>

                    @yield('content')

                </div>
            </div>


            <!-- Footer Start -->
            @include('admin.partials.footer')
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->


    <!-- Modal -->
    @include('admin.partials.modal')


    <!-- Theme Settings -->
    {{-- @include('admin.partials.theme') --}}

    @include('admin.partials.script')



</body>

</html>
