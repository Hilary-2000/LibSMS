<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>User Management | {{ucwords(strtolower(session("fullname")))}} </title>
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
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Library Management</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item active">Library Management</li>
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
                                            <h5 class="mb-0 card-title flex-grow-1">Library List</h5>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal_two"><i class="bx bx-plus"></i> Add Library</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- center modal -->
                                        <div class="modal fade bs-example-modal-center" id="modal_one" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Library Name</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="/Setting/Library-mgmt/Update" class="needs-validation" novalidate>
                                                            @csrf
                                                            <input type="hidden" name="library_id" value="0" id="library_id">
                                                            <div class="mb-3">
                                                                <label for="library_name" class="form-control-label">Library Name</label>
                                                                <input type="text" class="form-control" name="library_name" id="library_name" placeholder="Library Name" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Set the Library Name.
                                                                </div>
                                                            </div>
                                                            <button class="w-100 btn btn-soft-success" type="submit"><i class="bx bx-save"></i> Save</button>
                                                        </form>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                        <!-- center modal -->
                                        <div class="modal fade bs-example-modal-center" tabindex="-1" id="modal_two" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Register New Library</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="/Setting/Library-mgmt/New" class="needs-validation" novalidate>
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="library_name_new" class="form-control-label">Library Name</label>
                                                                <input type="text" class="form-control" name="library_name_new" id="library_name_new" placeholder="Library Name" required>
                                                                <div class="valid-feedback">
                                                                    Looks good!
                                                                </div>
                                                                <div class="invalid-feedback">
                                                                    Set the Library Name.
                                                                </div>
                                                            </div>
                                                            <button class="w-100 btn btn-soft-success" type="submit"><i class="bx bx-save"></i> Save</button>
                                                        </form>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                        <!-- center modal -->
                                        <div class="modal fade bs-example-modal-center" tabindex="-1" id="modal_three" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to permanent delete <b id="library_name_delete">Library Name</b>?</p>
                                                        <form method="POST" action="/Settings/Lib-Management/Delete/0" id="delete_lib_form" class="w-100">@csrf<button type="submit" id="delete_lib_link" class="btn btn-outline btn-danger my-3 w-100"><i class="bx bx-trash"></i> Delete</button></form>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->

                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                                <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Library Name</th>
                                                    <th>Total Books</th>
                                                    <th>Total Checked Out</th>
                                                    <th>Total Remaining</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    @for ($i = 0; $i < count($libraries); $i++)
                                                        <tr>
                                                            <td>{{$i+1}}</td>
                                                            <td>{{ucwords(strtolower($libraries[$i]->Name))}} <input type="hidden" value="{{ucwords(strtolower($libraries[$i]->Name))}}" id="library_name_holder_{{ucwords(strtolower($libraries[$i]->id))}}" value=""></td>
                                                            <td>{{$libraries[$i]->total}} Book(s)</td>
                                                            <td>{{$libraries[$i]->checked_out}} Book(s)</td>
                                                            <td>{{$libraries[$i]->total - $libraries[$i]->checked_out}} Book(s)</td>
                                                            <td>
                                                                <ul class="list-unstyled hstack gap-1 mb-0">
                                                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Library details">
                                                                        <button class="btn btn-outline-primary btn-sm edit_book_details" id="edit_book_details_{{$libraries[$i]->id}}" data-bs-toggle="modal" data-bs-target="#modal_one"><i class="bx bx-pen"></i></button>
                                                                    </li>
                                                                    @if ($libraries[$i]->id != 1)
                                                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                            <button class="btn btn-outline-danger btn-sm delete_book" id="delete_book_{{$libraries[$i]->id}}" data-bs-toggle="modal" data-bs-target="#modal_three"><i class="bx bx-trash"></i></button>
                                                                        </li>
                                                                    @endif
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
        <script src="/assets/js/pages/datatables.init.js"></script> 
        <!-- Alerts Live Demo js -->
        <script src="/assets/js/pages/alerts.init.js"></script>

        <script src="/assets/js/app.js"></script>

        <script>
            var library_name = document.getElementById("library_name");
            var library_id = document.getElementById("library_id");
            var edit_book_details = document.getElementsByClassName("edit_book_details");
            var delete_book = document.getElementsByClassName("delete_book");
            
            // set the click event listener
            for (let index = 0; index < edit_book_details.length; index++) {
                const element = edit_book_details[index];
                element.addEventListener("click", showBookDetails);
            }

            // data setter function
            function showBookDetails () {
                var this_id = this.id.substr(18);
                var library_name_holder = document.getElementById("library_name_holder_"+this_id).value;
                library_name.value = library_name_holder;
                library_id.value = this_id;
            }

            // delete all books
            for (let index = 0; index < delete_book.length; index++) {
                const element = delete_book[index];
                element.addEventListener("click",deleteBook);
            }

            // delete data
            function deleteBook() {
                var this_id = this.id.substr(12);
                var library_name_holder = document.getElementById("library_name_holder_"+this_id).value;
                document.getElementById("library_name_delete").innerText = library_name_holder;
                document.getElementById("delete_lib_form").action = "/Settings/Lib-Management/Delete/"+this_id;
            }
        </script>
    </body>
</html>