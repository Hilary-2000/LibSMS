<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Coming Soon | {{ucwords(strtolower(session("fullname")))}} </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Ladybird Lbrary Management System" name="description" />
        <meta content="Ladybird Softech Co." name="author" />
        
        <!-- App favicon -->
        <link rel="shortcut icon" href="/images/ladybird_dark.png">

        <!-- Bootstrap Css -->
        <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="/assets/js/plugin.js"></script>

    </head>

    <body>
        <div class="home-btn d-none d-sm-block">
            <a href="/Dashboard" class="text-white"><i class="fas fa-home h2"></i></a>
        </div>

        <div class="my-2 pt-sm-2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <a href="index.html" class="d-block auth-logo">
                                <img src="/images/ladybird_dark.png" alt="" height="100" class="auth-logo-dark mx-auto">
                                <img src="/images/ladybird_dark.png" alt="" height="100" class="auth-logo-light mx-auto">
                            </a>
                            <div class="row justify-content-center mt-2">
                                <div class="col-sm-4">
                                    <div class="maintenance-img">
                                        <img src="/assets/images/coming-soon.svg" alt="" class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                            <h4 class="mt-5">Coming Soon</h4>
                            <p class="text-muted">{{$message}}.</p>
                            <a href="{{url()->previous()}}" class="text-primary">Go back to Previous Page</a>

                            <div class="row justify-content-center mt-5">
                                <div class="col-md-8">
                                    <div data-countdown="{{$date}}" class="counter-number"></div>
                                </div> <!-- end col-->
                            </div> <!-- end row-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="/assets/libs/jquery/jquery.min.js"></script>
        <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="/assets/libs/simplebar/simplebar.min.js"></script>
        <script src="/assets/libs/node-waves/waves.min.js"></script>

        <!-- Plugins js-->
        <script src="/assets/libs/jquery-countdown/jquery.countdown.min.js"></script>

        <!-- Countdown js -->
        <script src="/assets/js/pages/coming-soon.init.js"></script>

        <script src="/assets/js/app.js"></script>

    </body>
</html>