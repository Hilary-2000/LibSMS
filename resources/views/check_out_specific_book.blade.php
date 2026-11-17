<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Confirm Book Checkout | {{ucwords(strtolower(session("fullname")))}} </title>
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
                            <a href="/Circulation/check-out" class="btn btn-soft-primary btn-sm"><i class="bx bx-left-arrow-alt"></i> Back</a>
                        </div>
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Confirm Book Checkout</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Circulation</li>
                                            <li class="breadcrumb-item">Book Checkout</li>
                                            <li class="breadcrumb-item active">Confirm Checkout</li>
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
                                                    <span class="d-none d-sm-block">Confirm Checkout</span> 
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#home1" role="tab">
                                                    <span class="d-block d-sm-none"><i class="bx bx-info-circle"></i></span>
                                                    <span class="d-none d-sm-block">Book Details</span>
                                                </a>
                                            </li>
                                        </ul>
        
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active" id="profile1" role="tabpanel">
                                                <form action="/Circulation/Confirm/check-out" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    @if (!empty($reservation_hold))
                                                        <div class="alert alert-info">This copy is reserved for <b>{{ $reservation_hold->borrower_name }}</b> ({{ $reservation_hold->borrower_type }}). It can only be lent to them - or cancel the reservation under <a href="/Circulation/Reservations">Reservations</a>.</div>
                                                    @endif
                                                    <input type="hidden" name="book_ids" value="{{$book_details->book_id}}">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="book_borrower" class="form-label">Borrower</label>
                                                                <select name="book_borrower" id="book_borrower" class="form-control" required>
                                                                    <option value="" hidden>Select an Option</option>
                                                                    <option value="staff">Staff</option>
                                                                    <option value="student" selected>Student</option>
                                                                </select>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Book title is required.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12" id="select_student_details">
                                                            <div class="mb-3">
                                                                <label for="student_borrower" class="form-label">Select Student</label>
                                                                <select name="student_borrower" id="student_borrower" class="select2 form-control">
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
                                                        <div class="col-md-12" id="select_staff_details">
                                                            <div class="mb-3">
                                                                <label for="staff_borrower" class="form-label">Select Staff</label>
                                                                <select name="staff_borrower" id="staff_borrower" class="select2 form-control">
                                                                    <option>Select an option</option>
                                                                    <optgroup label="Staff List">
                                                                        @for ($index = 0; $index < count($staff_details); $index++)
                                                                            <option value="{{$staff_details[$index]->user_id}}">{{ucwords(strtolower($staff_details[$index]->fullname))}}</option>
                                                                        @endfor
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="expected_return_date" class="form-label">Expected return date</label>
                                                                <input min="{{date("Y-m-d")}}" type="date" value="{{date("Y-m-d")}}" name="expected_return_date" class="form-control" id="expected_return_date" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    The book return date should be set.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="mb-3">
                                                                <label for="checkout_comment" class="form-label">Comment (optional)</label>
                                                                <textarea name="checkout_comment" id="checkout_comment" cols="30" rows="5" class="form-control" placeholder="Write your comment here!"></textarea>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    The book return date should be set.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        {{-- opens the confirmation below; the form is only submitted from there --}}
                                                        <button type="button" id="open_checkout_confirm" class="btn btn-soft-primary btn-hover w-100 rounded"><i class="bx bx-save"></i> Checkout</button>
                                                    </div>
                                                </form>
                                                <div class="modal fade" id="confirm_checkout" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="confirm_checkout_label" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="confirm_checkout_label">Confirm Check-Out</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-2">Lend <b>{{ $book_details->book_title }}</b> (Call No: <b>{{ $book_details->call_no }}</b>) to <b id="cc_borrower">&hellip;</b>?</p>
                                                                <div class="alert alert-danger py-2 small d-none" id="cc_missing">Select the student or staff member who is borrowing the book first.</div>
                                                                <div class="alert alert-info py-2 small mb-0">
                                                                    <b>What this does:</b> records that this book is <b>leaving the library</b> with the borrower today.
                                                                    It will show as <b>checked out</b> and cannot be lent to anyone else until it is checked in.
                                                                    If it is not back by the due date, it is listed as <b>overdue</b> and librarians get a reminder.
                                                                    <div class="mt-1">Due back: <b id="cc_due">&hellip;</b> <span id="cc_days" class="text-muted"></span></div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
                                                                <button type="button" class="btn btn-outline-success" id="cc_confirm"><i class="bx bx-log-out-circle"></i> Yes, check it out</button>
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
                                                            <label class="form-label"><b>Location</b></label>
                                                            <p>
                                                                @if($book_details->availability_status == 0)
                                                                    Borrowed
                                                                @else
                                                                    Available at:
                                                                    @if(isset($book_details->shelf_no_location) && strlen($book_details->shelf_no_location) > 0)
                                                                        <b>{{ $book_details->shelf_no_location }} - 
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

        <!-- JAVASCRIPT -->

        <script src="/assets/libs/select2/js/select2.min.js"></script>
        <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/assets/libs/spectrum-colorpicker2/spectrum.min.js"></script>
        <script src="/assets/libs/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
        <script src="/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/libs/@chenfengyuan/datepicker/datepicker.min.js"></script>

        <!-- form advanced init -->
        <script src="/assets/js/pages/form-advanced.init.js"></script>
        <script src="/assets/js/confirm_checkout.js"></script>

        <script>
            // Check-out confirmation: show who borrows the book and until when, then submit the form.
            (function () {
                var form = document.querySelector('form[action="/Circulation/Confirm/check-out"]');
                var openBtn = document.getElementById("open_checkout_confirm");
                if (!form || !openBtn) return;
                var modal = new bootstrap.Modal(document.getElementById("confirm_checkout"));
                function selectedText(id) {
                    var sel = document.getElementById(id);
                    if (!sel || sel.selectedIndex < 0) return null;
                    var opt = sel.options[sel.selectedIndex];
                    return opt && opt.value && opt.value !== "Select an option" ? opt.text.trim() : null;
                }
                openBtn.addEventListener("click", function () {
                    if (!form.reportValidity()) return;
                    var type = document.getElementById("book_borrower").value;
                    var who = type === "staff" ? selectedText("staff_borrower") : selectedText("student_borrower");
                    document.getElementById("cc_borrower").textContent = who ? who + " (" + type + ")" : "nobody selected";
                    document.getElementById("cc_missing").classList.toggle("d-none", !!who);
                    document.getElementById("cc_confirm").disabled = !who;

                    var due = document.getElementById("expected_return_date").value; // yyyy-mm-dd
                    var d = new Date(due + "T00:00:00");
                    document.getElementById("cc_due").textContent = isNaN(d) ? due : d.toLocaleDateString(undefined, { weekday: "short", day: "numeric", month: "short", year: "numeric" });
                    var today = new Date(); today.setHours(0, 0, 0, 0);
                    var days = Math.round((d - today) / 86400000);
                    document.getElementById("cc_days").textContent = isNaN(days) ? "" : days === 0 ? "(today)" : "(in " + days + " day" + (days === 1 ? "" : "s") + ")";
                    modal.show();
                });
                document.getElementById("cc_confirm").addEventListener("click", function () {
                    this.disabled = true; // no double check-outs from a double click
                    if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
                });
            })();
        </script>
        <script src="/assets/js/app.js"></script>
    </body>
</html>