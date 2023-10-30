<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Circulation Stats | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="/images/ladybird_dark.png">

        <link href="/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

        <link href="/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="/assets/js/plugin.js"></script>

        <!-- DataTables -->
        <link href="/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    </head>

    <body data-sidebar="dark">

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->
        <!-- Loader -->
        <div id="preloader">
            <div id="status">
                <div class="spinner-chase">
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                    <div class="chase-dot"></div>
                </div>
            </div>
        </div>
        
        <!-- Begin page -->
        <div id="layout-wrapper">
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="." class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="/assets/images/logo.svg" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="/assets/images/logo-dark.png" alt="" height="17">
                                </span>
                            </a>

                            <a href="." class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="/images/ladybird_dark-removebg.png" alt="" height="40">
                                </span>
                                <span class="logo-lg">
                                    <img src="/images/ladybird_dark-removebg.png" alt="" height="100">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                    </div>

                    <div class="d-flex">

                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
        
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                                <i class="bx bx-fullscreen"></i>
                            </button>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-bell"></i>
                                <span class="badge bg-danger rounded-pill">{{count($notifications) > 99 ? "99+" : count($notifications)}}</span>
                            </button>

                            {{-- notification details --}}
                            <x-notification :notifications="$notifications"/>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="https://lsims.ladybirdsmis.com/sims/{{(session()->get("user_details")->profile_loc != null && strlen(trim(session()->get("user_details")->profile_loc)) > 0) ? session()->get("user_details")->profile_loc : "images/dp.png"}}"
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{session("gender") == "M" ? "Mr." : "Ms."}} {{explode(" ",ucwords(strtolower(session("fullname"))))[0]}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="/Logout"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                            </div>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                                <i class="bx bx-cog bx-spin"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </header>

            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">
                <div data-simplebar class="h-100">
                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        @php
                            $lib_priv = json_decode(session()->get("user_details")->library_previleges,true);
                            function isPresent($array,$needle){
                                if ($array == null) {
                                    return false;
                                }
                                for ($index=0; $index < count($array); $index++) { 
                                    if ($array[$index] == $needle) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        @endphp
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" key="t-menu">Menu</li>
                            {{-- dashboard --}}
                            <li class="">
                                <a href="/Dashboard" class="waves-effect">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-chat">Dashboard</span>
                                </a>
                            </li>
                            @if (isPresent($lib_priv,"Acquisition") || count($lib_priv) == 0)
                                <li>
                                    <a href="/Acquisitions" class="waves-effect">
                                        <i class="bx bx-add-to-queue"></i>
                                        <span key="t-file-manager">Acquisitions</span>
                                    </a>
                                </li>
                            @endif
                            @if (isPresent($lib_priv,"Cataloging") || count($lib_priv) == 0)
                                <li>
                                    <a href="/Cataloging" class="waves-effect">
                                        <i class="bx bx-file"></i>
                                        <span key="t-file-manager">Cataloging</span>
                                    </a>
                                </li>
                            @endif
                            @if (isPresent($lib_priv,"Circulation") || count($lib_priv) == 0)
                                <li class="mm-active">
                                    <a href="/Circulation" class="waves-effect">
                                        <i class="bx bx-rotate-left"></i>
                                        <span key="t-file-manager">Circulation</span>
                                    </a>
                                </li>
                            @endif
                            @if (isPresent($lib_priv,"Report") || count($lib_priv) == 0)
                                <li>
                                    <a href="/Reports" class="waves-effect">
                                        <i class="bx bxs-report"></i>
                                        <span key="t-file-manager">Reports</span>
                                    </a>
                                </li>
                            @endif
                            @if (isPresent($lib_priv,"Settings") || count($lib_priv) == 0)
                                <li class="">
                                    <a href="javascript: void(0);" class="waves-effect has-arrow " aria-expanded="false">
                                        <i class="bx bx-cog"></i>
                                        <span key="t-jobs">Settings</span>
                                    </a>
                                    <ul class="sub-menu" aria-expanded="false" style="height: 0px;">
                                        <li><a href="/Settings/User-mgmt" key="t-job-list"><i class="bx bxs-user"></i> User Management</a></li>
                                        <li><a href="/Settings/Library-mgmt" key="t-job-list"><i class="bx bxs-book"></i> Library Management</a></li>
                                    </ul>
                                </li>
                            @endif
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Circulation Stats</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="/Cataloging">Book Circulation</a></li>
                                            <li class="breadcrumb-item active">Book Circulation Stats</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card">
                                <div class="card-body border-bottom">
                                    @if (session("success"))
                                        <div class="alert alert-success alert-dismissible fade show my-2" role="alert">
                                            <i class="mdi mdi-check-all me-2"></i>
                                            {{session("success")}}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    @if (session("error"))
                                        <div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
                                            <i class="mdi mdi-check-all me-2"></i>
                                            {{session("error")}}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <a href="/Circulation" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                                    <hr>
                                    <p>
                                        <b>Note:</b>

                                    </p>
                                    <ul>
                                        <li>Get insights on how books have been circulating</li>
                                    </ul>
                                    <hr>
                                    <a href="/Circulation/Stats" class="btn btn-outline-primary mb-3"><i class="bx bx-reset"></i> Reset</a>
                                    <form class="row" method="GET" action="/Circulation/Stats">
                                        <div class="col-md-3">
                                            <label for="borrower_stats" class="form-control-label">Select Option</label>
                                            <select name="borrower_stats" id="borrower_stats" class="form-control">
                                                <option value="" hidden>Select an Option</option>
                                                <option {{(session("borrower_stats") && session("borrower_stats") == "borrower_stats") ? "selected" : ""}} value="borrower_stats">Highest Borrower</option>
                                                <option {{(session("borrower_stats") && session("borrower_stats") == "books_borrowed_stats") ? "selected" : ""}} value="books_borrowed_stats">Highest Borrowed Book</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 {{session("borrower_type_opt") ? "" : "d-none"}}" id="borrower_type">
                                            <label for="borrower_type_opt" class="form-control-label">Borrower Type</label>
                                            <select name="borrower_type_opt" id="borrower_type_opt" class="form-control">
                                                <option value="" hidden>Select an Option</option>
                                                <option {{(session("borrower_type_opt") && session("borrower_type_opt") == "staff") ? "selected" : ""}} value="staff">Staff</option>
                                                <option {{(session("borrower_type_opt") && session("borrower_type_opt") == "students") ? "selected" : ""}} value="students">Students</option>
                                                <option {{(session("borrower_type_opt") && session("borrower_type_opt") == "All") ? "selected" : ""}} value="All">All</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 {{session("borrower_period_opt") ? "" : "d-none"}}" id="borrowing_period">
                                            <label for="borrower_period_opt" class="form-control-label">Term</label>
                                            <select name="borrower_period_opt" id="borrower_period_opt" class="form-control">
                                                <option value="" hidden>Select Term</option>
                                                <option {{(session("borrower_period_opt") && session("borrower_period_opt") == "All Year") ? "selected" : ""}} value="All Year">All Year</option>
                                                <option {{(session("borrower_period_opt") && session("borrower_period_opt") == "TERM_1") ? "selected" : ""}} value="TERM_1">Term 1</option>
                                                <option {{(session("borrower_period_opt") && session("borrower_period_opt") == "TERM_2") ? "selected" : ""}} value="TERM_2">Term 2</option>
                                                <option {{(session("borrower_period_opt") && session("borrower_period_opt") == "TERM_3") ? "selected" : ""}} value="TERM_3">Term 3</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3" id="student_lists">
                                            <label for="student_details" class="form-control-label">Select Class</label>
                                            <input type="hidden" value="{{((session("borrower_stats") && session("borrower_stats") != "books_borrowed_stats") && session("student_details")) ? session("student_details") : "null"}}" id="student_dets">
                                            <select name="student_details" id="student_details" class="select2 form-control">
                                                <option value="-" hidden>Select Class</option>
                                                <option {{(session("student_details") && session("student_details") == "whole school") ? "selected" : ""}} value="whole school" hidden>Whole School</option>
                                                @for ($i = 0; $i < count($student_detail); $i++)
                                                    <option {{(session("student_details") && session("student_details") == $student_detail[$i]->class_value) ? "selected" : ""}} value="{{$student_detail[$i]->class_value}}">{{$student_detail[$i]->class_name}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3 my-1">
                                            <button type="submit" class="btn btn-primary mt-3"><i class="bx bx-stats"></i> Show Stats</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @if (isset($display_data))
                                <div class="card">
                                    <div class="card-body border-bottom">
                                        <h6 class="text-center">Circulation Stats Table</h6>
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                <thead>
                                                    <tr>
                                                        
                                                        @for ($i = 0; $i < count($table_column); $i++)
                                                            <th>{{$table_column[$i]}}</th>
                                                        @endfor
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     @for ($i = 0; $i < count($display_data); $i++)
                                                        <tr>
                                                            @php
                                                                $counter = 0;
                                                            @endphp
                                                            <td>{{$i+1}}</td>
                                                            @foreach ($display_data[$i] as $key => $value)
                                                                @php
                                                                    $counter++;
                                                                @endphp
                                                                {{-- <td>{{$value}}</td> --}}
                                                                @if ($counter < count(get_object_vars($display_data[$i])))
                                                                    <td>{{$value}}</td>
                                                                @else
                                                                    <td><a href="{{$value}}"  class="btn btn-primary btn-sm mx-auto"><i class="mdi mdi-eye-outline"></i> View Stats</a></td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php
                                function getDateDifference($date1, $date2, $format = 'days') {
                                    $datetime1 = new DateTime($date1);
                                    $datetime2 = new DateTime($date2);
                                    $interval = $datetime1->diff($datetime2);

                                    switch ($format) {
                                        case 'years':
                                            return $interval->y;
                                        case 'months':
                                            return $interval->y * 12 + $interval->m;
                                        case 'days':
                                            return $interval->days;
                                        case 'hours':
                                            return $interval->days * 24 + $interval->h;
                                        case 'minutes':
                                            return ($interval->days * 24 + $interval->h) * 60 + $interval->i;
                                        case 'seconds':
                                            return (($interval->days * 24 + $interval->h) * 60 + $interval->i) * 60 + $interval->s;
                                        default:
                                            return $interval;
                                    }
                                }
                            @endphp
                        </div>
                    </div>
                </div>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © Ladybird Softech Co.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    <b>Date Updated : </b>12th Sep 2023  11:08AM
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center px-3 py-4">
            
                    <h5 class="m-0 me-2">Settings</h5>

                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                </div>

                <!-- Settings -->
                <hr class="mt-0" />
                <h6 class="text-center mb-0">Choose Layouts</h6>

                <div class="p-4">
                    <div class="mb-2">
                        <img src="/assets/images/layouts/layout-1.jpg" class="img-thumbnail" alt="layout images">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch" checked>
                        <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                    </div>
    
                    <div class="mb-2">
                        <img src="/assets/images/layouts/layout-2.jpg" class="img-thumbnail" alt="layout images">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input theme-choice" type="checkbox" id="dark-mode-switch">
                        <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                    </div>
                </div>
            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
        <script src="/assets/libs/jquery/jquery.min.js"></script>
        <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/assets/libs/node-waves/waves.min.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>
        <script src="/assets/js/pages/form-validation.init.js"></script>
        
        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/assets/libs/spectrum-colorpicker2/spectrum.min.js"></script>
        <script src="/assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
        <script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>

        {{-- select initialization --}}
        <script src="/assets/js/pages/form-advanced.init.js"></script>

        <!-- Required datatable js -->
        <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>


        <!-- Buttons examples -->
        <script src="/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <script>
            $(document).ready(function() {
                console.log("Ready");
                // table one
                $("#datatable").DataTable(), $("#datatable-buttons").DataTable({
                    lengthChange: !1,
                    buttons: ["copy", "excel", "pdf", "colvis"]
                }).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)"), $(".dataTables_length select").addClass("form-select form-select-sm")
            });
        </script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/circulation_stats.js"></script>

        <!-- Alerts Live Demo js -->
        {{-- <script src="/assets/js/pages/alerts.init.js"></script> --}}

        <script src="/assets/js/app.js"></script>

        
    </body>
</html>