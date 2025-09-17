<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="dark" data-sidebar-size="lg" data-sidebar="light" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Login | Wikarta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />


</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper">

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">

                <div class="row justify-content-center">
                    <center>
                        <div class="col-lg-12">
                            <div class="card" style="max-width: 330px;margin-top: 10%;box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.05) 0px 8px 32px;border-radius: 7px;margin-top: 15%;">
                                <!--<div class="card-header" style="background-color: #38454a;">-->
                                <div class="card-header" style="background-color: #ffffff;">
                                    @include('components.message')
                                    <span class="logo-lg">
                                        <img src="{{ asset('assets/images/logo-landscape.png') }}" alt="" height="50">
                                    </span>
                                </div>
                                <div class="card-body p-2">
                                    <div class="p-1 mt-1">
                                        <form action="{{ route('login')  }}" method="post">@csrf
                                            <div class="mb-1">
                                                <label for="username" class="form-label" style="float: left;">Username (PhoneNumber)</label>
                                                <input name="username" type="text" class="form-control" id="username" placeholder="username" value="{{ old('username') }}">
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label" for="password-input" style="float: left;">Password</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input name="password" type="password" class="form-control pe-5 password-input" placeholder="password" id="password-input">
                                                </div>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-success w-100" type="submit">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="mt-2 mb-2 text-center">
                                        <p class="mb-0">Already have an account ? <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Forgot Password</a></p>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                    </center>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">
                                Copyrights &copy; <script>
                                    document.write(new Date().getFullYear())
                                </script>. WIKARTA - All Rights Reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}">
    </script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>

    <!-- password-addon init -->
    <script src="{{ asset('assets/js/pages/password-addon.init.js') }}"></script>
</body>

</html>