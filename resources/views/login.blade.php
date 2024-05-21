<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Login | Ladybird Library System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="images/ladybird_dark.png">

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- App js -->
        <script src="assets/js/plugin.js"></script>

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
    </head>

    <body>
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary-subtle">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome to Ladybird Library MIS!</h5>
                                            <p>Sign In.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="images/library_image-removebg-preview.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div class="auth-logo">
                                    <a href="https://ladybirdsmis.com/" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="images/ladybird_dark.png" alt="" class="rounded-circle" height="75">
                                            </span>
                                        </div>
                                    </a>
                                    <a href="https://ladybirdsmis.com/" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="images/ladybird_dark.png" alt="" class="rounded-circle" height="75">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="/process_login">
                                        @csrf
                                        @if (session("error"))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{session("error")}}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif
                                        {{-- <div class="mb-3">
                                            <label for="school_code" class="form-label">School Code</label>
                                            <div class="autocomplete">
                                                <input type="text" value="{{session("school_code") ? session("school_code") : ""}}" class="form-control" name="school_code"  id="school_code" placeholder="Enter school code or Type school name" required>
                                            </div>
                                        </div> --}}

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" value="{{session("username") ? session("username") : ""}}" class="form-control" name="username"  id="username" placeholder="Enter username" required>
                                        </div>
                
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" name="user-password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" required>
                                                <button class="btn btn-light " type="button"  id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" {{session("remember_check") == "on" ? "checked" : ""}} type="checkbox" name="remember-check" id="remember-check">
                                            <label class="form-check-label" for="remember-check">
                                                Remember me
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <a href="https://ladybirdsmis.com/" class=""><i class="mdi mdi-web me-1"></i> Return to Main Website</a>
                                        </div>
                                    </form>
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            
                            <div>
                                <p>Don't have an account ? <a target="_blank" href="https://ladybirdsmis.com/#contact" class="fw-medium text-primary"> Signup now </a> </p>
                                <p>© <script>document.write(new Date().getFullYear())</script> Ladybird. Crafted with <i class="mdi mdi-heart text-danger"></i> by Ladybird Softech Co.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        
        <!-- App js -->
        <script src="assets/js/app.js"></script>

        {{-- script to display schools --}}
        <script>
            var school_data = @json($school_list);
            
            // set the school name and code
            var school_name = [];
            var school_code = [];
            for (let index = 0; index < school_data.length; index++) {
                const element = school_data[index];
                // console.log(element);
                school_name.push(element.school_name);
                school_code.push(element.school_code);
            }
            // console.log(school_name);

            autocomplete(document.getElementById("school_code"),school_name,school_code);

            function autocomplete(inp, arr, arr2) {
                /*the autocomplete function takes two arguments,
                the text field element and an array of possible autocompleted values:*/
                var currentFocus;
                /*execute a function when someone writes in the text field:*/
                inp.addEventListener("input", function (e) {
                    var a, b, i, val = this.value;
                    /*close any already open lists of autocompleted values*/
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    currentFocus = -1;
                    /*create a DIV element that will contain the items (values):*/
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    /*append the DIV element as a child of the autocomplete container:*/
                    this.parentNode.appendChild(a);
                    /*for each item in the array...*/
                    var counter = 0;
                    for (i = 0; i < arr.length; i++) {
                        if (counter > 10) {
                            break;
                        }
                        
                        /*check if the item starts with the same letters as the text field value:*/
                        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()
                            || (arr2[i]+"").substr(0, val.length).toUpperCase() == val.toUpperCase()
                        ) {
                            /*create a DIV element for each matching element:*/
                            b = document.createElement("DIV");
                            /*make the matching letters bold:*/
                            b.innerHTML = "<span class='text-primary'>"+arr[i] + " (" + arr2[i] + ") "+"</span>";
                            // b.innerHTML += arr[i].substr(val.length);
                            /*insert a input field that will hold the current array item's value:*/
                            b.innerHTML += "<input type='hidden' value='" + arr2[i] + "'>";
                            /*execute a function when someone clicks on the item value (DIV element):*/
                            b.addEventListener("click", function (e) {
                                /*insert the value for the autocomplete text field:*/
                                inp.value = this.getElementsByTagName("input")[0].value;
                                /*close the list of autocompleted values,
                                (or any other open lists of autocompleted values:*/
                                closeAllLists();
                            });
                            a.appendChild(b);
                            counter++;
                        }
                    }
                });
                /*execute a function presses a key on the keyboard:*/
                inp.addEventListener("keydown", function (e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x) x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        /*If the arrow DOWN key is pressed,
                        increase the currentFocus variable:*/
                        currentFocus++;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 38) { //up
                        /*If the arrow UP key is pressed,
                        decrease the currentFocus variable:*/
                        currentFocus--;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        /*If the ENTER key is pressed, prevent the form from being submitted,*/
                        e.preventDefault();
                        if (currentFocus > -1) {
                            /*and simulate a click on the "active" item:*/
                            if (x) x[currentFocus].click();
                        }
                    }
                    console.log(currentFocus);
                });
            
                function addActive(x) {
                    /*a function to classify an item as "active":*/
                    if (!x) return false;
                    /*start by removing the "active" class on all items:*/
                    removeActive(x);
                    if (currentFocus >= x.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = (x.length - 1);
                    /*add class "autocomplete-active":*/
                    x[currentFocus].classList.add("autocomplete-active");
                }
            
                function removeActive(x) {
                    /*a function to remove the "active" class from all autocomplete items:*/
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }
            
                function closeAllLists(elmnt) {
                    /*close all autocomplete lists in the document,
                    except the one passed as an argument:*/
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }
                /*execute a function when someone clicks in the document:*/
                document.addEventListener("click", function (e) {
                    closeAllLists(e.target);
                });
            }
        </script>
    </body>
</html>
