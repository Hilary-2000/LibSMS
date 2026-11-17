<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Book Details | {{ucwords(strtolower(session("fullname")))}} </title>
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
            @include('partials.sidebar', ['active' => 'Acquisitions'])
            <!-- Left Sidebar End -->

            

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        <div class="container my-2">
                            <a href="/Acquisitions" class="btn btn-primary"><i class="bx bx-left-arrow-alt"></i> Back</a>
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
                                        <img class="mx-auto" src="{{$book_details->thumbnail_location}}" alt="Book Thumbnail" width="200">
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
                                                        <form method="POST" action="/Acquisitions/Delete-book/{{$book_details->book_id}}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-danger"> Yes Delete</button></form>
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
                                                            <label class="form-label"><b>Publishers</b></label>
                                                            <p>{{(isset($book_details->book_publishers) && strlen($book_details->book_publishers) > 0)? $book_details->book_publishers : "N/A"}}</p>
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
                                                            <label class="form-label"><b>Location</b></label>
                                                            <p>
                                                                @if($book_details->availability_status == 0)
                                                                    Borrowed
                                                                @else
                                                                    Available at:
                                                                    @if(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)
                                                                        <b>{{ $book_details->shelf_no_location }}</b>
                                                                    @else
                                                                        <b>Location Not Set!</b>
                                                                    @endif
                                                                @endif
                                                            </p>
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
                                                            <label class="form-label"><b>Subject</b>
                                                                <div style="max-width:20px;max-height:20px;" class="spinner-grow text-primary m-1 d-none more_infor_spinners" role="status">
                                                                    <span class="sr-only">Loading...</span>
                                                                </div></label>
                                                            <div id="book_subjects"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="availability_status" role="tabpanel">
                                                <p class="mb-0">
                                                    @if($book_details->availability_status == 0)
                                                        <p>Borrowed</p>
                                                    @else
                                                        <p>
                                                            Available at:
                                                            @if(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)
                                                                <b>{{ $book_details->shelf_no_location }}</b>
                                                            @else
                                                                <b>Location Not Set!</b>
                                                            @endif
                                                        </p>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="tab-pane" id="circulation_log" role="tabpanel">
                                                <p class="mb-0">
                                                    show logs of how the book has been borrowed!
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
        <script src="/assets/libs/jszip/jszip.min.js"></script>
        <script src="/assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="/assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        
        <!-- Responsive examples -->
        <script src="/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
        

        {{-- validation --}}
        <script src="/assets/js/pages/form-validation.init.js"></script>
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        <!-- Datatable init js -->
        <script src="/assets/js/pages/datatables.init.js"></script>

        {{-- Acqusition --}}
        <script src="/assets/js/book_details.js"></script>

        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <!-- Sweet alert init js-->
        <script src="/assets/js/pages/sweet-alerts.init.js"></script>

        {{-- app --}}
        <script src="/assets/js/app.js"></script>
    </body>
</html>