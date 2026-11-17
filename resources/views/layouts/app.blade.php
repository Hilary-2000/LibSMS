{{--
    Master layout for library pages: head, top bar, sidebar, footer and the shared scripts.

    @extends('layouts.app', ['active' => 'Circulation'])   // menu entry to highlight (optional)
    @section('title', 'Reservations')
    @section('page_title', 'Reservations')                  // heading + breadcrumb
    @section('content') ... @endsection
    @push('styles') / @push('scripts')                       // page-specific assets

    Every asset path is absolute (/assets/...) so pages work at any URL depth. Controllers must
    pass $notifications and $notification_count (the "notifications" middleware provides them).
--}}
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>@yield('title') | {{ ucwords(strtolower(session("fullname"))) }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Library Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
        <link rel="apple-touch-icon" href="{{ asset('images/ladybird-apple-touch.png') }}?v=4">

        <link href="/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <script src="/assets/js/plugin.js"></script>
        <link href="/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        @stack('styles')
    </head>

    <body data-sidebar="dark">
        <div id="preloader">
            <div id="status">
                <div class="spinner-chase">
                    <div class="chase-dot"></div><div class="chase-dot"></div><div class="chase-dot"></div>
                    <div class="chase-dot"></div><div class="chase-dot"></div><div class="chase-dot"></div>
                </div>
            </div>
        </div>

        <div id="layout-wrapper">
            @include('partials.topbar')

            <!-- ========== Left Sidebar Start ========== -->
            @include('partials.sidebar', ['active' => $active ?? null])
            <!-- Left Sidebar End -->

            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">@yield('page_title')</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="/Dashboard">Dashboard</a></li>
                                            <li class="breadcrumb-item active">@yield('page_title')</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @foreach (["success" => "success", "error" => "danger"] as $flash => $style)
                            @if (session($flash))
                                <div class="alert alert-{{ $style }} alert-dismissible fade show" role="alert">
                                    {{ session($flash) }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        @endforeach

                        @yield('content')
                    </div>
                </div>

                @include('partials.footer')
            </div>
        </div>

        @include('partials.rightbar')
        <div class="rightbar-overlay"></div>

        <script src="/assets/libs/jquery/jquery.min.js"></script>
        <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/assets/libs/node-waves/waves.min.js"></script>
        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        @stack('scripts')
        <script src="/assets/js/app.js"></script>
    </body>
</html>
