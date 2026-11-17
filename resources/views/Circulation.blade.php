<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Book Circulation | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
        <link rel="apple-touch-icon" href="{{ asset('images/ladybird-apple-touch.png') }}?v=4">

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
            @include('partials.topbar')

            <!-- ========== Left Sidebar Start ========== -->
            @include('partials.sidebar')
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
                                    <h4 class="mb-sm-0 font-size-18">Book Circulation</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Book Circulation</li>
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
                                    <p class="card-title-desc">Click the button below to check out a book borrowed.</p>
                                    <a href="/Circulation/check-out" class="btn btn-secondary"><i class="bx bxs-log-out-circle"></i> Check Out</a>
                                    <a href="/Circulation/Stats" class="btn btn-secondary"><i class="bx bx-stats"></i> Statistics <span class="badge bg-success">new</span></a>
                                    <a href="/Circulation/Reservations" class="btn btn-secondary"><i class="bx bx-time-five"></i> Reservations</a>
                                    @include('partials.scan_box', ['back' => '/Circulation'])
                                    
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
                                                    <form method="POST" action="#" id="confirm_cancel_check_in_form" class="d-inline">@csrf<button type="submit" id="confirm_cancel_check_in" class="btn btn-soft-danger">Yes Cancel</button></form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
        
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
                            </ul>
                            @php
                                if (!function_exists('getDateDifference')) { function getDateDifference($date1, $date2, $format = 'days') {
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
                                } }
                            @endphp

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <div class="tab-pane" id="checked_in" role="tabpanel">
                                    <div class="card">
                                        <div class="card-body border-bottom">
                                            <h4 class="card-title">Books Checked In</h4>
                                            <p class="card-title-desc">This table displays books that were borrowed and have been returned.</p>
                                            <div class="table-responsive">
                                                <table id="datatable" data-server-paginated="1" class="table table-bordered dt-responsive  nowrap w-100">
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
                                                                <td>{{ ($pager_in->firstItem() ?? 1) + $i }} <input type="hidden" id="checkin_values_{{$checked_in[$i]->circulation_id}}" value="{{json_encode($checked_in[$i])}}"></td>
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
                                                @include('partials.pagination', ['pager' => $pager_in])
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
                                                <table id="datatable_check_out" data-server-paginated="1" class="table table-bordered dt-responsive  nowrap w-100">
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
                                                                <td>{{ ($pager_out->firstItem() ?? 1) + $i }}</td>
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
                                                @include('partials.pagination', ['pager' => $pager_out])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('partials.footer')
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        @include('partials.rightbar')
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
        
        

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="/assets/js/pages/datatables.init.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/circulation.js"></script>
        <script>
            // page links of the "returned" list end in #checked_in: reopen that tab after paging
            if (window.location.hash === "#checked_in") {
                var tabLink = document.querySelector('a[href="#checked_in"]');
                if (tabLink) { bootstrap.Tab.getOrCreateInstance(tabLink).show(); }
            }
        </script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>