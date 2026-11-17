<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Cataloguing | {{ucwords(strtolower(session("fullname")))}} </title>
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
            @include('partials.sidebar', ['active' => 'Cataloging'])
            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        <a href="/Cataloging" class="btn btn-sm btn-soft-primary my-2"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Cataloguing</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Dashboard</li>
                                            <li class="breadcrumb-item active">Book Cataloguing</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="card">
                            <div class="card-body">
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
                                <h5 class="card-title">Note:</h5>
                                <p class="card-title-desc">Organize and structure accessing library's collection of materials from this window.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Book Thumbnail</h4>
                                        <input type="hidden" value="{{$book_data->thumbnail_location}}" id="book_thumbnail_holder">
                                        <img id="book_thumbnails" src="{{$book_data->thumbnail_location}}" alt="Book Thumbnail: " width="150" class="mt-2">
                                    </div>
                                </div><!--end card-->
                            </div><!--end col-->
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Book Details - {{$book_data->book_title}}</h4>
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Call No</th>
                                                    <th>Location</th>
                                                    <th>Date Added</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < count($book_data_all); $i++)
                                                        <tr>
                                                            <td>{{$i+1}} 
                                                                @if ($book_data_all[$i]->availability_status == 1)
                                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Available" class="badge bg-success">in</span> 
                                                                @else
                                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Borrowed" class="badge bg-danger">Out</span> 
                                                                @endif
                                                                <input type="hidden" value="{{json_encode($book_data_all[$i])}}" id="book_dets_{{$book_data_all[$i]->book_id}}"></td>
                                                            <td>{{$book_data_all[$i]->call_no}}</td>
                                                            <td>{{$book_data_all[$i]->shelf_no_location != null ? $book_data_all[$i]->shelf_no_location:"Not Set"}}</td>
                                                            <td>{{date("D dS M Y",strtotime($book_data_all[$i]->date_recorded))}}</td>
                                                            <td>
                                                                <ul class="list-unstyled hstack gap-1 mb-0">
                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Catalogue Details">
                                                                        <button type="button" id="book_details_{{$book_data_all[$i]->book_id}}" class="book_details btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable"><i class="mdi mdi-pen"></i> Edit</button>
                                                                    </li>
                                                                    <li>
                                                                        <a href="/Acquisitions/Book-details/{{$book_data_all[$i]->book_id}}" class="btn btn-soft-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="View book details"><i class="mdi mdi-eye"></i> Details</a>
                                                                    </li>
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>    
                                        </div>
                                    </div>
                                </div><!--end card-->
                            </div><!--end col-->
                        </div><!--end row-->
                        <!-- Scrollable modal -->
                        <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollable" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Edit Book Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body"><!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                    <span class="d-none d-sm-block">Overview</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#edit_book_details" role="tab">
                                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                    <span class="d-none d-sm-block">Edit</span> 
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active" id="overview" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Location</b></label>
                                                            <p id="book_location_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Call Number</b></label>
                                                            <p id="book_call_no_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Edition</b></label>
                                                            <p id="book_edition_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Language</b></label>
                                                            <p id="book_language_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Category / Subject</b></label>
                                                            <p id="book_category_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Keywords</b></label>
                                                            <p id="book_keywords_overview">Not Set</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="edit_book_details" role="tabpanel">
                                                <form action="/Catalogue/EditBooks" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <input type="hidden" name="book_id" id="books_ids">
                                                                <label for="book_location" class="form-label"><b>Book Location</b></label>
                                                                <input type="text" value="{{session('book_location') ? session('book_location') : ''}}" name="book_location" class="form-control" id="book_location"
                                                                    placeholder="Science Shelf, row 10, col 3" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Where in the shelves is the book?
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_call_number" class="form-label"><b>Call Number</b></label>
                                                                <input type="text" value="{{session('book_call_number') ? session('book_call_number') : ''}}" name="book_call_number" class="form-control" id="book_call_number"
                                                                    placeholder="Author" required>
                                                                    <div class="valid-feedback">
                                                                        Looks good!
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                        Whats the book unique identifier?
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_edition" class="form-label"><b>Book Edition</b></label>
                                                                <input type="text" value="{{session("book_edition") ? session("book_edition") : ""}}" name="book_edition" class="form-control" id="book_edition"
                                                                    placeholder="Book Edition">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_language" class="form-label"><b>Book Language</b></label>
                                                                <input type="text" placeholder="English, Kiswahili" value="{{session("book_language") ? session("book_language") : ""}}" name="book_language" class="form-control" id="book_language"
                                                                    placeholder="Last name" required>
                                                                    <div class="valid-feedback">
                                                                        Looks good!
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                        Whats the book language?
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_category" class="form-label"><b>Book Category / Subject</b></label>
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
                                                                <label for="book_keywords" class="form-label"><b>Book Keywords</b></label>
                                                                <input type="text" placeholder="Book title, Book author - used for search" value="{{session("book_keywords") ? session("book_keywords") : ""}}" name="book_keywords" class="form-control" id="book_keywords"
                                                                    placeholder="Last name">
                                                                    <div class="valid-feedback">
                                                                        Looks good!
                                                                    </div>
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
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-soft-primary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
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
        <script src="/assets/libs/jszip/jszip.min.js"></script>
        <script src="/assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="/assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="/assets/js/pages/datatables.init.js?v=2"></script>  

         <!-- Table Editable plugin -->
         <script src="/assets/libs/table-edits/build/table-edits.min.js"></script>

        <script src="/assets/js/pages/table-editable.int.js"></script>
        
        {{-- Book catalogue JS --}}
        <script src="/assets/js/book_catalogue.js"></script>
        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>