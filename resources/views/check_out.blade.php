<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Check Out | {{ucwords(strtolower(session("fullname")))}} </title>
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

    {{-- autocomplete style for school listing --}}
    <style>
        /*the container must be positioned relative:*/
        .autocomplete {
            position: relative;
            display: inline-block;
            width: 100%
        }

        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            /*position the autocomplete items to be the same width as the container:*/
            top: 100%;
            left: 0;
            right: 0;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }

        /*when hovering an item:*/
        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }

        /*when navigating through the items using the arrow keys:*/
        .autocomplete-active {
            background-color: DodgerBlue !important;
            color: #ffffff;
        }
    </style>

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
                        <a href="/Circulation" class="btn btn-soft-primary btn-sm my-3"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Check Out Books</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Book Circulation</li>
                                            <li class="breadcrumb-item active">Check Out Book</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card">
                                <div class="card-body border-bottom">
                                    {{-- <a href="/Circulation/check-in" class="btn btn-secondary"><i class="bx bxs-log-in-circle"></i> Check In</a> --}}
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
                                    <div class="my-3">
                                        <p><b>Note:</b></p>
                                        <ul>
                                            <li>All books on the shelf are listed, {{ request('per_page', 25) }} per page &mdash; use the page numbers under the table to move between pages.</li>
                                            <li>Use the search bar on the table first: typing filters the current page, pressing Enter searches every book. <b>Advanced Search</b> below does the same full search.</li>
                                            <li>Scan a book's label in the scan box to go straight to its check-out.</li>
                                        </ul>
                                        <div class="d-flex gap-2 flex-wrap mb-3">
                                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                                Advanced Search
                                            </button>
                                        </div>
                                        <div class="collapse {{$search_title != null ? 'show' : ''}}" id="collapseExample">
                                            <div class="card border border-3 shadow-none card-body text-muted mb-0">
                                                <div class="form-group">
                                                    <div class="d-none">
                                                        <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none" id="book_isbn_loader_in" role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                    </div>
                                                    @if ($search_title != null)
                                                        <a href="/Circulation/check-out" class="btn btn-soft-primary btn-sm mb-3">Reset</a><br>
                                                    @endif
                                                    <label for="keyword_search" class="form-control-label">Advanced Keyword Search</label>
                                                    <form class="row" method="GET" action="/Circulation/check-out">
                                                        <div class="col-lg-9">
                                                            <div class="autocomplete">
                                                                <input type="text" value="{{$search_title != null ? $search_title : ''}}" name="keyword_search" id="keyword_search" class="form-control" placeholder="USE: ISBN, Book Title, Author, Call Number, Publisher" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <button id="submit_advance_search" class="btn btn-light" type="submit"><i class="mdi mdi-search-outline"></i>Advanced Search</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>
                                    @include('partials.scan_box', ['back' => '/Circulation/check-out'])
                                    @include('partials.table_header', ['title' => 'Books available to lend', 'icon' => 'bx-book-open', 'table' => 'datatable', 'action' => '/Circulation/check-out', 'search' => $search_title, 'pager' => $pager, 'noun' => 'book'])
                                    <div class="table-responsive">
                                        <table id="datatable" data-server-paginated="1" class="table table-bordered dt-responsive  nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>ISBN</th>
                                                <th class="d-none">ISBN-10</th>
                                                <th class="d-none">Keywords</th>
                                                <th>Call No.</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 0; $i < count($book_list); $i++)
                                                    <tr>
                                                        <td>{{ ($pager->firstItem() ?? 1) + $i }}</td>
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
                                                        <td>{{$book_list[$i]->call_no}}</td>
                                                        <td>
                                                            @if ($book_list[$i]->availability_status == 1)
                                                                <ul class="list-unstyled hstack gap-1 mb-0">
                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="Check-out">
                                                                        <a href="/Circulation/check-out/{{$book_list[$i]->book_id}}" class="btn btn-sm btn-soft-primary"><i class="bx bx-log-out-circle"></i> Check-Out</a>
                                                                    </li>
                                                                </ul>
                                                            @else
                                                                <ul class="list-unstyled hstack gap-1 mb-0">
                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="View borrower details">
                                                                        <a href="/Circulation/View/check-out/{{$book_list[$i]->book_id}}/{{$book_list[$i]->circulation_id}}" class="btn btn-sm btn-soft-success"><i class="mdi mdi-eye-outline"></i> View</a>
                                                                    </li>
                                                                </ul>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                        @include('partials.pagination', ['pager' => $pager])
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
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
        <script src="/assets/js/checkout.js"></script>
    </body>
</html>