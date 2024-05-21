<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Circulation Profile | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                <i class="bx bx-bell {{$notification_count > 0 ? 'bx-tada' : ''}}"></i>
                                <span class="badge bg-danger rounded-pill">{{$notification_count > 99 ? "99+" : $notification_count}}</span>
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
                                    <h4 class="mb-sm-0 font-size-18">Borrower Circulation Profile</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="/Cataloging">Book Circulation</a></li>
                                            <li class="breadcrumb-item active">Borrower Circulation Profile</li>
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
                                    <a href="{{url()->previous() ?? '/Circulation/Stats'}}" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{$borrower_data->dp}}" alt="" class="avatar-md rounded-circle img-thumbnail">
                                                </div>
                                                <div class="flex-grow-1 align-self-center">
                                                    <div class="text-muted">
                                                        <h5 class="mb-1">{{ucwords(strtolower($borrower_data->fullname))}}</h5>
                                                        <p class="mb-0">{{$borrower_data->role}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
            
                                        <div class="col-lg-8 align-self-center">
                                            <div class="text-lg-center mt-4 mt-lg-0">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <div>
                                                            <p class="text-muted text-truncate mb-2"> <b style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Books borrowed by {{ucwords(strtolower($borrower_data->fullname))}} but not yet returned." data-bs-original-title="Books borrowed by {{ucwords(strtolower($borrower_data->fullname))}} but not yet returned."><i class="mdi mdi-information-outline font-size-15 pt-1"></i></b> Books Checked Out - <span class="badge bg-danger font-size-10 p-1">Out</span></p>
                                                            <h5 class="mb-0">{{$books_checked_out}} Book(s)</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div>
                                                            <p class="text-muted text-truncate mb-2"> <b style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Books borrowed by {{ucwords(strtolower($borrower_data->fullname))}} and returned." data-bs-original-title="Books borrowed by {{ucwords(strtolower($borrower_data->fullname))}} and returned."><i class="mdi mdi-information-outline font-size-15 pt-1"></i></b> Books Checked In - <span class="badge bg-success font-size-10 p-1">In</span></p>
                                                            <h5 class="mb-0">{{$books_checked_in}} Book(s)</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div>
                                                            <p class="text-muted text-truncate mb-2">Most Borrowed Book</p>
                                                            <h5 class="mb-0">{{count($total_books) > 0 ? $total_books[0]->book_title : "N.A"}}</h5>
                                                            @if (count($total_books) > 0)
                                                                <span class="badge bg-success font-size-10 p-1">{{"Borrowed ".$total_books[0]->Total." Time(s)"}}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
            
                                        {{-- <div class="col-lg-4 d-none d-lg-block">
                                            <div class="clearfix mt-4 mt-lg-0">
                                                <div class="dropdown float-end">
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="bx bxs-cog align-middle me-1"></i> Setting
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="#">Action</a>
                                                        <a class="dropdown-item" href="#">Another action</a>
                                                        <a class="dropdown-item" href="#">Something else</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        
                                    </div>
                                    <!-- end row -->
                                    <!--  Large modal example -->
                                    <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="myLargeModalLabel">Check-in Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-3 text-center">
                                                            <h4 class="card-title">Book Thumbnail</h4>
                                                            <img class="mx-auto" id="book_thumbnails" src="/images/ladybird_dark-removebg.png" alt="Book Thumbnail" width="150">
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <h4 class="card-title">Check-In Details</h4>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Book Title</b></label>
                                                                        <p id="book_title">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Book Call Number</b></label>
                                                                        <p id="book_call_number">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Borrower</b></label>
                                                                        <p><span id="user_fullname">Loading...</span>
                                                                            <span id="tool_tip_borrower" class="badge badge-pill badge-soft-success">Student</span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Date Checked-Out</b></label>
                                                                        <p id="edit_date_checked_out">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Date Checked-In</b></label>
                                                                        <p id="edit_return_date">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Expected Return Date</b></label>
                                                                        <p id="edit_expected_return_date">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Checked In By</b></label>
                                                                        <p id="fullname_check_in">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Checked Out By</b></label>
                                                                        <p id="fullname_check_out">Loading...</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label"><b>Comments</b></label>
                                                                        <p id="comments">Loading...</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button id="cancel_check_in_link" class="btn btn-sm btn-soft-danger w-100" data-bs-target="#confirm_cancel_checkin" data-bs-toggle="modal" data-bs-dismiss="modal"> Cancel Check-In</button>
                                                            {{-- <a href="#" ><i class="bx bx-pencil"></i> Cancel Check-In</a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                    <div class="modal fade" id="confirm_cancel_checkin" aria-hidden="true" aria-labelledby="..." tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancel Check-In</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><b>Note</b> <br>
                                                        This action is irreversible. <br>The system will assume that "<b id="this_book"></b>" of Call No: <b id="this_book_call_no"></b> is not checked in.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <!-- Toogle to first dialog, `data-bs-dismiss` attribute can be omitted - clicking on link will close dialog anyway -->
                                                    <button class="btn btn-soft-success" data-bs-target="#bs-example-modal-lg" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="#" id="confirm_cancel_check_in" class="btn btn-soft-danger" >Yes Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#checked_in" role="tab">
                                                <span class="d-block d-sm-none"><i class="bx bx-log-in-circle"></i></span>
                                                <span class="d-none d-sm-block">Books Checked In</span> 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#checked_out" role="tab">
                                                <span class="d-block d-sm-none"><i class="bx bx-log-out-circle"></i></span>
                                                <span class="d-none d-sm-block">Books Checked Out</span> 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " data-bs-toggle="tab" href="#circulation_stats" role="tab">
                                                <span class="d-block d-sm-none"><i class="bx bx-log-out-circle"></i></span>
                                                <span class="d-none d-sm-block">Circulation Stats</span> 
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane" id="checked_in" role="tabpanel">
                                            <div class="card">
                                                <div class="card-body border-bottom">
                                                    <h4 class="card-title">Books Checked In</h4>
                                                    <p class="card-title-desc">This table displays books that were borrowed and have been returned.</p>
                                                    <div class="table-responsive">
                                                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                            <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Title</th>
                                                                <th>Call No.</th>
                                                                <th>Borrower</th>
                                                                <th>Expected Return Date</th>
                                                                <th>Actual Return Date</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                @for ($i = 0; $i < count($checked_in); $i++)
                                                                    <tr>
                                                                        <td>{{$i+1}} <input type="hidden" id="checkin_values_{{$checked_in[$i]->circulation_id}}" value="{{json_encode($checked_in[$i])}}"></td>
                                                                        <td>{{$checked_in[$i]->book_title}}</td>
                                                                        <td>{{$checked_in[$i]->book_call_number}}</td>
                                                                        <td>{{$checked_in[$i]->user_fullname}}
                                                                            @if ($checked_in[$i]->user_borrowing == "student")
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-success" data-bs-original-title="Student">Student</span>
                                                                            @else
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-success" data-bs-original-title="Staff">Staff</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{date("D dS M Y",strtotime($checked_in[$i]->expected_return_date))}}</td>
                                                                        <td>{{date("D dS M Y",strtotime($checked_in[$i]->return_date))}}</td>
                                                                        <td>
                                                                            <ul class="list-unstyled hstack gap-1 mb-0">
                                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="View check-in details">
                                                                                    <button class="btn btn-sm btn-outline-primary view_check_out_details" id="view_check_out_details_{{$checked_in[$i]->circulation_id}}" type="button" data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg"><i class="mdi mdi-eye-outline"></i> View</button>
                                                                                </li>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane active" id="checked_out" role="tabpanel">
                                            <div class="card">
                                                <div class="card-body border-bottom">
                                                    <h4 class="card-title">Books Checked Out</h4>
                                                    <p class="card-title-desc">This table displays books that were borrowed and have not been returned.</p>
                                                    <div class="table-responsive">
                                                        <table id="datatable_check_out" class="table table-bordered dt-responsive  nowrap w-100">
                                                            <thead>
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Title</th>
                                                                <th>Call No.</th>
                                                                <th>Borrower</th>
                                                                <th>Expected Return Date</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                @for ($i = 0; $i < count($checked_out); $i++)
                                                                    <tr>
                                                                        <td>{{$i+1}}</td>
                                                                        <td>{{$checked_out[$i]->book_title}}</td>
                                                                        <td>{{$checked_out[$i]->book_call_number}}</td>
                                                                        <td>{{$checked_out[$i]->user_fullname}} 
                                                                            @if ($checked_out[$i]->user_borrowing == "student")
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-success" data-bs-original-title="Student">Student</span>
                                                                            @else
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-success" data-bs-original-title="Staff">Staff</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{date("D dS M Y",strtotime($checked_out[$i]->expected_return_date))}}
                                                                            @if ((date("Ymd")*1) > (date("Ymd",strtotime($checked_out[$i]->expected_return_date))*1))
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-danger" data-bs-original-title="Overdue by {{getDateDifference(date("Ymd"), date("Ymd",strtotime($checked_out[$i]->expected_return_date)), 'days')}} Day(s)">Overdue</span>
                                                                            @endif
                                                                            @if ((date("Ymd")*1) == (date("Ymd",strtotime($checked_out[$i]->expected_return_date))*1))
                                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" class="badge badge-pill badge-soft-success" data-bs-original-title="Due Today">Due</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <ul class="list-unstyled hstack gap-1 mb-0">
                                                                                <li data-bs-toggle="tooltip" data-bs-placement="top" title="View borrower details">
                                                                                    <a href="/Circulation/View/check-out/{{$checked_out[$i]->book_id}}/{{$checked_out[$i]->circulation_id}}" class="btn btn-sm btn-soft-success"><i class="mdi mdi-eye-outline"></i> View</a>
                                                                                </li>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="circulation_stats" role="tabpanel">
                                            <div class="card">
                                                <div class="card-body border-bottom">
                                                    <h4 class="card-title">Circulation History</h4>
                                                    <ul class="verti-timeline list-unstyled">
                                                        @if (count($circulation_data) > 0)
                                                            @for ($i = 0; $i < 20; $i++)
                                                                @if (count($circulation_data) == $i)
                                                                    @break;
                                                                @endif
                                                                <li class="event-list">
                                                                    <div class="event-timeline-dot">
                                                                        <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                                    </div>
                                                                    <div class="d-flex">
                                                                        <div class="flex-shrink-0 me-3">
                                                                            <h5 class="font-size-10"><span class="font-size-10 badge {{$circulation_data[$i]->status == "Check-In" ? 'bg-success' : 'bg-warning'}}">{{$circulation_data[$i]->status}}</span> <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <div>
                                                                                {!!$circulation_data[$i]->story!!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endfor
                                                        @else
                                                            <li class="event-list">
                                                                <div class="event-timeline-dot">
                                                                    <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <h5 class="font-size-10">Now <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div>
                                                                            This student has not borrowed any book before!
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <b>Date Updated : </b>1st Nov 2023 : 01:21AM
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

        <!-- Required datatable js -->
        <script src="/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

        <!-- Buttons examples -->
        <script src="/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        
        
        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/assets/libs/spectrum-colorpicker2/spectrum.min.js"></script>
        <script src="/assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
        <script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="/assets/js/pages/datatables.init.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/form-advanced.init.js"></script>
        
        <!-- Alerts Live Demo js -->
        <script src="/assets/js/circulation.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>