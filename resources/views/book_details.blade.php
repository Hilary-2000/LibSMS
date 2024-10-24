<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Book Details | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                <li class="mm-active">
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
                                <li>
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
                            <a href="/Acquisitions" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        </div>

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Details</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Acquisitions</li>
                                            <li class="breadcrumb-item active">Book Details</li>
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
                                        <img class="mx-auto" src="" alt="Book Thumbnail" id="book_thumbnails" width="200">
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
                                        
                                        {{-- <p class="card-title-desc">Example of custom tabs</p> --}}
                                        <button class="btn btn-outline-danger btn-sm" id="delete_trash" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="bx bx-trash"></i> Delete</button>
                                        <button class="btn btn-outline-warning btn-sm {{$book_details->lost_status == "1" ? 'd-none' : ''}}" id="mark_lost" data-bs-toggle="modal" data-bs-target="#mark_as_lost_window"><i class="bx bx-no-entry"></i> Mark as Lost</button>
                                        <button class="btn btn-outline-success btn-sm {{$book_details->lost_status == "0" ? 'd-none' : ''}}" id="mark_found" data-bs-toggle="modal" data-bs-target="#mark_as_found_window"><i class="bx bx-no-entry"></i> Mark as Found</button>
                                        <div class="border border-primary rounded bg-light text-black my-2 p-2 {{$book_details->lost_status == "0" ? 'd-none' : ''}}">
                                            <p><b>Note:</b></p>
                                            This book was lost on : "{{date("D dS M Y", strtotime($book_details->date_lost))}}" by "{{ucwords(strtolower($book_details->lost_by))}}" and was reported on "{{date("D dS M Y", strtotime($book_details->date_recorded))}}" by "{{ucwords(strtolower($book_details->reported_by))}}"
                                        </div>
                                        
                                        <!-- Static Backdrop Modal -->
                                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Delete Book</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>This action is irreversible? <br>Are you sure you want to delete <b>{{$book_details->book_title}}</b> Call No: <b>{{$book_details->call_no}}</b>?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                        <a href="/Acquisitions/Delete-book/{{$book_details->book_id}}" class="btn btn-outline-danger"> Yes Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Static Backdrop Modal -->
                                        <div class="modal fade" id="mark_as_found_window" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Mark Book as Found</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to mark <b>{{$book_details->book_title}}</b> Call No: <b>{{$book_details->call_no}}</b> as found?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                        <a href="/Acquisitions/found-book/{{$book_details->book_id}}" class="btn btn-outline-danger"> Yes Confirm</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Static Backdrop Modal -->
                                        <div class="modal fade" id="mark_as_lost_window" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Mark as Lost</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{url()->route("confirm_lost", $book_details->book_id)}}" method="post">
                                                            @csrf
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label for="who_lost_it" class="form-label">Who lost it?</label>
                                                                    <select name="who_lost_it" id="who_lost_it" class="select2 form-control">
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
                                                            <div class="form-control-group w-100">
                                                                <label for="when_was_it_lost" class="form-label">When was it lost?</label>
                                                                <input type="date" value="<?=date("Y-m-d")?>" class="form-control" name="when_was_it_lost" id="when_was_it_lost">
                                                            </div>
                                                            <div class="corm-control-group w-100">
                                                                <label for="who_reported_it" class="form-label">Who reported it?</label>
                                                                <select name="who_reported_it" id="who_reported_it" class="select2 form-control">
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
                                                            <div class="form-control-group w-100">
                                                                <label for="when_was_it_reported" class="form-label">When was it reported?</label>
                                                                <input type="date" value="<?=date("Y-m-d")?>" name="when_was_it_reported" class="form-control" id="when_was_it_reported" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Book title is required.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                                <button type="submit" class="btn btn-outline-success"> <i class="fas fa-check"></i> Confirm</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block">Book Details</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#profile1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-pen"></i></span>
                                                    <span class="d-none d-sm-block">Edit Details</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item" id="more_information_data">
                                                <a class="nav-link" data-bs-toggle="tab" href="#messages1"  role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-info-circle"></i></span>
                                                    <span  class="d-none d-sm-block">More Infor</span>   
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#availability_status" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-stats"></i></span>
                                                    <span class="d-none d-sm-block">Availability Status</span>   
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#circulation_log" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-rotate-left"></i></span>
                                                    <span class="d-none d-sm-block">Circulation Log</span>   
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active" id="home1" role="tabpanel">
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
                                            <div class="tab-pane" id="profile1" role="tabpanel">
                                                <form action="/Acquisitions/update-book" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    <input type="hidden" name="book_ids" value="{{$book_details->book_id}}">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_title" class="form-label">Book Title</label>
                                                                <input type="text" name="book_title" class="form-control" id="book_title"
                                                                    value="{{(isset($book_details->book_title) && strlen($book_details->book_title) > 0)? $book_details->book_title : ""}}" placeholder="Book Title e.g, Rich Dad, Poor Dad" required>
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
                                                                <input type="text" value="{{(isset($book_details->book_author) && strlen($book_details->book_author) > 0)? $book_details->book_author : ""}}" name="book_author" class="form-control" id="book_author"
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
                                                                <input type="text" value="{{(isset($book_details->isbn_10) && strlen($book_details->isbn_10) > 0)? $book_details->isbn_10 : ""}}" name="isbn_10" class="form-control" id="isbn_10"
                                                                    placeholder="ISBN-10">
                                                            </div>
                                                        </div>
                                                        <input type="hidden" value="{{(isset($book_details->isbn_13) && strlen($book_details->isbn_13) > 0)? $book_details->isbn_13 : ""}}" class="form-control" id="isbn_13_details_hidden">
                                                        <input type="hidden" value="{{(isset($book_details->isbn_10) && strlen($book_details->isbn_10) > 0)? $book_details->isbn_10 : ""}}" class="form-control" id="isbn_10_details_hidden">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="isbn_13" class="form-label">ISBN-13</label>
                                                                <input type="text" placeholder="ISBN-13" value="{{(isset($book_details->isbn_13) && strlen($book_details->isbn_13) > 0)? $book_details->isbn_13 : ""}}" name="isbn_13" class="form-control" id="isbn_13"
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
                                                                <input type="number" placeholder="203" value="{{(isset($book_details->no_of_pages) && strlen($book_details->no_of_pages) > 0)? $book_details->no_of_pages : ""}}" name="no_of_pages" class="form-control" id="no_of_pages"
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
                                                                        <option {{$book_details->book_category == $subject_name[$i] ? "selected" : ""}} value="{{$subject_name[$i]}}">{{$subject_name[$i]}}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_publishers" class="form-label">Publishers</label>
                                                                <input type="text" value="{{(isset($book_details->book_publishers) && strlen($book_details->book_publishers) > 0)? $book_details->book_publishers : ""}}" name="book_publishers" class="form-control" id="book_publishers"
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
                                                                <input type="date" value="{{(isset($book_details->published_date) && strlen($book_details->published_date) > 0)? $book_details->published_date : ""}}" name="date_published" class="form-control" id="date_published"
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
                                                                <input type="text" name="book_call_no" value="{{(isset($book_details->call_no) && strlen($book_details->call_no) > 0)? $book_details->call_no : ""}}" class="form-control" id="book_call_no"
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
                                                                <input type="text" name="book_location" value="{{(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)? $book_details->shelf_no_location : ""}}"  class="form-control" id="book_location"
                                                                    placeholder="Shelf No, Row Number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="library_location" class="form-label">Library Located</label>
                                                                <select name="library_location" id="library_location" class="form-control" required>
                                                                    <option hidden value="">Select Option</option>
                                                                    @for ($i = 0; $i < count($libraries); $i++)
                                                                        <option {{$book_details->library_location == $libraries[$i]->id ? 'selected' : ''}} value="{{$libraries[$i]->id}}">{{ucwords(strtolower($libraries[$i]->Name))}}</option>
                                                                    @endfor
                                                                </select>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Select the library the book is Located!.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_description" class="form-label">Book description</label>
                                                                <textarea name="book_description" id="book_description" cols="30" rows="5" class="form-control" placeholder="Briefly write what the book is about!">{{(isset($book_details->description) && strlen($book_details->description) > 0)? $book_details->description : ""}}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_cover_url" class="form-label">Book Cover URL</label>
                                                                <input type="text" value="{{(isset($book_details->thumbnail_location) && strlen($book_details->thumbnail_location) > 0)? $book_details->thumbnail_location : ""}}" name="book_cover_url" class="form-control" id="book_cover_url"
                                                                    placeholder="Book cover url">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_dimensions" class="form-label">Physical Dimensions</label>
                                                                <input type="text" value="{{(isset($book_details->physical_dimensions) && strlen($book_details->physical_dimensions) > 0)? $book_details->physical_dimensions : ""}}" name="book_dimensions" class="form-control" id="book_dimensions"
                                                                    placeholder="8 x 5 x 0.4 inches">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_language" class="form-label">Language</label>
                                                                <input type="text" value="{{(isset($book_details->language) && strlen($book_details->language) > 0)? $book_details->language : ""}}" name="book_language" class="form-control" id="book_language"
                                                                    placeholder="English, Kiswahili, Arabic">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="no_of_revisions" class="form-label">No Of Revisions</label>
                                                                <input type="text" value="{{(isset($book_details->no_of_revisions) && strlen($book_details->no_of_revisions) > 0)? $book_details->no_of_revisions : ""}}" name="no_of_revisions" class="form-control" id="no_of_revisions"
                                                                    placeholder="Number of revisions">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <button type="submit" class="btn btn-soft-primary btn-hover w-100 rounded"><i class="bx bx-save"></i> Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="messages1" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">
                                                                <b>Source of Information</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div>
                                                            </label>
                                                            <br><a target="_blank" href="." id="according_to">Click Here</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Weight</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <p id="book_weight">Loading...</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Physical Format</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <p id="physical_format">Loading...</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Edition</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <p id="edition_name">Loading...</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Date Created</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <p id="date_created">Loading...</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Last Modified</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <p id="last_modified">Loading...</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Authors</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <div id="book_authors"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="availability_status" role="tabpanel">
                                                <p class="mb-0">
                                                    @if($book_details->availability_status == 0)
                                                        <p class="text-soft-success">Book has been borrowed! <br>Check circulation log for more details!</p>
                                                    @else
                                                        <p>
                                                            Available at:
                                                            @if(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)
                                                                <b>{!! $book_details->shelf_no_location !!}</b> 
                                                                <br>Library Located : 
                                                                <b>
                                                                    @for ($i = 0; $i < count($libraries); $i++)
                                                                        @if($book_details->library_location == $libraries[$i]->id)
                                                                            {{$libraries[$i]->Name}}
                                                                        @endif
                                                                    @endfor 
                                                                </b>
                                                            @else
                                                                <b>Location Not Set!</b>
                                                            @endif
                                                        </p>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="circulation_log" role="tabpanel">
                                                <h4 class="card-title mb-5">Activity</h4>
                                                <ul class="verti-timeline list-unstyled">
                                                    @if (count($book_circulation_details) > 0)
                                                        @for ($i = 0; $i < 20; $i++)
                                                            @if (count($book_circulation_details) == $i)
                                                                @break;
                                                            @endif
                                                            <li class="event-list">
                                                                <div class="event-timeline-dot">
                                                                    <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 me-3">
                                                                        <h5 class="font-size-10">{{date("D dS M Y @ H:i:s A",strtotime($book_circulation_details[$i]->date))}} <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div>
                                                                            {!!$book_circulation_details[$i]->description!!}
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
                                                                        This book has not been circulated yet!
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
                                    <b>Date Updated : </b>24rd Oct 2024 : 12:21AM
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
        {{-- <script src="/assets/js/pages/form-advanced.init.js"></script> --}}

        {{-- Acqusition --}}
        <script src="/assets/js/book_details.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <!-- Sweet alert init js-->
        <script src="/assets/js/pages/sweet-alerts.init.js"></script>

        {{-- app --}}
        <script src="/assets/js/app.js"></script>
        <script>
            var book_thumbnail = document.getElementById("book_thumbnail_holder");
            window.onload = function () {
                document.getElementById("book_thumbnails").src = book_thumbnail.value;
            }
        </script>
    </body>
</html>