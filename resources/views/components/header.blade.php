<meta charset="utf-8" />
<title>PT. Wijaya Karya Arta</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="" name="description" />
<meta content="" name="author" />

<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('favicon.png') }}">

<!-- Layout config Js -->
<script src="{{ asset('assets/js/layout.js') }}"></script>

<!-- multi.js css -->
<link href="{{ asset('assets/libs/multi.js/multi.min.css') }}" rel="stylesheet" type="text/css" />

<!-- autocomplete css -->
<link href="{{ asset('assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}" rel="stylesheet" type="text/css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

<!--datatable css-->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<!--datatable responsive css-->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />

<!-- jsvectormap css -->
<link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

<!--Swiper slider css-->
<link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Bootstrap Css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Icons Css -->
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- App Css-->
<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

<!-- custom Css-->
<link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    .f1-steps {
        overflow: hidden;
        position: relative;
        margin-top: 20px;
    }

    .f1-progress {
        position: absolute;
        top: 24px;
        left: 0;
        width: 100%;
        height: 1px;
        background: #ddd;
    }

    .f1-step {
        position: relative;
        float: left;
        width: 25%;
        padding: 0 5px;
    }

    .f1-step-icon {
        display: inline-block;
        width: 40px;
        height: 40px;
        margin-top: 4px;
        background: #ddd;
        font-size: 16px;
        color: #fff;
        line-height: 40px;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
    }

    .f1-step p {
        color: #ccc;
    }

    .f1 fieldset {
        display: none;
        text-align: left;
    }

    .f1-buttons {
        text-align: right;
    }

    .f1 .input-error {
        border-color: #f35b3f;
    }

    #example tr td {
        padding-top: 5px;
        padding-bottom: 2px;
    }

    .tableRole tr td {
        padding-top: 6px;
        padding-bottom: 6px;
    }

    .tableRole tr th {
        padding-top: 6px;
        padding-bottom: 6px;
    }

    /*#loading-progress {
          position: fixed;
          width: 100%;
          height: 100%;
          font-size: 150px;
          text-align: center;
          vertical-align: middle;
          color: #000000;
          z-index: 999999;
          background-color: #FFFFFF;
          padding-top: 0px;
        }*/

    .loading-progress {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid blue;
        border-right: 16px solid green;
        border-bottom: 16px solid red;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;

        position: fixed;
        /* or absolute */
        top: 40%;
        left: 50%;
        margin-left: -50px;
    }



    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 479px) {
        .nav-link {
            width: 100%;
        }
    }

    @media (max-width: 767px) {}

    @media (min-width: 768px) and (max-width: 979px) {}

    @media (max-width: 979px) {}

    @media (min-width: 2300px) {}
</style>