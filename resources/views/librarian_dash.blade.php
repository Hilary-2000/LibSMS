<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Dashboard | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description"/>
        <meta content="Ladybird Softech Co." name="author"/>
        <!-- App favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ladybird-favicon.png') }}?v=4">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=4">
        <link rel="apple-touch-icon" href="{{ asset('images/ladybird-apple-touch.png') }}?v=4">

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="assets/js/plugin.js"></script>

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
                        @if (session("error"))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                {{session("error")}}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                            <div class="col-xl-4">
                                <div class="card overflow-hidden">
                                    <div class="bg-primary-subtle">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="text-primary p-3">
                                                    <h5 class="text-primary">Welcome Back !</h5>
                                                    <p>Librarian Dashboard</p>
                                                </div>
                                            </div>
                                            <div class="col-5 align-self-end">
                                                <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="avatar-md profile-user-wid mb-4">
                                                    <img src="https://lsims.ladybirdsmis.com/sims/{{(session()->get("user_details")->profile_loc != null && strlen(trim(session()->get("user_details")->profile_loc)) > 0) ? session()->get("user_details")->profile_loc : "images/dp.png"}}" alt="" class="img-thumbnail rounded-circle">
                                                </div>
                                                <h5 class="font-size-15 text-truncate">{{session("gender") == "M" ? "Mr." : "Ms."}} {{explode(" ",ucwords(strtolower(session("fullname"))))[0]}}</h5>
                                                <p class="text-muted mb-0 text-truncate">{{session("auth") == 1 ? "Headteacher" : (session("auth") == 0 ? "Administrator" : "Librarian")}}</p>
                                            </div>

                                            <div class="col-sm-8">
                                                <div class="pt-4">

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <h5 class="font-size-15">{{$check_out_user}} Book(s)</h5>
                                                            <p class="text-muted mb-0">Checked-Out</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <h5 class="font-size-15">{{$check_in_user}} Book(s)</h5>
                                                            <p class="text-muted mb-0">Checked-In</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Books Checked-Out</h4>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <p class="text-muted">This month</p>
                                                <h3>{{$borrowed_this_month}} </h3>
                                                <p class="text-muted">
                                                    @if ($borrowed_this_month == 0 && $borrowed_last_month == 0)
                                                        @php
                                                            $percentage = 0;
                                                        @endphp
                                                        <input type="hidden" value="{{$percentage}}" id="radial_chart_values">
                                                        <span class="text-danger me-2">{{$percentage}}% <i class="mdi mdi-arrow-down"></i> </span> From previous Month
                                                    @endif
                                                    @if ($borrowed_this_month > $borrowed_last_month && ($borrowed_this_month != 0 && $borrowed_last_month != 0))
                                                        @php
                                                            $percentage = round(($borrowed_this_month - $borrowed_last_month) / $borrowed_this_month * 100);
                                                        @endphp
                                                        <span class="text-success me-2">{{$percentage}}% <i class="mdi mdi-arrow-up"></i> </span> From previous Month
                                                    @endif
                                                    @if ($borrowed_this_month < $borrowed_last_month && ($borrowed_this_month != 0 && $borrowed_last_month != 0))
                                                        @php
                                                            $percentage = round(($borrowed_last_month - $borrowed_this_month) / $borrowed_this_month * 100);
                                                        @endphp
                                                        <span class="text-danger me-2">{{$percentage}}% <i class="mdi mdi-arrow-down"></i> </span> From previous Month
                                                    @endif
                                                    @if ($borrowed_this_month == $borrowed_last_month)
                                                        @php
                                                            $percentage = 0;
                                                        @endphp
                                                        <input type="hidden" value="{{$percentage}}" id="radial_chart_values">
                                                        <span class="text-danger me-2">{{$percentage}}% <i class="mdi mdi-arrow-down"></i> </span> From previous Month
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0">Books are like magic portals to worlds unknown.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted fw-medium">Total Books</p>
                                                        <h4 class="mb-0">{{$book_count}} Books</h4>
                                                    </div>

                                                    <div class="flex-shrink-0 align-self-center">
                                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                            <span class="avatar-title">
                                                                <i class="bx bx-book font-size-24"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted fw-medium">Checked Out Today</p>
                                                        <h4 class="mb-0">{{$checked_out_today}} Books</h4>
                                                    </div>

                                                    <div class="flex-shrink-0 align-self-center ">
                                                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                            <span class="avatar-title rounded-circle bg-primary">
                                                                <i class="bx bxs-log-in-circle font-size-24"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mini-stats-wid">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted fw-medium">Checked In Today</p>
                                                        <h4 class="mb-0">{{$checked_in_today}} Books</h4>
                                                    </div>

                                                    <div class="flex-shrink-0 align-self-center">
                                                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                            <span class="avatar-title rounded-circle bg-primary">
                                                                <i class="bx bxs-log-out-circle font-size-24"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="col-xl-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title mb-5">Book Circulation Logs</h4>
                                                <ul class="verti-timeline list-unstyled">
                                                    <li class="event-list">
                                                        <div class="event-timeline-dot">
                                                            <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                        </div>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 me-3">
                                                                <h5 class="font-size-14">22 Nov <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div>
                                                                    Responded to need “Volunteer Activities
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="event-list">
                                                        <div class="event-timeline-dot">
                                                            <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                        </div>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 me-3">
                                                                <h5 class="font-size-14">17 Nov <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div>
                                                                    Everyone realizes why a new common language would be desirable... <a href="javascript: void(0);">Read more</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="event-list active">
                                                        <div class="event-timeline-dot">
                                                            <i class="bx bxs-right-arrow-circle font-size-18 bx-fade-right"></i>
                                                        </div>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 me-3">
                                                                <h5 class="font-size-14">15 Nov <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div>
                                                                    Joined the group “Boardsmanship Forum”
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="event-list">
                                                        <div class="event-timeline-dot">
                                                            <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                        </div>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 me-3">
                                                                <h5 class="font-size-14">12 Nov <i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></h5>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div>
                                                                    Responded to need “In-Kind Opportunity”
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="text-center mt-4"><a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light btn-sm">View More <i class="mdi mdi-arrow-right ms-1"></i></a></div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h4 class="card-title mb-4">Top Borrowed Books</h4>
                                                @if (count($most_borrowed_books) > 0)
                                                    <div class="text-center">
                                                        <div class="mb-4">
                                                            <i class="bx bx-book text-primary display-4"></i>
                                                        </div>
                                                        <h3>{{$most_borrowed_books[0]->Total}} Times</h3>
                                                        <p>{{$most_borrowed_books[0]->book_title}}</p>
                                                    </div>
                                                    @php
                                                        $total_count = 0;
                                                        for ($index=0; $index < count($most_borrowed_books); $index++) { 
                                                            $total_count += $most_borrowed_books[$index]->Total;
                                                        }
                                                        // get the percentage of each book
                                                        for ($index=0; $index < count($most_borrowed_books); $index++) {
                                                            $percentage = round(($most_borrowed_books[$index]->Total / $total_count) * 100);
                                                            $most_borrowed_books[$index]->percentage = $percentage;
                                                        }
                                                    @endphp
            
                                                    <div class="table-responsive mt-4">
                                                        <table class="table align-middle table-nowrap">
                                                            <tbody>
                                                                @for ($i = 0; $i < count($most_borrowed_books); $i++)
                                                                    <tr>
                                                                        <td style="width: 30%">
                                                                            <p class="mb-0">{{$most_borrowed_books[$i]->book_title}}</p>
                                                                        </td>
                                                                        <td style="width: 25%">
                                                                            <h6 class="mb-0">{{number_format($most_borrowed_books[$i]->Total)}} X</h6></td>
                                                                        <td>
                                                                            <div class="progress bg-transparent progress-sm">
                                                                                <div class="progress-bar bg-primary rounded" role="progressbar" style="width: {{$most_borrowed_books[$i]->percentage}}%" aria-valuenow="{{$most_borrowed_books[$i]->percentage}}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center">
                                                        <div class="mb-4">
                                                            <i class="bx bx-book text-primary display-4"></i>
                                                        </div>
                                                        <h3>0</h3>
                                                        <p>No Check-Out Done Yet!</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div>
                        </div>
                        <!-- end row -->

                        {{-- Charts: last 12 months. Each has a "View as table" toggle (identity is never colour-only). --}}
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="card-title mb-1">Borrowing activity</h4>
                                                <p class="text-muted small mb-0">Last 12 months &middot; {{ number_format($charts['total_12m']) }} check-outs &middot; <b>{{ number_format($charts['overdue_now']) }}</b> overdue right now</p>
                                            </div>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#activity_table">View as table</button>
                                        </div>
                                        <div id="chart_activity" style="min-height: 320px"></div>
                                        <div class="collapse" id="activity_table">
                                            <div class="table-responsive"><table class="table table-sm mb-0">
                                                <thead><tr><th>Month</th><th>Checked out</th><th>Returned</th><th>Late / overdue (by due month)</th></tr></thead>
                                                <tbody>@foreach ($charts['months'] as $k => $month)<tr><td>{{ $month }}</td><td>{{ $charts['checked_out'][$k] }}</td><td>{{ $charts['returned'][$k] }}</td><td>{{ $charts['late'][$k] }}</td></tr>@endforeach</tbody>
                                            </table></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title mb-1">Students vs staff</h4>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#types_table">View as table</button>
                                        </div>
                                        <p class="text-muted small">Check-outs, last 12 months</p>
                                        <div id="chart_types" style="min-height: 280px"></div>
                                        <div class="collapse" id="types_table">
                                            <table class="table table-sm mb-0"><tbody>@foreach ($charts['borrower_types']['labels'] as $k => $label)<tr><td>{{ $label }}</td><td>{{ $charts['borrower_types']['values'][$k] }}</td></tr>@endforeach</tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title mb-1">Most borrowed books</h4>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#top_table">View as table</button>
                                        </div>
                                        <p class="text-muted small">Times borrowed, all time</p>
                                        <div id="chart_top_books" style="min-height: 320px"></div>
                                        <div class="collapse" id="top_table">
                                            <table class="table table-sm mb-0"><tbody>@foreach ($charts['top_books']['labels'] as $k => $label)<tr><td>{{ $label }}</td><td>{{ $charts['top_books']['values'][$k] }}</td></tr>@endforeach</tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h4 class="card-title mb-1">Borrowing by class</h4>
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#class_table">View as table</button>
                                        </div>
                                        <p class="text-muted small">Student check-outs, last 12 months</p>
                                        <div id="chart_classes" style="min-height: 320px"></div>
                                        <div class="collapse" id="class_table">
                                            <table class="table table-sm mb-0"><tbody>@foreach ($charts['classes']['labels'] as $k => $label)<tr><td>{{ $label }}</td><td>{{ $charts['classes']['values'][$k] }}</td></tr>@endforeach</tbody></table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">New Arrivals</h4>
                                        <div class="table-responsive">
                                            <table id="datatable" class="table align-middle dataTable table-nowrap mb-0 dt-responsive w-100 no-footer dtr-inline">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="align-middle">Title</th>
                                                        <th class="align-middle">Author</th>
                                                        <th class="align-middle">ISBN-10</th>
                                                        <th class="align-middle">ISBN-13</th>
                                                        <th class="align-middle">Call No</th>
                                                        <th class="align-middle">Date Registered</th>
                                                        <th class="align-middle">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < count($book_data); $i++)
                                                        <tr>
                                                            <td>{{$book_data[$i]->book_title}}</td>
                                                            <td>{{$book_data[$i]->book_author}}</td>
                                                            <td>{{$book_data[$i]->isbn_10}}</td>
                                                            <td>{{$book_data[$i]->isbn_13}}</td>
                                                            <td>{{$book_data[$i]->call_no}}</td>
                                                            <td>{{date("D dS M Y",strtotime($book_data[$i]->date_recorded))}}</td>
                                                            <td><a href="/Acquisitions/Book-details/{{$book_data[$i]->book_id}}" class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i> View</a></td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- end table-responsive -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                <!-- Transaction Modal -->
                <div class="modal fade transaction-detailModal" tabindex="-1" role="dialog" aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="transaction-detailModalLabel">Order Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Product id: <span class="text-primary">#SK2540</span></p>
                                <p class="mb-4">Billing Name: <span class="text-primary">Neal Matthews</span></p>

                                <div class="table-responsive">
                                    <table class="table align-middle table-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Product</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">
                                                    <div>
                                                        <img src="assets/images/product/img-7.png" alt="" class="avatar-sm">
                                                    </div>
                                                </th>
                                                <td>
                                                    <div>
                                                        <h5 class="text-truncate font-size-14">Wireless Headphone (Black)</h5>
                                                        <p class="text-muted mb-0">$ 225 x 1</p>
                                                    </div>
                                                </td>
                                                <td>$ 255</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">
                                                    <div>
                                                        <img src="assets/images/product/img-4.png" alt="" class="avatar-sm">
                                                    </div>
                                                </th>
                                                <td>
                                                    <div>
                                                        <h5 class="text-truncate font-size-14">Phone patterned cases</h5>
                                                        <p class="text-muted mb-0">$ 145 x 1</p>
                                                    </div>
                                                </td>
                                                <td>$ 145</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Sub Total:</h6>
                                                </td>
                                                <td>
                                                    $ 400
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Shipping:</h6>
                                                </td>
                                                <td>
                                                    Free
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <h6 class="m-0 text-right">Total:</h6>
                                                </td>
                                                <td>
                                                    $ 400
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end modal -->

                <!-- subscribeModal -->
                {{-- <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-bottom-0">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-4">
                                    <div class="avatar-md mx-auto mb-4">
                                        <div class="avatar-title bg-light rounded-circle text-primary h1">
                                            <i class="mdi mdi-email-open"></i>
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-xl-10">
                                            <h4 class="text-primary">Subscribe !</h4>
                                            <p class="text-muted font-size-14 mb-4">Subscribe our newletter and get notification to stay update.</p>

                                            <div class="input-group bg-light rounded">
                                                <input type="email" class="form-control bg-transparent border-0" placeholder="Enter Email address" aria-label="Recipient's username" aria-describedby="button-addon2">
                                                
                                                <button class="btn btn-primary" type="button" id="button-addon2">
                                                    <i class="bx bxs-paper-plane"></i>
                                                </button>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!-- end modal -->

                @include('partials.footer')
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        @include('partials.rightbar')
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <!-- apexcharts -->
        <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>
        <script>
            // Dashboard charts. Colours: validated categorical palette (blue, orange, aqua) - see app/Services/DashboardCharts.php for the data.
            (function () {
                var C = @json($charts);
                // light and dark steps of the same validated palette (dark checked against Skote's dark card #2a3042)
                var charts = [];
                function palette() {
                    var dark = document.documentElement.getAttribute("data-bs-theme") === "dark";
                    return dark
                        ? { series: ["#3987e5", "#d95926", "#199e70"], ink: "#c3cbe4", grid: "#32394e", surface: "#2a3042", mode: "dark" }
                        : { series: ["#2a78d6", "#eb6834", "#1baf7a"], ink: "#52514e", grid: "#e9e9e7", surface: "#fcfcfb", mode: "light" };
                }
                function renderAll() {
                charts.forEach(function (c) { c.destroy(); });
                charts = [];
                var P = palette();
                var SERIES = P.series, INK = P.ink, GRID = P.grid, SURF = P.surface;
                var base = {
                    theme: { mode: P.mode },
                    tooltip: { theme: P.mode },
                    chart: { toolbar: { show: false }, fontFamily: "inherit", foreColor: INK, background: "transparent", animations: { enabled: false } },
                    grid: { borderColor: GRID, strokeDashArray: 3 },
                    dataLabels: { enabled: false },
                    legend: { position: "top", horizontalAlign: "left", markers: { radius: 12 } },
                    noData: { text: "No borrowing recorded yet" }
                };
                function draw(el, opts) {
                    var node = document.querySelector(el);
                    if (!node) return;
                    var chart = new ApexCharts(node, Object.assign({}, base, opts));
                    chart.render();
                    charts.push(chart);
                }
                draw("#chart_activity", {
                    chart: Object.assign({}, base.chart, { type: "line", height: 320 }),
                    series: [
                        { name: "Checked out", data: C.checked_out },
                        { name: "Returned", data: C.returned },
                        { name: "Late / overdue", data: C.late }
                    ],
                    colors: SERIES,
                    stroke: { width: 2, curve: "straight" },
                    markers: { size: 4, strokeWidth: 2, strokeColors: SURF, hover: { size: 6 } },
                    xaxis: { categories: C.months, axisBorder: { color: GRID }, axisTicks: { show: false } },
                    // all-zero data makes ApexCharts print "Infinity" on the axis: give it a small fixed range
                    yaxis: { min: 0, max: Math.max.apply(null, C.checked_out.concat(C.returned, C.late)) < 4 ? 4 : undefined, tickAmount: 4, forceNiceScale: true, labels: { formatter: function (v) { return Math.round(v); } } },
                    tooltip: { shared: true, intersect: false, theme: P.mode }
                });
                draw("#chart_types", {
                    chart: Object.assign({}, base.chart, { type: "donut", height: 280 }),
                    series: C.borrower_types.values,
                    labels: C.borrower_types.labels,
                    colors: SERIES.slice(0, 2),
                    stroke: { width: 2, colors: [SURF] },
                    legend: { position: "bottom" },
                    dataLabels: { enabled: true, formatter: function (v) { return Math.round(v) + "%"; }, dropShadow: { enabled: false } },
                    plotOptions: { pie: { donut: { size: "62%", labels: { show: true, total: { show: true, label: "Check-outs", color: INK } } } } }
                });
                draw("#chart_top_books", {
                    chart: Object.assign({}, base.chart, { type: "bar", height: 320 }),
                    series: [{ name: "Times borrowed", data: C.top_books.values }],
                    colors: [SERIES[0]],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, borderRadiusApplication: "end", barHeight: "60%" } },
                    xaxis: { categories: C.top_books.labels, max: Math.max.apply(null, C.top_books.values.concat([0])) < 4 ? 4 : undefined, tickAmount: 4, labels: { formatter: function (v) { return Math.round(v); } } },
                    yaxis: { labels: { maxWidth: 240 } },
                    grid: { borderColor: GRID, strokeDashArray: 3, padding: { left: 18 } },
                    legend: { show: false },
                    stroke: { width: 2, colors: [SURF] }
                });
                draw("#chart_classes", {
                    chart: Object.assign({}, base.chart, { type: "bar", height: 320 }),
                    series: [{ name: "Check-outs", data: C.classes.values }],
                    colors: [SERIES[0]],
                    plotOptions: { bar: { borderRadius: 4, borderRadiusApplication: "end", columnWidth: "55%" } },
                    xaxis: { categories: C.classes.labels, axisBorder: { color: GRID }, axisTicks: { show: false } },
                    yaxis: { min: 0, max: Math.max.apply(null, C.classes.values.concat([0])) < 4 ? 4 : undefined, tickAmount: 4, labels: { formatter: function (v) { return Math.round(v); } } },
                    legend: { show: false },
                    stroke: { width: 2, colors: [SURF] }
                });
                }
                renderAll();
                // the Light / Dark / System switch fires this when the theme changes
                document.addEventListener("library:themechange", renderAll);
            })();
        </script>

        <!-- dashboard init -->
        <script src="assets/js/pages/dashboard.init.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js"></script>
    </body>

</html>