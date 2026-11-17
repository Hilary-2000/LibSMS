<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Acquisitions | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 card-title flex-grow-1">Book List</h5>
                                            <div class="flex-shrink-0">
                                                <button type="button" id="show_windows" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable"><i class="bx bx-plus"></i> Add a Book</button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p><b>Note:</b></p>
                                            <ul>
                                                <li>All books are listed, {{ request('per_page', 25) }} per page &mdash; use the page numbers under the table to move between pages.</li>
                                                <li>Use the search bar on the table first: typing filters the current page, pressing Enter searches every book. <b>Advanced Search</b> below does the same full search.</li>
                                                <li>For printable or exportable lists (Excel / PDF), use the <b>Reports</b> section.</li>
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
                                                            <a href="/Acquisitions" class="btn btn-soft-primary btn-sm mb-3">Reset</a><br>
                                                        @endif
                                                        <label for="keyword_search" class="form-control-label">Advanced Keyword Search</label>
                                                        <form class="row" method="GET" action="/Acquisitions">
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
                                                        <input class="form-check-input theme-choice" type="checkbox" id="allow_typing">
                                                        <label class="form-check-label" for="allow_typing">Allow Typing</label><hr>
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
                                                                                    <label for="book_call_no" class="form-label">Book Call No | <span class="text-success" id="prev_call_no">Call No : N/A</span></label>
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
                                                                            <hr>
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label for="book_location" class="form-label">Book Location</label>
                                                                                    <input type="text" name="book_location" value="{{session("book_location") ? session("book_location") : ""}}"  class="form-control" id="book_location"
                                                                                        placeholder="Shelf No, Row Number">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label for="library_location" class="form-label">Library Located</label>
                                                                                    <select name="library_location" id="library_location" class="form-control" required>
                                                                                        <option hidden value="">Select Option</option>
                                                                                        @for ($i = 0; $i < count($libraries); $i++)
                                                                                            <option {{session('library_location') == $libraries[$i]->id ? 'selected' : ''}} value="{{$libraries[$i]->id}}">{{ucwords(strtolower($libraries[$i]->Name))}}</option>
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
                                                                            <hr>
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
                                        @include('partials.table_header', ['title' => 'Book Table', 'icon' => 'bx-book', 'table' => 'datatable', 'action' => '/Acquisitions', 'search' => $search_title, 'pager' => $pager, 'noun' => 'book'])
                                        @php
                                            if (!function_exists('elongateData')) { function elongateData($larger_length,$data){
                                                // get the length of the string
                                                $length = strlen($data);

                                                // get the remaining length if string to make it match the longest one
                                                $larger_length -= $length;

                                                // loop to add the string string to match the longest.
                                                for ($index=0; $index < $larger_length; $index++) { 
                                                    $data.= ($index+1 == $larger_length ? "   " : "   ");
                                                }
                                                return $data;
                                            } }
                                        @endphp
                                        <div class="d-flex flex-wrap gap-2 my-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="print_selected_labels"><i class="bx bx-barcode"></i> Print labels for selected</button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="print_page_labels"><i class="bx bx-barcode"></i> Print labels for this page</button>
                                            <a href="/Acquisitions/Import" class="btn btn-sm btn-outline-success"><i class="bx bx-import"></i> Import books (CSV / Excel)</a>
                                            <a href="/Acquisitions/Stock-take" class="btn btn-sm btn-outline-secondary"><i class="bx bx-list-check"></i> Stock-take</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="datatable" data-server-paginated="1" class="table table-bordered dt-responsive  nowrap w-100">
                                                <thead>
                                                <tr>
                                                    <th><input type="checkbox" class="form-check-input" id="pick_all_labels" title="Select all on this page" aria-label="Select all on this page"> No.</th>
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
                                                            <td><input type="checkbox" class="form-check-input label_pick" value="{{$book_list[$i]->book_id}}" aria-label="Select for labels"> {{ ($pager->firstItem() ?? 1) + $i }}</td>
                                                            <td>{{elongateData($larger_length,$book_list[$i]->book_title)}}
                                                                @if ($book_list[$i]->availability_status == 1)
                                                                    @if ($book_list[$i]->lost_status == 0)
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Available" class="badge bg-success">in</span> 
                                                                    @else
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Lost" class="badge bg-danger">lost</span> 
                                                                    @endif
                                                                @else
                                                                    @if ($book_list[$i]->lost_status == 0)
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Borrowed" class="badge bg-danger">Out</span> 
                                                                    @else
                                                                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Lost" class="badge bg-danger">lost</span> 
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>{{elongateData($larger_length,$book_list[$i]->book_author)}}</td>
                                                            <td>{{elongateData($larger_length,$book_list[$i]->isbn_13)}}</td>
                                                            <td class="d-none" >{{$book_list[$i]->isbn_10}}</td>
                                                            <td class="d-none" >{{$book_list[$i]->keywords}}</td>
                                                            <td>{{elongateData($larger_length,date("M dS, Y",strtotime($book_list[$i]->date_recorded)))}}</td>
                                                            <td>{{elongateData($larger_length,$book_list[$i]->call_no)}}</td>
                                                            <td>
                                                                <ul class="list-unstyled hstack gap-1 mb-0">
                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                                        <a href="/Acquisitions/Book-details/{{$book_list[$i]->book_id}}" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></a>
                                                                    </li>
                                                                    @if ($book_list[$i]->availability_status == 1)
                                                                        @if ($book_list[$i]->lost_status == 0)
                                                                            <li data-bs-toggle="tooltip" data-bs-placement="top" title="Check-Out">
                                                                                <a href="/Circulation/check-out/{{$book_list[$i]->book_id}}" class="btn btn-sm btn-soft-warning"><i class="bx bx-log-out-circle"></i></a>
                                                                            </li>
                                                                        @endif
                                                                    @endif
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            @include('partials.pagination', ['pager' => $pager])
                                        </div>
                                        <style>
                                            .w-5{
                                                display: none;
                                            }
                                        </style>
                                        {{-- <span>{{$book_list->links()}}</span> --}}
                                    </div>
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
        <script src="/assets/js/pages/datatables.init.js?v=2"></script> 
        <!-- Alerts Live Demo js -->
        <script src="assets/js/pages/alerts.init.js"></script> 

        {{-- Acqusition --}}
        <script src="/assets/js/acquisition.js"></script>

        <script>
            // barcode labels for the ticked books / the whole page
            (function () {
                function openLabels(ids) {
                    if (!ids.length) { alert("Tick at least one book first."); return; }
                    window.open("/Acquisitions/Labels?ids=" + ids.join(","), "_blank");
                }
                var all = document.getElementById("pick_all_labels");
                if (all) all.addEventListener("change", function () {
                    document.querySelectorAll(".label_pick").forEach(function (c) { c.checked = all.checked; });
                });
                document.getElementById("print_selected_labels").addEventListener("click", function () {
                    openLabels(Array.from(document.querySelectorAll(".label_pick:checked")).map(function (c) { return c.value; }));
                });
                document.getElementById("print_page_labels").addEventListener("click", function () {
                    openLabels(Array.from(document.querySelectorAll(".label_pick")).map(function (c) { return c.value; }));
                });
            })();
        </script>
        <script src="/assets/js/app.js"></script>
    </body>
</html>