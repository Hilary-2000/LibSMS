<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Cataloguing | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="/images/ladybird_dark.png">

        <link href="assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="assets/js/plugin.js"></script>

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

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
                                    <img src="assets/images/logo.svg" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="assets/images/logo-dark.png" alt="" height="17">
                                </span>
                            </a>

                            <a href="." class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="images/ladybird_dark-removebg.png" alt="" height="40">
                                </span>
                                <span class="logo-lg">
                                    <img src="images/ladybird_dark-removebg.png" alt="" height="100">
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
                                <span class="badge bg-danger rounded-pill">0</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0" key="t-notifications"> Notifications </h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#!" class="small" key="t-view-all"> View All</a>
                                        </div>
                                    </div>
                                </div>
                                <div data-simplebar style="max-height: 230px;">
                                    <a href="#" class="text-reset notification-item">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title bg-success rounded-circle font-size-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">No Notifications</h6>
                                                <div class="font-size-12 text-muted">
                                                    <p class="mb-1" key="t-occidental">You have no notifications at the moment.</p>
                                                    {{-- <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1 hours ago</span></p> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2 border-top d-grid">
                                    <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                        <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View More..</span> 
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-1.jpg"
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{session("gender") == "M" ? "Mr." : "Ms."}} {{explode(" ",ucwords(strtolower(session("fullname"))))[0]}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="#"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                                <a class="dropdown-item d-block" href="#"><i class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings">Settings</span></a>
                                <a class="dropdown-item" href="#"><i class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span key="t-lock-screen">Lock screen</span></a>
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
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" key="t-menu">Menu</li>
                            {{-- dashboard --}}
                            <li>
                                <a href="/Dashboard" class="waves-effect">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-chat">Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="/Acquisitions" class="waves-effect">
                                    <i class="bx bx-add-to-queue"></i>
                                    <span key="t-file-manager">Acquisitions</span>
                                </a>
                            </li>
                            <li>
                                <a href="/Cataloging" class="waves-effect">
                                    <i class="bx bx-file"></i>
                                    <span key="t-file-manager">Cataloging</span>
                                </a>
                            </li>
                            <li>
                                <a href="/Circulation" class="waves-effect">
                                    <i class="bx bx-rotate-left"></i>
                                    <span key="t-file-manager">Circulation</span>
                                </a>
                            </li>
                            <li>
                                <a href="/Administration" class="waves-effect">
                                    <i class="bx bx-building"></i>
                                    <span key="t-file-manager">Administration</span>
                                </a>
                            </li>
                            <li>
                                <a href="/Settings" class="waves-effect">
                                    <i class="bx bx-cog"></i>
                                    <span key="t-file-manager">Settings</span>
                                </a>
                            </li>
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

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Cataloguing</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Book Cataloguing</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body border-bottom">
                                        @if (session("success"))
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <i class="mdi mdi-check-all me-2"></i>
                                                {{session("success")}}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif
                                        @if (session("error"))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <i class="mdi mdi-check-all me-2"></i>
                                                {{session("error")}}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif
                                        <input type="hidden" id="main_keylogger_listener">
                                        <h5 class="mb-0 card-title flex-grow-1">Book List</h5>
                                        {{-- add the option for scanning --}}
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                {{-- <button type="button" id="show_windows" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable"><i class="bx bx-plus"></i> Add a Book</button> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <p class="card-desc">This table shows all the different books you have in your library!</p>
                                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>ISBN</th>
                                                <th class="d-none">ISBN-10</th>
                                                <th>Quantity</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < count($book_list); $i++)
                                                    <tr>
                                                        <td>{{$i+1}}</td>
                                                        <td>{{$book_list[$i]['book_title']}}</td>
                                                        <td>{{$book_list[$i]['book_author']}}</td>
                                                        <td>{{$book_list[$i]['isbn_13']}}</td>
                                                        <td class="d-none" >{{$book_list[$i]['isbn_10']}}</td>
                                                        <td>{{$book_list[$i]['Total']." Book(s)"}}</td>
                                                        <td>
                                                            <ul class="list-unstyled hstack gap-1 mb-0">
                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                    <a href="/Cataloging/Edit/{{$book_list[$i]['isbn_13']}}" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i> View</a>
                                                                </li>
                                                                {{-- <li data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                    <button class="btn btn-soft-danger btn-sm delete_data" id="delete_data{{$book_list[$i]->book_id}}"><i class="mdi mdi-delete-outline"></i></button>
                                                                </li> --}}
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                    </div> <!-- container-fluid -->
                </div><!-- End Page-content -->
                <!-- End Page-content -->

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © Ladybird Softech Co.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    <b>Date Updated : </b>24th June 2023 <-> 11:08PM
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
                        <img src="assets/images/layouts/layout-1.jpg" class="img-thumbnail" alt="layout images">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch" checked>
                        <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                    </div>
    
                    <div class="mb-2">
                        <img src="assets/images/layouts/layout-2.jpg" class="img-thumbnail" alt="layout images">
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

        
        <!-- JAVASCRIPT -->
        <script src="/assets/libs/jquery/jquery.min.js"></script>
        <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/assets/libs/node-waves/waves.min.js"></script>

        <!-- Required datatable js -->
        <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

        <!-- Buttons examples -->
        <script src="/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="/assets/libs/jszip/jszip.min.js"></script>
        <script src="/assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="/assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        
        <!-- Responsive examples -->
        <script src="/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
        

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="assets/js/pages/datatables.init.js"></script>  


        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>