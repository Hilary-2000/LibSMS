<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        @include('partials.theme_init')
        <title>Change Library privileges | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                <a href="/Settings/User-mgmt" class="btn btn-sm btn-soft-primary my-3"><i class="mdi mdi-arrow-left"></i> Back</a>
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">User Management</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item">Patron List</li>
                                            <li class="breadcrumb-item active">User Management</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card">
                                    <div class="card-body border-bottom">
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 card-title flex-grow-1">User Details</h5>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        <div class="container text-center">
                                            <img class="img-thumbnail rounded-circle avatar-xl mt-4" src="https://lsims.ladybirdsmis.com/sims/{{$user_data->profile_loc}}" alt="Profile Picture" height="200">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>User Fullname</b></label>
                                                    <p>{{ucwords(strtolower($user_data->fullname))}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>User Role</b></label>
                                                    <p>{{ucwords(strtolower($user_data->auth == 1 ? "Headteacher" : ($user_data->auth == 0 ? "Administrator" : $user_data->auth)))}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>User Gender</b></label>
                                                    <p>{{ucwords(strtolower(($user_data->gender == "M" ? "Male" : "Female")))}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>Phone number</b></label>
                                                    <p>{{ucwords(strtolower($user_data->phone_number))}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>Email</b></label>
                                                    <p>{{ucwords(strtolower($user_data->email))}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label"><b>User Address</b></label>
                                                    <p>{{ucwords(strtolower($user_data->address))}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
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
                                            <h5 class="mb-0 card-title flex-grow-1">Librarian Privileges</h5>
                                            <div class="flex-shrink-0">
                                            </div>
                                        </div>
                                        @php
                                            $library_previleges = \App\Http\Middleware\LibraryAccess::parsePrivileges($user_data->library_previleges);
                                        @endphp
                                        <div class="table-responsive">
                                            <p class="card-title-desc">Check the menu sections this user can open. The Dashboard is always available. Leaving every box unchecked gives full access.</p>
                                            <table id="" class="table table-bordered dt-responsive  nowrap w-100">
                                                <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Privileges</th>
                                                    <th>Allowed <input class="form-check-input " id="input_fields" type="checkbox" aria-label="Allow all"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach (\App\Http\Middleware\LibraryAccess::PRIVILEGES as $privilege => $info)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}.</td>
                                                        <td>
                                                            {{ $info["label"] }}
                                                            <small class="text-muted d-block text-wrap">{{ $info["covers"] }}</small>
                                                        </td>
                                                        <td><input class="form-check-input input_fields" {{ in_array($privilege, $library_previleges, true) ? "checked" : "" }} type="checkbox" value="{{ $privilege }}" aria-label="{{ $info["label"] }}"></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <form action="/settings/store_privileges" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{$user_data->user_id}}">
                                                <input type="hidden" name="store_privileges" id="store_privileges" value="{{$user_data->library_previleges}}">
                                                <button type="submit" class="btn btn-sm btn-outline-primary w-100"><i class=""></i> Save</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body">
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
        <script src="/assets/libs/parsleyjs/parsley.min.js"></script>

        {{-- librarian --}}
        <script src="/assets/js/librarian_priveleges.js"></script>

        <!-- Datatable init js -->
        <script src="/assets/js/pages/datatables.init.js?v=2"></script>
        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>
    </body>
</html>