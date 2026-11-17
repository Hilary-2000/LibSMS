<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Circulation Stats | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
        <link rel="apple-touch-icon" href="{{ asset('images/ladybird-apple-touch.png') }}?v=4">

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
            @include('partials.topbar')

            <!-- ========== Left Sidebar Start ========== -->
            @include('partials.sidebar', ['active' => 'Circulation'])
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