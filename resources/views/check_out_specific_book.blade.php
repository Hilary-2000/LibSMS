<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Confirm Book Checkout | {{ucwords(strtolower(session("fullname")))}} </title>
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
                        <div class="container my-2">
                            <a href="/Circulation/check-out" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        </div>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Confirm Book Checkout</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Circulation</li>
                                            <li class="breadcrumb-item">Book Checkout</li>
                                            <li class="breadcrumb-item active">Confirm Checkout</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-body border-bottom text-center">
                                        <h5 class="text-center">Book Thumbnail</h5>
                                        <input type="hidden" value="{{$book_details->thumbnail_location}}" id="book_thumbnail_holder">
                                        <img class="mx-auto" id="book_thumbnails" alt="Book Thumbnail" width="200">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
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
                                        <h4 class="card-title">Book Information - {{(isset($book_details->book_title) && strlen($book_details->book_title) > 0)? $book_details->book_title : "N/A"}}</h4>

                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#profile1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-book-open"></i></span>
                                                    <span class="d-none d-sm-block">Confirm Checkout</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-info-circle"></i></span>
                                                    <span class="d-none d-sm-block">Book Details</span>
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active" id="profile1" role="tabpanel">
                                                <form action="/Circulation/Confirm/check-out" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    <input type="hidden" name="book_ids" value="{{$book_details->book_id}}">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_borrower" class="form-label">Borrower</label>
                                                                <select name="book_borrower" id="book_borrower" class="form-control" required>
                                                                    <option value="" hidden>Select an Option</option>
                                                                    <option value="staff">Staff</option>
                                                                    <option value="student" selected>Student</option>
                                                                </select>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Book title is required.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12" id="select_student_details">
                                                            <div class="mb-3">
                                                                <label for="student_borrower" class="form-label">Select Student</label>
                                                                <select name="student_borrower" id="student_borrower" class="select2 form-control">
                                                                    <option>Select an option</option>
                                                                    @for ($i = 0; $i < count($student_detail); $i++)
                                                                        <optgroup label="{{$student_detail[$i]->class_name}}">
                                                                            @for ($index = 0; $index < count($student_detail[$i]->student_data); $index++)
                                                                                <option value="{{$student_detail[$i]->student_data[$index]->adm_no}}">{{ucwords(strtolower($student_detail[$i]->student_data[$index]->first_name." ".$student_detail[$i]->student_data[$index]->second_name." ".$student_detail[$i]->student_data[$index]->surname))." {".$student_detail[$i]->student_data[$index]->adm_no."}"}}</option>
                                                                            @endfor
                                                                        </optgroup>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12" id="select_staff_details">
                                                            <div class="mb-3">
                                                                <label for="staff_borrower" class="form-label">Select Staff</label>
                                                                <select name="staff_borrower" id="staff_borrower" class="select2 form-control">
                                                                    <option>Select an option</option>
                                                                    <optgroup label="Staff List">
                                                                        @for ($index = 0; $index < count($staff_details); $index++)
                                                                            <option value="{{$staff_details[$index]->user_id}}">{{ucwords(strtolower($staff_details[$index]->fullname))}}</option>
                                                                        @endfor
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="expected_return_date" class="form-label">Expected return date</label>
                                                                <input min="{{date("Y-m-d")}}" type="date" value="{{date("Y-m-d")}}" name="expected_return_date" class="form-control" id="expected_return_date" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    The book return date should be set.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="checkout_comment" class="form-label">Expected return date</label>
                                                                <textarea name="checkout_comment" id="checkout_comment" cols="30" rows="5" class="form-control" placeholder="Write your comment here!"></textarea>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    The book return date should be set.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <button type="submit" class="btn btn-soft-primary btn-hover w-100 rounded"><i class="bx bx-save"></i> Checkout</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="home1" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Title</b></label>
                                                            <p>{{(isset($book_details->book_title) && strlen($book_details->book_title) > 0)? $book_details->book_title : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Call No</b></label>
                                                            <p>{{(isset($book_details->call_no) && strlen($book_details->call_no) > 0)? $book_details->call_no : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Author</b></label>
                                                            <p>{{(isset($book_details->book_author) && strlen($book_details->book_author) > 0)? $book_details->book_author : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Location</b></label>
                                                            <p>
                                                                @if($book_details->availability_status == 0)
                                                                    Borrowed
                                                                @else
                                                                    Available at:
                                                                    @if(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)
                                                                        <b>{!! $book_details->shelf_no_location !!} - 
                                                                            @for ($i = 0; $i < count($libraries); $i++)
                                                                                @if($book_details->library_location == $libraries[$i]->id)
                                                                                    {{$libraries[$i]->Name}}
                                                                                @endif
                                                                            @endfor
                                                                        </b>
                                                                    
                                                                    @else
                                                                        <b>Location Not Set!</b>
                                                                    @endif
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Date Published</b></label>
                                                            <p>{{(isset($book_details->book_publishers) && strlen($book_details->book_publishers) > 0)? date("D dS M Y",strtotime($book_details->published_date)) : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>No. Of pages</b></label>
                                                            <p>{{(isset($book_details->no_of_pages) && strlen($book_details->no_of_pages) > 0)? $book_details->no_of_pages : "N/A"}} Page(s)</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Category / Subject</b></label>
                                                            <p>{{(isset($book_details->book_category) && strlen($book_details->book_category) > 0)? $book_details->book_category : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>ISBN-10</b></label>
                                                            <p>{{(isset($book_details->isbn_10) && strlen($book_details->isbn_10) > 0)? $book_details->isbn_10 : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>ISBN-13</b></label>
                                                            <p>{{(isset($book_details->isbn_13) && strlen($book_details->isbn_13) > 0)? $book_details->isbn_13 : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Date Recorded</b></label>
                                                            <p>{{(isset($book_details->date_recorded) && strlen($book_details->date_recorded) > 0)? date("D dS M Y",strtotime($book_details->date_recorded)) : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Publishers</b></label>
                                                            <p>{{(isset($book_details->book_publishers) && strlen($book_details->book_publishers) > 0)? $book_details->book_publishers : "N/A"}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Description</b></label>
                                                            <p>{{(isset($book_details->description) && strlen($book_details->description) > 0)? $book_details->description : "No description"}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <b>Date Updated : </b>19th August 2023 <-> 12:08AM
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
        
        <!-- JAVASCRIPT -->
        <script src="/assets/libs/jquery/jquery.min.js"></script>
        <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/assets/libs/node-waves/waves.min.js"></script>

        {{-- validation --}}
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>
        <script src="/assets/js/pages/form-validation.init.js"></script>

        <!-- JAVASCRIPT -->

        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/assets/libs/spectrum-colorpicker2/spectrum.min.js"></script>
        <script src="/assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
        <script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>

        <!-- form advanced init -->
        <script src="/assets/js/pages/form-advanced.init.js"></script>
        <script src="/assets/js/confirm_checkout.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>