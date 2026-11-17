<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Reports | {{ucwords(strtolower(session("fullname")))}} </title>
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

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Reports</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Book Reports</li>
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
                                        {{-- add the option for scanning --}}
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 card-title flex-grow-1">Library MIS Reports</h5>
                                        </div>
                                        <hr>
                                        <div class="col-md-12 my-2">
                                            <div id="liveAlertPlaceholder"><div>
                                        </div>
                                        <form class="row" method="GET" action="/Reports">
                                            <div class="col-md-3">
                                                <label for="book_options_inside" class="form-control-label">Book Options</label>
                                                <select name="book_options_inside" id="book_options_inside" class="form-control" required>
                                                    <option hidden value="">Select book option</option>
                                                    <option {{session('book_options_inside') == "book_information" ? "selected" : ""}} value="book_information">All Book Information</option>
                                                    <option {{session('book_options_inside') == "latest_acquisition" ? "selected" : ""}} value="latest_acquisition">Acquisition</option>
                                                    <option {{session('book_options_inside') == "checked_out" ? "selected" : ""}} value="checked_out">Checked Out</option>
                                                    <option {{session('book_options_inside') == "checked_in" ? "selected" : ""}} value="checked_in">Checked In</option>
                                                    <option {{session('book_options_inside') == "due_checked_in" ? "selected" : ""}} value="due_checked_in">Borrowed due for Check-In</option>
                                                    <option {{session('book_options_inside') == "to_be_due_checkin" ? "selected" : ""}} value="to_be_due_checkin">Due for Check-In & days before</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 {{(session('book_options_inside') == "latest_acquisition" || session('book_options_inside') == "checked_out" ||session('book_options_inside') == "checked_in" || session('book_options_inside') == "due_checked_in") ? "" : "d-none"}}" id="date_selection_option">
                                                <label for="date_type_selection" class="form-control-label">Date Selection Options</label>
                                                <select name="date_type_selection" id="date_type_selection" class="form-control">
                                                    <option hidden value="">Select date option</option>
                                                    <option {{session("date_type_selection") == "specific_date" ? "selected" : ""}} selected value="specific_date">Specific Date</option>
                                                    <option {{session("date_type_selection") == "between_periods" ? "selected" : ""}} value="between_periods">Between Periods</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 {{(session('book_options_inside') == "latest_acquisition" || session('book_options_inside') == "checked_out" ||session('book_options_inside') == "checked_in" || session('book_options_inside') == "due_checked_in") && session("date_type_selection") == "specific_date" ? "" : "d-none"}}" id="single_date">
                                                <label for="select_date_1" class="form-control-label">Select Date:</label>
                                                <input type="date" value="{{(session("select_date_1") != null) ? session("select_date_1") : date("Y-m-d")}}" class="form-control" name="select_date_1" id="select_date_1">
                                            </div>
                                            <div class="col-md-3 {{(session('book_options_inside') == "latest_acquisition" || session('book_options_inside') == "checked_out" ||session('book_options_inside') == "checked_in" || session('book_options_inside') == "due_checked_in") && session("date_type_selection") == "between_periods" ? "" : "d-none"}}" id="from_date">
                                                <label for="select_date_from" class="form-control-label">From:</label>
                                                <input type="date" value="{{(session("select_date_from") != null) ? session("select_date_from") : date("Y-m-d",strtotime("-7 days"))}}" class="form-control" name="select_date_from" id="select_date_from">
                                            </div>
                                            <div class="col-md-3 {{(session('book_options_inside') == "latest_acquisition" || session('book_options_inside') == "checked_out" ||session('book_options_inside') == "checked_in" || session('book_options_inside') == "due_checked_in") && session("date_type_selection") == "between_periods" ? "" : "d-none"}}" id="to_date">
                                                <label for="select_date_to" class="form-control-label">To:</label>
                                                <input type="date" value="{{(session("select_date_to") != null) ? session("select_date_to") : date("Y-m-d")}}" class="form-control" name="select_date_to" id="select_date_to">
                                            </div>
                                            <div class="col-md-3">
                                                <button class="mt-3 btn btn-primary" type="submit" id="filter_button"><i class="bx bx-filter-alt"></i> Display</button>
                                            </div>
                                        </form>
                                    </div>
                                    @if (isset($book_list))
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="report_books_table" class="table table-bordered dt-responsive nowrap w-100 report-export" data-export-title="Library books - {{ session('school_details')->school_name ?? '' }}">
                                                    <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Title</th>
                                                        <th>Author</th>
                                                        <th>ISBN</th>
                                                        <th class="d-none">ISBN-10</th>
                                                        <th class="d-none">Keywords</th>
                                                        <th>Date Acquired</th>
                                                        <th>Call No.</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for ($i = 0; $i < count($book_list); $i++)
                                                            <tr>
                                                                <td>{{$i+1}}</td>
                                                                <td>{{$book_list[$i]->book_title}}
                                                                    @if ($book_list[$i]->availability_status == 1)
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Available" class="badge bg-success">in</span> 
                                                                    @else
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Borrowed" class="badge bg-danger">Out</span> 
                                                                    @endif
                                                                </td>
                                                                <td>{{$book_list[$i]->book_author}}</td>
                                                                <td>{{$book_list[$i]->isbn_13}}</td>
                                                                <td class="d-none" >{{$book_list[$i]->isbn_10}}</td>
                                                                <td class="d-none" >{{$book_list[$i]->keywords}}</td>
                                                                <td>{{date("M dS, Y",strtotime($book_list[$i]->date_recorded))}}</td>
                                                                <td>{{$book_list[$i]->call_no}}</td>
                                                                <td>
                                                                    <ul class="list-unstyled hstack gap-1 mb-0">
                                                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                            <a href="/Acquisitions/Book-details/{{$book_list[$i]->book_id}}" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i> View</a>
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
                                        </div>
                                    @else
                                        <div class="card-body">
                                            <div class="container text-center">
                                                <h5 class="card-title">{{$search_heading}}</h5>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="report_results_table" class="table table-bordered dt-responsive nowrap w-100 report-export" data-export-title="{{ $search_heading }} - {{ session('school_details')->school_name ?? '' }}">
                                                    <thead>
                                                    <tr>
                                                        @for ($i = 0; $i < count($table_heading); $i++)
                                                            <th>{{$table_heading[$i]}}</th>
                                                        @endfor
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for ($index = 0; $index < count($book_data); $index++)
                                                            <tr>
                                                                @for ($i = 0; $i < count($book_data[$index]); $i++)
                                                                    @if (($i+1) == count($book_data[$index]))
                                                                        <td>
                                                                            <ul class="list-unstyled hstack gap-1 mb-0">
                                                                                @for ($indx = 0; $indx < count($book_data[$index][$i]); $indx++)
                                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{isset($book_data[$index][$i][$indx]->title) ? $book_data[$index][$i][$indx]->title : ""}}">
                                                                                        <a href="{{$book_data[$index][$i][$indx]->href}}" class="btn btn-sm btn-soft-primary">{!!$book_data[$index][$i][$indx]->show!!}</a>
                                                                                    </li>
                                                                                @endfor
                                                                            </ul>
                                                                        </td>
                                                                    @else
                                                                        <td>{!!$book_data[$index][$i]!!}</td>
                                                                    @endif
                                                                @endfor
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div><!--end card-->
                            </div><!--end col-->

                        </div><!--end row-->
                        

                    </div> <!-- container-fluid -->
                </div><!-- End Page-content -->
                <!-- End Page-content -->

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
        <script src="assets/js/pages/datatables.init.js"></script>
        {{-- Excel / PDF / print export for the report tables. All rows are exported, not just the visible page. --}}
        <script src="/assets/libs/jszip/jszip.min.js"></script>
        <script src="/assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="/assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script>
            $(function () {
                $("table.report-export").each(function () {
                    var title = $(this).data("export-title");
                    // the last column holds the View / Info buttons: leave it out of exports
                    var cols = { columns: ":not(:last-child)" };
                    var table = $(this).DataTable({
                        pageLength: 25,
                        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        dom: "<'row mb-2'<'col-md-6'B><'col-md-6'f>>rt<'row mt-2'<'col-md-5'il><'col-md-7'p>>",
                        buttons: [
                            { extend: "excelHtml5", text: "<i class='mdi mdi-file-excel'></i> Excel", className: "btn btn-sm btn-success", title: title, exportOptions: cols },
                            { extend: "pdfHtml5", text: "<i class='mdi mdi-file-pdf-box'></i> PDF", className: "btn btn-sm btn-danger", title: title, orientation: "landscape", pageSize: "A4", exportOptions: cols },
                            { extend: "csvHtml5", text: "CSV", className: "btn btn-sm btn-secondary", title: title, exportOptions: cols },
                            { extend: "print", text: "<i class='mdi mdi-printer'></i> Print", className: "btn btn-sm btn-primary", title: title, exportOptions: cols }
                        ]
                    });
                });
                $(".dataTables_length select").addClass("form-select form-select-sm d-inline-block w-auto");
            });
        </script>
        <!-- Datatable init js -->
        <script src="assets/js/reports.js"></script>
        <!-- Alerts Live Demo js -->
        <script src="assets/js/pages/alerts.init.js"></script> 

        <script src="/assets/js/app.js"></script>
    </body>
</html>