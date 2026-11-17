<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>View Book Checked-out | {{ucwords(strtolower(session("fullname")))}} </title>
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
                        <div class="container my-2">
                            <a href="{{url()->previous()}}" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        </div>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Book Circulation Details</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Circulation</li>
                                            <li class="breadcrumb-item">Book Lists</li>
                                            <li class="breadcrumb-item active">Book Circulation Details</li>
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
                                        <input type="hidden" value="{{$book_details->thumbnail_location}}" name="" id="book_thumbnail_holder">
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
                                                    <span class="d-none d-sm-block">Borrower Details</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-info-circle"></i></span>
                                                    <span class="d-none d-sm-block">Book Details</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- Static Backdrop Modal -->
                                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Cancel Check-Out</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Cancel the check-out of <b>{{$book_details->book_title}}</b> (Call No: <b>{{$book_details->call_no}}</b>) to <b>{{$book_borrow_data[0]->borrower_fullname}}</b>?</p>
                                                        <div class="alert alert-warning py-2 small mb-0">
                                                            <b>What this does:</b> use it only when the book was lent <u>by mistake</u>. The loan is deleted as if it never happened and the book goes back on the shelf as available.
                                                            <br>It is <b>not</b> how you record a return &mdash; when the borrower brings the book back, use <b>Check-In</b> instead so the loan stays in their history.
                                                            This cannot be undone.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                        <form method="POST" action="/Circulation/Cancel/check-out/{{$book_borrow_data[0]->circulation_id}}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-danger"><i class="bx bx-undo"></i> Yes, cancel the check-out</button></form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="confirm_checkin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">Confirm Check-In</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @php
                                                            $due_day = date("Ymd", strtotime($book_borrow_data[0]->expected_return_date));
                                                            $days_late = $due_day < date("Ymd") ? (int) round((strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", strtotime($book_borrow_data[0]->expected_return_date)))) / 86400) : 0;
                                                        @endphp
                                                        <p class="mb-2">Check in <b>{{$book_details->book_title}}</b> (Call No: <b>{{$book_details->call_no}}</b>) borrowed by <b>{{$book_borrow_data[0]->borrower_fullname}}</b>?</p>
                                                        <div class="alert alert-info py-2 small mb-0">
                                                            <b>What this does:</b> records that the book has been <b>returned to the library today</b>. It becomes available for others to borrow, the loan moves to the borrowing history, and any overdue reminder for it is cleared.
                                                            If someone has reserved this book, the copy will be kept aside for them.
                                                            <div class="mt-1">Due back: <b>{{ date("D dS M Y", strtotime($book_borrow_data[0]->expected_return_date)) }}</b>
                                                                @if ($days_late > 0) &mdash; returned <b class="text-danger">{{ $days_late }} day{{ $days_late == 1 ? '' : 's' }} late</b>@else &mdash; on time @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                        <form method="POST" action="/Circulation/Confirm/check-in/{{$book_borrow_data[0]->circulation_id}}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-success"> Yes Confirm Check-In</button></form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                            <div class="tab-pane active" id="profile1" role="tabpanel">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Borrower Name</b></label>
                                                            <p>{{$book_borrow_data[0]->borrower_fullname}}
                                                                <span class="badge badge-pill badge-soft-success font-size-12">{{$book_borrow_data[0]->user_borrowing}}</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Date Checked-out </b></label>
                                                            <p>{{date("D dS M Y @ h:i:s A",strtotime($book_borrow_data[0]->date_checked_out))}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Book Call Number</b></label>
                                                            <p>{{$book_borrow_data[0]->book_call_number}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Expected Return Date</b></label>
                                                            <p>{{date("D dS M Y",strtotime($book_borrow_data[0]->expected_return_date))}}
                                                                    @if ((date("Ymd")*1) > (date("Ymd",strtotime($book_borrow_data[0]->expected_return_date))*1))
                                                                        <span class="badge badge-pill badge-soft-danger font-size-12" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger" data-bs-original-title="{{getDateDifference(date("Ymd",strtotime($book_borrow_data[0]->expected_return_date)),date("Ymd"))}} Days past due date">Overdue</span>
                                                                    @endif
                                                                    @if ((date("Ymd")*1) == (date("Ymd",strtotime($book_borrow_data[0]->expected_return_date))*1))
                                                                        <span class="badge badge-pill badge-soft-success font-size-12" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger" data-bs-original-title="Due Today">Due</span>
                                                                    @endif
                                                                    @if ((date("Ymd")*1) < (date("Ymd",strtotime($book_borrow_data[0]->expected_return_date))*1))
                                                                        <span class="badge badge-pill badge-soft-success font-size-12" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger" data-bs-original-title="{{getDateDifference(date("Ymd"),date("Ymd",strtotime($book_borrow_data[0]->expected_return_date)))}} Days before due">Borrowed</span>
                                                                    @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Checked Out By</b></label>
                                                            <p>{{$book_borrow_data[0]->user_checkout_fullname}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label"><b>Comments</b></label>
                                                            <p>{{$book_borrow_data[0]->comments != null ? $book_borrow_data[0]->comments : "No Comment!"}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        <form action="/Circulation/ExtendReturnDate" class="needs-validation" novalidate method="post">
                                                            @csrf
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <input type="hidden" name="circulation_id" value="{{$book_borrow_data[0]->circulation_id}}">
                                                                    <label for="expected_return_date" class="form-label">Change Expected return date</label>
                                                                    <input min="{{date("Y-m-d")}}" type="date" value="{{date("Y-m-d",strtotime($book_borrow_data[0]->expected_return_date))}}" name="expected_return_date" class="form-control" id="expected_return_date" required>
                                                                    <div class="valid-feedback">
                                                                        Looks good!
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                        Set the expected return date.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-outline-success btn-block my-2 w-100"><i class="bx bx-rotate-left"></i> Change Date</button>
                                                        </form>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        <div class="mb-3 row justify-content-between">
                                                            <div class="col-auto">
                                                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirm_checkin"><i class="bx bx-log-in-circle"></i> Confirm Check-In</button>
                                                            </div>
                                                            <div class="col-auto">
                                                                <button class="btn btn-soft-danger" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="bx bx-undo"></i> Cancel Check-out</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

        {{-- validation --}}
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>
        <script src="/assets/js/pages/form-validation.init.js"></script>

        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/assets/libs/spectrum-colorpicker2/spectrum.min.js"></script>
        <script src="/assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
        <script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>

        <!-- form advanced init -->
        <script src="/assets/js/pages/form-advanced.init.js"></script>
        {{-- <script src="/assets/js/confirm_checkout.js"></script> --}}
        <script>
            var book_thumbnail = document.getElementById("book_thumbnail_holder");
            window.onload = function () {
                document.getElementById("book_thumbnails").src = book_thumbnail.value;
            }
        </script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>