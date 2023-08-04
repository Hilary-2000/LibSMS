<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Acquisitions | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                    <h4 class="mb-sm-0 font-size-18">Book Acquisitions</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Book Acquisitions</li>
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
                                        {{-- add the option for scanning --}}
                                        <div class="container p-0">
                                            <h5>Scan Options</h5>
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check w-xs" name="btnradio" id="btnradio4" autocomplete="off" checked="">
                                                <label class="btn btn-outline-secondary" for="btnradio4">Search Books</label>
                                              
                                                <input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off">
                                                <label class="btn btn-outline-secondary" for="btnradio5">Add Books</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 card-title flex-grow-1">Book List</h5>
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable">Add a Book</button>
                                                <a href="." class="btn btn-light"><i class="mdi mdi-refresh"></i></a>
                                                <div class="dropdown d-inline-block">

                                                    <button type="menu" class="btn btn-success" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item" href="#">Action</a></li>
                                                        <li><a class="dropdown-item" href="#">Another action</a></li>
                                                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Scrollable modal -->
                                    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add a Book</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                        <div class="">
                                                            <div id="liveAlertPlaceholder"><div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Book ISBN
                                                            <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none" id="book_isbn_loader" role="status">
                                                                <span class="sr-only">Loading...</span>
                                                            </div>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" id="hold_book_isbn_value" class="form-control" placeholder="Search here using the Book ISBN...">
                                                            <button id="search_book_using_isbn" class="btn btn-light" type="button"><i class="mdi mdi-search-outline"></i> Search</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="card">
                                                                <div class="card-body m-0 p-0">
                                                                    <div class="text-center">
                                                                        {{-- <img src="assets/images/companies/adobe-photoshop.svg" alt="" height="50" class="mx-auto d-block"> --}}
                                                                        <h5 class="mt-3 mb-1">Book Cover</h5>
                                                                        <p id="my_image_holders">
                                                                            <h5 class="card-title placeholder-glow">
                                                                                {{-- <span class="placeholder col-6">
                                                                                    Images Appear Here
                                                                                    Images Appear Here
                                                                                    Images Appear Here
                                                                                    Images Appear Here
                                                                                    Images Appear Here
                                                                                    Images Appear Here
                                                                                </span> --}}
                                                                                <img src="images/book_cover.jpg" width="150" id="book_cover_image" alt="" class="mx-auto d-block">
                                                                            </h5>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-7 border border-primary rounded">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="text-center">
                                                                        {{-- <img src="assets/images/companies/adobe-photoshop.svg" alt="" height="50" class="mx-auto d-block"> --}}
                                                                        <h5 class="mt-3 mb-1">Book Details</h5>
                                                                    </div>
                                                                    <form action="/Acquisitions/add-book" method="POST" class="needs-validation" novalidate>
                                                                        @csrf
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_title" class="form-label">Book Title</label>
                                                                                    <input type="text" value="{{session('book_title') ? session('book_title') : ''}}" name="book_title" class="form-control" id="book_title"
                                                                                        placeholder="Book Title e.g, Rich Dad, Poor Dad" required>
                                                                                    <div class="valid-feedback">
                                                                                        Looks good!
                                                                                    </div>
                                                                                    <div class="invalid-feedback">
                                                                                        Book title is required.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_author" class="form-label">Author</label>
                                                                                    <input type="text" value="{{session('book_author') ? session('book_author') : ''}}" name="book_author" class="form-control" id="book_author"
                                                                                        placeholder="Author" required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            We need the book`s Author.
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="isbn_10" class="form-label">ISBN-10</label>
                                                                                    <input type="text" value="{{session("isbn_10") ? session("isbn_10") : ""}}" name="isbn_10" class="form-control" id="isbn_10"
                                                                                        placeholder="ISBN-10">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="isbn_13" class="form-label">ISBN-13</label>
                                                                                    <input type="text" placeholder="ISBN-13" value="{{session("isbn_13") ? session("isbn_13") : ""}}" name="isbn_13" class="form-control" id="isbn_13"
                                                                                        placeholder="Last name" required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            ISBN-13 should not be empty.
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="no_of_pages" class="form-label">No. Of pages</label>
                                                                                    <input type="number" placeholder="203" value="{{session("no_of_pages") ? session("no_of_pages") : ""}}" name="no_of_pages" class="form-control" id="no_of_pages"
                                                                                        placeholder="Last name" required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            Provide book`s total no of pages!
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_category" class="form-label">Book Category / Subject</label>
                                                                                    <select name="book_category" id="book_category" class="form-control">
                                                                                        <option value="">Select Option</option>
                                                                                        @for ($i = 0; $i < count($subject_name); $i++)
                                                                                            <option {{session("book_category") == $subject_name[$i] ? "selected" : ""}} value="{{$subject_name[$i]}}">{{$subject_name[$i]}}</option>
                                                                                        @endfor
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_publishers" class="form-label">Publishers</label>
                                                                                    <input type="text" value="{{session("book_publishers") ? session("book_publishers") : ""}}" name="book_publishers" class="form-control" id="book_publishers"
                                                                                        placeholder="Book Publishers" required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            Should not be empty.
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="date_published" class="form-label">Date Published</label>
                                                                                    <input type="date" value="{{session("date_published") ? session("date_published") : date("Y-m-d")}}" name="date_published" class="form-control" id="date_published"
                                                                                        placeholder="Last name" required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            Should not be empty.
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_call_no" class="form-label">Book Call No</label>
                                                                                    <input type="text" name="book_call_no" value="{{session("book_call_no") ? session("book_call_no") : ""}}" class="form-control" id="book_call_no"
                                                                                        placeholder="Book Call No." required>
                                                                                        <div class="valid-feedback">
                                                                                            Looks good!
                                                                                        </div>
                                                                                        <div class="invalid-feedback">
                                                                                            Book Call No is a unique id for every book in the library.
                                                                                        </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_location" class="form-label">Book Location</label>
                                                                                    <input type="text" name="book_location" value="{{session("book_location") ? session("book_location") : ""}}"  class="form-control" id="book_location"
                                                                                        placeholder="Shelf No, Row Number">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_description" class="form-label">Book description</label>
                                                                                    <textarea name="book_description" id="book_description" cols="30" rows="5" class="form-control" placeholder="Briefly write what the book is about!">{{session("book_description") ? session("book_description") : ""}}</textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_cover_url" class="form-label">Book Cover URL</label>
                                                                                    <input type="text" value="{{session("book_cover_url") ? session("book_cover_url") : ""}}" name="book_cover_url" class="form-control" id="book_cover_url"
                                                                                        placeholder="Book cover url">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_dimensions" class="form-label">Physical Dimensions</label>
                                                                                    <input type="text" value="{{session("book_dimensions") ? session("book_dimensions") : ""}}" name="book_dimensions" class="form-control" id="book_dimensions"
                                                                                        placeholder="8 x 5 x 0.4 inches">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="book_language" class="form-label">Language</label>
                                                                                    <input type="text" value="{{session("book_language") ? session("book_language") : ""}}" name="book_language" class="form-control" id="book_language"
                                                                                        placeholder="English, Kiswahili, Arabic">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="no_of_revisions" class="form-label">No Of Revisions</label>
                                                                                    <input type="text" value="{{session("no_of_revisions") ? session("no_of_revisions") : ""}}" name="no_of_revisions" class="form-control" id="no_of_revisions"
                                                                                        placeholder="Number of revisions">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-4">
                                                                            <button type="submit" class="btn btn-soft-primary btn-hover w-100 rounded"><i class="mdi mdi-save"></i> Save</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                                    </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="card-body">
                                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>ISBN</th>
                                                <th class="d-none">ISBN-10</th>
                                                <th>Date Acquired</th>
                                                <th>Call No.</th>
                                                <th>Location</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < count($book_list); $i++)
                                                    <tr>
                                                        <td>{{$i+1}}</td>
                                                        <td>{{$book_list[$i]->book_title}}</td>
                                                        <td>{{$book_list[$i]->book_author}}</td>
                                                        <td>{{$book_list[$i]->isbn_13}}</td>
                                                        <td class="d-none" >{{$book_list[$i]->isbn_10}}</td>
                                                        <td>{{date("M dS, Y",strtotime($book_list[$i]->date_recorded))}}</td>
                                                        <td>{{$book_list[$i]->call_no}}</td>
                                                        <td>{{$book_list[$i]->shelf_no_location}}</td>
                                                        <td>
                                                            <ul class="list-unstyled hstack gap-1 mb-0">
                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                    <a href="job-details.html" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></a>
                                                                </li>
                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                    <a href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></a>
                                                                </li>
                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                    <a href="#jobDelete" data-bs-toggle="modal" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-delete-outline"></i></a>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endfor
                                                {{-- <tr>
                                                    <td>Rich Dad, Poor Dad</td>
                                                    <td>Robert Kiyosaki</td>
                                                    <td>9878HJNJN</td>
                                                    <td class="d-none" >9878HJNJN</td>
                                                    <td>August 12th 2023</td>
                                                    <td>MGM-101</td>
                                                    <td>Shelf 10, Row 15</td>
                                                    <td>
                                                        <ul class="list-unstyled hstack gap-1 mb-0">
                                                            <li data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                <a href="job-details.html" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></a>
                                                            </li>
                                                            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <a href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></a>
                                                            </li>
                                                            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                <a href="#jobDelete" data-bs-toggle="modal" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-delete-outline"></i></a>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div><!--end card-->
                            </div><!--end col-->

                        </div><!--end row-->
                        

                    </div> <!-- container-fluid -->
                </div><!-- End Page-content -->
                <!-- End Page-content -->

                <!-- Transaction Modal -->
                <div class="modal fade transaction-detailModal" tabindex="-1" role="dialog" aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="transaction-detailModalLabel">Order Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Product id: <span class="text-primary">#SK2540</span></p>
                                <p class="mb-4">Billing Name: <span class="text-primary">Neal Matthews</span></p>

                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Product</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">
                                                    <div>
                                                        <img src="assets/images/product/img-7.png" alt="" class="avatar-sm">
                                                    </div>
                                                </th>
                                                <td>
                                                    <div>
                                                        <h5 class="text-truncate font-size-14">Wireless Headphone (Black)</h5>
                                                        <p class="text-muted mb-0">$ 225 x 1</p>
                                                    </div>
                                                </td>
                                                <td>$ 255</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <div>
                                                        <img src="assets/images/product/img-4.png" alt="" class="avatar-sm">
                                                    </div>
                                                </th>
                                                <td>
                                                    <div>
                                                        <h5 class="text-truncate font-size-14">Phone patterned cases</h5>
                                                        <p class="text-muted mb-0">$ 145 x 1</p>
                                                    </div>
                                                </td>
                                                <td>$ 145</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Sub Total:</h6>
                                                </td>
                                                <td>
                                                    $ 400
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Shipping:</h6>
                                                </td>
                                                <td>
                                                    Free
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Total:</h6>
                                                </td>
                                                <td>
                                                    $ 400
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end modal -->

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
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <!-- Required datatable js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/jszip/jszip.min.js"></script>
        <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        
        <!-- Responsive examples -->
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
        

        {{-- validation --}}
        <script src="assets/js/pages/form-validation.init.js"></script>
        <script src="assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="assets/js/pages/datatables.init.js"></script>

        {{-- Acqusition --}}
        <script src="assets/js/acquisition.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="assets/js/pages/alerts.init.js"></script>

        <script src="assets/js/app.js"></script>
    </body>
</html>