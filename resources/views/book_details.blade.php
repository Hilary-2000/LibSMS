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
                                        <a href="/Acquisitions/Labels?ids={{$book_details->book_id}}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bx bx-barcode"></i> Print label</a>
                                        @if ($book_details->availability_status == 0 && $book_details->lost_status == 0)
                                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#reserve_window"><i class="bx bx-time-five"></i> Reserve</button>
                                        @endif
                                        <div class="my-2 small text-muted">
                                            Label code: <b class="text-body">{{ $book_code }}</b>
                                            @if ($waiting_count > 0) &middot; <a href="/Circulation/Reservations">{{ $waiting_count }} waiting for this title</a>@endif
                                        </div>
                                        @if ($reservation_hold)
                                            <div class="alert alert-info py-2 my-2">This copy is being kept aside for <b>{{ $reservation_hold->borrower_name }}</b> ({{ $reservation_hold->borrower_type }}), who reserved it.</div>
                                        @endif
                                        {{-- reserve: every copy of this title is out, put the borrower on the waiting list --}}
                                        <div class="modal fade" id="reserve_window" tabindex="-1" aria-labelledby="reserve_window_label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form class="modal-content" method="POST" action="/Circulation/Reservations/New">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{$book_details->book_id}}">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="reserve_window_label">Reserve "{{ $book_details->book_title }}"</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted">The next copy that is checked in will be kept aside for this person.</p>
                                                        <label class="form-label" for="reserve_borrower_type">Reserve for</label>
                                                        <select class="form-control mb-3" name="borrower_type" id="reserve_borrower_type" onchange="document.getElementById('reserve_student_box').classList.toggle('d-none', this.value!=='student');document.getElementById('reserve_staff_box').classList.toggle('d-none', this.value!=='staff');">
                                                            <option value="student" selected>Student</option>
                                                            <option value="staff">Staff</option>
                                                        </select>
                                                        <div id="reserve_student_box" class="mb-3">
                                                            <label class="form-label" for="reserve_student">Student</label>
                                                            <select class="form-control" name="student_borrower" id="reserve_student">
                                                                <option>Select an option</option>
                                                                @foreach ($reserve_students as $group)
                                                                    <optgroup label="{{ $group->class_name }}">
                                                                        @foreach ($group->student_data as $student)
                                                                            <option value="{{ $student->adm_no }}">{{ ucwords(strtolower($student->first_name." ".$student->second_name)) }} ({{ $student->adm_no }})</option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div id="reserve_staff_box" class="mb-3 d-none">
                                                            <label class="form-label" for="reserve_staff">Staff</label>
                                                            <select class="form-control" name="staff_borrower" id="reserve_staff">
                                                                <option>Select an option</option>
                                                                @foreach ($reserve_staff as $staff)
                                                                    <option value="{{ $staff->user_id }}">{{ ucwords(strtolower($staff->fullname)) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <label class="form-label" for="reserve_comments">Comments (optional)</label>
                                                        <input class="form-control" name="comments" id="reserve_comments" maxlength="500">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary"><i class="bx bx-time-five"></i> Reserve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
                                                        <form method="POST" action="/Acquisitions/Delete-book/{{$book_details->book_id}}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-danger"> Yes Delete</button></form>
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
                                                        <form method="POST" action="/Acquisitions/found-book/{{$book_details->book_id}}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-danger"> Yes Confirm</button></form>
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
                                                                <b>{{ $book_details->shelf_no_location }}</b> 
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