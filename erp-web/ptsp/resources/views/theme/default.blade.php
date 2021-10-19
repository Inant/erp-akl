<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('theme/assets/images/favicon.png') !!}">
    <title>ERP - PT. Sekar Pamenang</title>
    <!-- Custom CSS -->
    <link href="{!! asset('theme/assets/libs/chartist/dist/chartist.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('theme/assets/extra-libs/c3/c3.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('theme/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') !!}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{!! asset('theme/assets/libs/select2/dist/css/select2.min.css') !!}">
    <link href="{!! asset('theme/assets/libs/toastr/build/toastr.min.css') !!}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{!! asset('public/theme/dist/css/styler.css') !!}" rel="stylesheet">
    @yield('css-content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            display: none; <- Crashes Chrome on hover
            -webkit-appearance: none;
            margin: 0;
        }
        .sidebar-nav ul .nav-small-cap {
            /*background-color:#099a97;*/
        }
    </style>
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        @include('theme.header')
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        @include('theme.sidebar')
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
			@yield('breadcrumb')
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            @yield('content')
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->

            <div id="app">
            </div>

            <footer class="footer text-center">
				All Rights Reserved by Xtreme admin. Designed and Developed by <a href="https://wrappixel.com">WrapPixel</a>.
			</footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->


    @yield('js-content')
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>

    <!-- <script src="{{ mix('js/app.js') }}" type="text/javascript"></script> -->

    <!-- Bootstrap tether Core JavaScript -->
    <script src="{!! asset('theme/assets/libs/popper.js/dist/umd/popper.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/libs/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
    <!-- apps -->
    <script src="{!! asset('theme/dist/js/app.min.js') !!}"></script>
    <script src="{!! asset('theme/dist/js/app.init.js') !!}"></script>
    <script src="{!! asset('theme/dist/js/app-style-switcher.js') !!}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{!! asset('theme/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/extra-libs/sparkline/sparkline.js') !!}"></script>
    <!--Wave Effects -->
    <script src="{!! asset('theme/dist/js/waves.js') !!}"></script>
    <!--Menu sidebar -->
    <script src="{!! asset('theme/dist/js/sidebarmenu.js') !!}"></script>
    <!--Custom JavaScript -->
    <script src="{!! asset('theme/dist/js/custom.min.js') !!}"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <!-- <script src="{!! asset('theme/assets/libs/chartist/dist/chartist.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') !!}"></script> -->
    <!--c3 charts -->
    <!-- <script src="{!! asset('theme/assets/extra-libs/c3/d3.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/extra-libs/c3/c3.min.js') !!}"></script> -->
    <!--chartjs -->
    <!-- <script src="{!! asset('theme/assets/libs/chart.js/dist/Chart.min.js') !!}"></script>
    <script src="{!! asset('theme/dist/js/pages/dashboards/dashboard1.js') !!}"></script> -->
    <!-- datatables -->
    <script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
    <script src="{!! asset('theme/dist/js/pages/datatable/datatable-basic.init.js') !!}"></script>
    <!-- <script src="{!! asset('theme/dist/js/pages/datatable/datatable-api.init.js') !!}"></script> -->
    <!-- select2 -->
    <script src="{!! asset('theme/assets/libs/select2/dist/js/select2.full.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/libs/select2/dist/js/select2.min.js') !!}"></script>
    <script src="{!! asset('theme/dist/js/pages/forms/select2/select2.init.js') !!}"></script>
    <!-- toasr -->
    <script src="{!! asset('theme/dist/js/custom.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/libs/toastr/build/toastr.min.js') !!}"></script>
    <script src="{!! asset('theme/assets/extra-libs/toastr/toastr-init.js') !!}"></script>

    <!-- custom erp -->
    <script src="{!! asset('theme/dist/js/custom-erp.js') !!}"></script>

    <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();


    @if(Session::has('message'))
		var type="{{Session::get('alert-type','info')}}"

        switch(type){
			case 'info':
		         toastr.info("{{ Session::get('message') }}", "Information", { positionClass: 'toastr toast-bottom-right', containerId: 'toast-bottom-right' });
		         break;
            case 'success':
                toastr.success("{{ Session::get('message') }}", "Successful Notification", { positionClass: 'toastr toast-bottom-right', containerId: 'toast-bottom-right' });
	            break;
         	case 'warning':
	            toastr.warning("{{ Session::get('message') }}", "Warning Notification", { positionClass: 'toastr toast-bottom-right', containerId: 'toast-bottom-right' });
	            break;
	        case 'error':
		        toastr.error("{{ Session::get('message') }}", "Error Notification", { positionClass: 'toastr toast-bottom-right', containerId: 'toast-bottom-right' });
		        break;
		}

	@endif


    </script>

</body>

</html>
